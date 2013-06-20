<?php
namespace suite6\LightCDN;

use Entities\AssetInfo;

class LightCDNEngine
{
    
    private $request_client_to_cache;
    private $allowed_servers;
    
    public function __construct($request = null)
    {
        global $settings;
        if (!is_null($request))
            $this->request_client_to_cache = $request;
        $this->allowed_servers = $settings['allowed servers'];
    }
    
    public function getBaseDataPath()
    {
        # Directory path is already define in bootstrap
        global $dir_path;
        return $dir_path . '/';
        
    }
    
    public function getFileNameFromReferrer()
    {
        @$ext = end(explode('.', $this->request_client_to_cache->url));
        return md5($this->request_client_to_cache->url) . '.' . $ext;
        
    }
    
    public function getFilePathFromReferrer()
    {
        return $this->getBaseDataPath() . $this->getFileNameFromReferrer();
    }
    
    public function getFilePathFromURL()
    {
        return $this->getBaseDataPath() . $this->getFileNameFromReferrer();
    }
    
    // Check reguested server is valid or not
    public function isToAllowedServer()
    {
        return (in_array($this->request_client_to_cache->host, $this->allowed_servers));
    }
    
    public function execute()
    {
        # Execute code
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->request_client_to_cache->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $return   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); # get http code
        curl_close($ch);
        
        # Check if httpCode is OK
        if ($httpCode == 200) {
            
            # extract server header and content
            list($curl_header, $content) = explode("\r\n\r\n", $return, 2);
            
            $headers = array();
            $data    = explode("\n", $curl_header);
            
            array_shift($data);
            
            # create array for server request header
            foreach ($data as $part) {
                $pos                             = strpos($part, ':');
                $key                             = substr($part, 0, $pos);
                $value                           = substr($part, $pos + 1, strlen($part));
                $headers[strtolower(trim($key))] = trim($value);
            }
            
            # create array for return the values: Dont need HttpCode, just headers and content is enough
            $request_server_to_cache['headers'] = $headers;
            $request_server_to_cache['content'] = $content;
            
            # Return Server Data
            return $request_server_to_cache;
        } else {
            return false;
        }
        
    }
    
    public function extractAsArray($string)
    {
        $inputString = trim($string);
        if (!$inputString) {
            return false;
        }
        
        $explode_inputString = explode(',', $inputString);
        $explode_inputString = array_map('trim', $explode_inputString);
        
        $outputArray = array();
        if ($explode_inputString) {
            foreach ($explode_inputString as $key => $value) {
                $explode_value   = explode("=", $value);
                $k               = trim($explode_value[0]);
                $v               = isset($explode_value[1]) && !empty($explode_value[1]) ? trim($explode_value[1]) : 0;
                $outputArray[$k] = $v;
            }
        }
        return $outputArray;
    }
    
    
    public function getAsset()
    {
        global $tackler_config;
        
        if (($this->request_client_to_cache->method === 'GET' OR $this->request_client_to_cache->method === 'HEAD') AND $this->isToAllowedServer()) {
            return $this->getServeAsset();
        } else {
            header("Location: " . $tackler_config->get_default_403_handler());
        }
    }
    
    public function getServeAsset()
    {
        global $entityManager, $tackler_config;
        
        if (file_exists($this->getFilePathFromReferrer()) AND $this->validate()) {
            $this->serve();
        } elseif ($this->save()) {
            $this->serve();
        } else {
            header("Location: " . $tackler_config->get_default_404_handler());
        }
    }
    
    
    
    // Save asset or update
    public function save()
    {
        
        global $entityManager;
        $file_name = $this->getFilePathFromURL();
        
        # Execute request to get data from server
        $request_server_to_cache = $this->execute();
        
        
        if ($request_server_to_cache) {
            
            // if file exist refresh
            if (file_exists($file_name))
                @unlink($file_name);
            
            
            //If write fali return null  (redirect 404)
            if (file_put_contents($file_name, $request_server_to_cache['content'])) {
                
                # some servers will not return a content-length so read filesize directly
                if (!$request_server_to_cache['headers']['content-length']) {
                    $request_server_to_cache['headers']['content-length'] = filesize($file_name);
                }
                
                # Strip Via from server header
                if (isset($request_server_to_cache['headers']['Via'])) {
                    unset($request_server_to_cache['headers']['Via']);
                }
                
                $request_server_to_cache_serialize = serialize($request_server_to_cache['headers']);
                
                $exist = false;
                $exist = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
                    'original_url' => filter($this->request_client_to_cache->url),
                    'deleted' => '0'
                ));
                
                $servedTime = new \DateTime('NOW');
                
                if ($exist == false) {
                    $assets_info = new AssetInfo();
                    $assets_info->setHeader($request_server_to_cache_serialize);
                    $assets_info->setFirstServed($servedTime);
                    $assets_info->setLastServed($servedTime);
                    $assets_info->setName(filter($this->getFileNameFromReferrer()));
                    $assets_info->setSize(filter($request_server_to_cache['headers']['content-length']));
                    $assets_info->setOriginalUrl(filter($this->request_client_to_cache->url));
                    $assets_info->setMimeType(filter($request_server_to_cache['headers']['content-type']));
                    
                    #save entity to db
                    $entityManager->persist($assets_info);
                    $entityManager->flush();
                } else {
                    
                    # Update entity to db
                    $exist->setHeader($request_server_to_cache_serialize);
                    $exist->setFirstServed($servedTime);
                    $entityManager->flush();
                }
                
                return true;
            } else {
                return false;
            }
        }
        
        return false;
    }
    
    // Read the record from the database and return
    public function serve()
    {
        ob_start();
        global $entityManager, $connection;
        $return = array();
        
        //update last served;
        $assets_info = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
            'original_url' => filter($this->request_client_to_cache->url)
        ));
        
        
        if ($assets_info) {
            $assets_info->setLastServed(new \DateTime('NOW'));
        }

        $entityManager->flush();
        ignore_user_abort(false);
        
        $filename = $this->getFilePathFromURL();
        if (file_exists($filename)) {
            // For PHPunit testing return true becuase  in PHPUnit test header already set that's reason create error cannot modify header information 
            // - header already set . For PHPunit check header already set if set return true.
            if (headers_sent())
                return true;
            
            
            // Build header here, return header
            $assets_header = unserialize($assets_info->getHeader());
            
            
            if (isset($assets_header['content-type']))
                header('Content-Type: ' . $assets_header['content-type']);
            
            if (isset($assets_header['content-length']))
                header('Content-Length: ' . $assets_header['content-length']);
            
            if (isset($assets_header['via']))
                header('Via: ' . $_SERVER['HTTP_HOST']);
            
            if (isset($assets_header['vary']))
                header('Vary: ' . $assets_header['vary']);
            
            if (isset($assets_header['last-modified']))
                header('Last-Modified: ' . $assets_header['last-modified']);
            
            if (isset($assets_header['etag']))
                header('ETag: ' . $assets_header['etag']);
            
            if (isset($assets_header['content-language']))
                header('Content-Language: ' . $assets_header['content-language']);
            
            if (isset($assets_header['accept-encoding']))
                header('Accept-Encoding: ' . $assets_header['accept-encoding']);
            
            if (isset($assets_header['expires']))
                header('Expires: ' . $assets_header['expires']);
            
            
            # Add current GMT time
            header('date: ' . gmdate("D, M d Y H:i:s") . ' GMT');
            
            #print_r();
            #exit;
            
            if ($this->request_client_to_cache->method === 'GET' && file_exists($filename)) {
                readfile($filename);
            }
        } else
            return null;
    }
    
    // Locate the record from the database by original URL and deleted = FALSE
    public function validate()
    {
        
        global $entityManager;
        
        # Fetch data from database
        $assets_info = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
            'original_url' => filter($this->request_client_to_cache->url),
            'deleted' => '0'
        ));
        
        # If data is aviable then unserialize header to get array for further operations
        if ($assets_info) {
            $assets_header      = unserialize($assets_info->getHeader());
            $assets_firstServed = (array) $assets_info->getFirstServed();
        }
		
        
        
        /**
         * If last-modified available with client header
         * The Last-Modified entity-header field indicates the date and time at which the origin server believes the variant was last modified.
         **/
        if (isset($this->request_client_to_cache->headers['last-modified'])) {
            if (!isset($assets_header['last-modified']) OR strtotime($assets_header['last-modified']) != strtotime($this->request_client_to_cache->headers['last-modified'])) {
                return false;
            } else {
                return true;
            }
        }
        
        /**
         * An ETag is an opaque identifier assigned by a web server to a specific version of a resource found at a URL.
         * If the resource content at that URL ever changes, a new and different ETag is assigned. 
         * Used in this manner ETags are similar to fingerprints, and they can be quickly compared to determine if two versions of a resource are the same or not.
         **/
        if (isset($this->request_client_to_cache->headers['etag'])) {
            if (!isset($assets_header['etag']) OR strtotime($assets_header['etag']) != strtotime($this->request_client_to_cache->headers['etag'])) {
                return false;
            } else {
                return true;
            }
        }
        
        
        /**
         *    Pragma is the HTTP/1.0 implementation and cache-control is the HTTP/1.1 implementation of the same concept.
         *    They both are meant to prevent the client from caching the response. Older clients may not support HTTP/1.1 which is why that header is still in use.
         **/
        if (isset($this->request_client_to_cache->headers['cache-control']) || isset($this->request_client_to_cache->headers['pragma'])) {
            
            if ($this->request_client_to_cache->headers['cache-control']) {
                $clientCacheControlArray = $this->extractAsArray($this->request_client_to_cache->headers['cache-control']);
            } else {
                # Reuse same code for pragma bcas it is same alike cache-control but just for HTTP/1.0
                $clientCacheControlArray = $this->extractAsArray($this->request_client_to_cache->headers['pragma']);
            }
            
            
            # check if anyone of these headers are found (As per their priority)
            $search = array(
                'no-cache',
                'no-store',
                'max-age'
            );
            
            foreach ($search as $key => $value) {
                # If the client is making a request and asks that it not be cached, then we should issue a 302 redirect to the asset URL.
                if (array_key_exists($value, $clientCacheControlArray) AND $clientCacheControlArray[$value] == 0) {
                    header('Location:' . $this->request_client_to_cache->url);
                    die();
                }
            }
            
            
            # check if max-age and > 0
            if (isset($clientCacheControlArray['max-age']) and $clientCacheControlArray['max-age'] > 0 AND isset($assets_firstServed)) {
                # If yes, then go ahead and compare expire_time with current_time
                
                # if First served is not available then no need to proceed further
                if (!$assets_firstServed) {
                    return false;
                }
                
                
                $expire_time  = date('Y-m-d H:i:s', strtotime($assets_firstServed['date'] . " +" . $clientCacheControlArray['max-age'] . " Seconds"));
                $current_time = date('Y-m-d H:i:s');
                
                # TRUE if time isnt expired else default FALSE
                if ($expire_time >= $current_time) {
                    return true;
                }
                
            }
        }
        
        # Expires depends on accuracy of user's clock, so it's mostly a bad choice (as most browsers support HTTP/1.1). 
        # So we did check max-age before Expires
        if (isset($this->request_client_to_cache->headers['expires'])) {
            $expires_time = strtotime($this->request_client_to_cache->headers['expires']);
            $current_time = strtotime(gmdate('D, d M Y H:i:s', time()) . ' GMT');
            
            # TRUE if time isnt expired else default FALSE
            if ($expires_time >= $current_time) {
                return true;
            }
        }
        
        
        #  If the no-cache is specified OR expires is 0, then should not occupy space on disk for the file, So unlink file.
        /*if (isset($this->request_client_to_cache->headers['cache-control']) && (stristr($this->request_client_to_cache->headers['cache-control'], 'no-cache') OR stristr($this->request_client_to_cache->headers['cache-control'], 'max-age=0'))) {
        header('Location:'.$this->request_client_to_cache->url);
        } else if (isset($this->request_client_to_cache->headers['pragma']) && stristr($this->request_client_to_cache->headers['pragma'], 'no-cache')) {
        header('Location:'.$this->request_client_to_cache->url);
        } else if (isset($this->request_client_to_cache->headers['expires']) && $this->request_client_to_cache->headers['expires'] == 0) {
        header('Location:'.$this->request_client_to_cache->url);
        }*/
        
        
        /*
        if (isset($this->request_client_to_cache->headers['cache-control']) && !stristr($this->request_client_to_cache->headers['cache-control'], 'no-cache') && !stristr($this->request_client_to_cache->headers['cache-control'], 'max-age=0')) {
        return true;
        } else if (isset($this->request_client_to_cache->headers['pragma']) && !stristr($this->request_client_to_cache->headers['pragma'], 'no-cache')) {
        return true;
        } else if (isset($this->request_client_to_cache->headers['expires']) && $this->request_client_to_cache->headers['expires'] != 0) {
        return true;
        } else {
        return false;
        }
        */
        
        
    }
    
    
    // if memory exceeded update deleted = 1 in DB
    public function clean_up()
    {
        global $settings, $dir_path, $entityManager;
        //Total size in bytes
        $toatl_disk_size = disk_total_space("/");
        //For window
        //$toatl_disk_size = disk_total_space("H:");
        //Total size of data directory in bytes
        $total_size_data = ($toatl_disk_size * $settings['setting']['max space']) / 100;
        //if size downloded greater than total size return false
        //if($argument_count == 0)
        // Total occupied size of data folder        
        $total_occupied  = $this->getDirectorySize($dir_path);
        
        $total_occupied = $total_occupied;
        
        while ($total_occupied >= $total_size_data) {
            $assets_info = array();
            $assets_info = $entityManager->getRepository('Entities\AssetInfo')->getSingleReord();
            
            if ($assets_info) {
                foreach ($assets_info as $record) {
                    $update         = $entityManager->getRepository('Entities\AssetInfo')->updateReord($record['id']);
                    $total_occupied = $total_occupied - $record['file_size'];
                }
            }
        }
        // Remove assest(s) fron Data directory
        $this->remove_assets();
        return true;
    }
    
    // if memory exceeded remove asset(s) from data directory
    public function remove_assets()
    {
        global $entityManager, $argv;
        // For run cron pass 2 int arguments arguments  e.g (5,2)
        // if run from command line
        $where = "";
        $limit = "LIMIT 1";
        if (isset($argv) && count($argv) > 2) {
            $where = "AND mod(id," . $argv[1] . ")=" . $argv[2] . "";
            $limit = "";
        }
        $assets_data = array();
        $assets_data = $entityManager->getRepository('Entities\AssetInfo')->getDeletedReord($where, $limit);
        
        
        foreach ($assets_data as $record) {
            @unlink($this->getBaseDataPath() . $record['file_name']);
        }
    }
    
    // get directory size
    public function getDirectorySize($directory)
    {
        $dirSize = 0;
        
        if (!$dh = opendir($directory))
            return false;
        
        while ($file = readdir($dh)) {
            if ($file == "." || $file == "..")
                continue;
            
            if (is_file($directory . "/" . $file))
                $dirSize += filesize($directory . "/" . $file);
            
            if (is_dir($directory . "/" . $file))
                $dirSize += $this->getDirectorySize($directory . "/" . $file);
        }
        
        closedir($dh);
        
        return $dirSize;
    }
    
}
