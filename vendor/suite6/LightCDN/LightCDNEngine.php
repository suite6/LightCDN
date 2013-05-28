<?php

namespace suite6\LightCDN;

use Entities\AssetInfo;

class LightCDNEngine
{
    
    private $client_request;
    private $allowed_servers;
    private $header;
    
    public function __construct($request = null)
    {
        global $settings;
        if (!is_null($request))
            $this->client_request = $request;
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
        @$ext = end(explode('.', $this->client_request->url));
        return md5($this->client_request->url) . '.' . $ext;
     
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
        return (in_array($this->client_request->host, $this->allowed_servers));
    }
    
    public function getAsset()
    {
        global $tackler_config;
        
        if (($this->client_request->isGET() OR $this->client_request->isHEAD()) AND $this->isToAllowedServer()) {
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
        
        $return_data = array();
        $new_header  = array();
        $ch          = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->client_request->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $return   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
        curl_close($ch);
        list($curl_header, $asset) = explode("\r\n\r\n", $return, 2);
        
       
		
        if ($httpCode == 200) {
            // if file exist refresh
            if (file_exists($file_name))
                @unlink($file_name);
            
            
            //If write fali return null  (redirect 404)
            if (file_put_contents($file_name, $asset)) {
                
                $this->header = serialize($this->client_request->headers);
                
                $exist = false;
                $exist = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
                    'original_url' => filter($this->client_request->url),
                    'deleted' => '0'
                ));
                if ($exist == false) {
                    $assets_info = new AssetInfo();
                    $assets_info->setHeader($this->header);
                    $assets_info->setLastServed(new \DateTime('NOW'));
                    $assets_info->setName(filter($this->getFileNameFromReferrer()));
                    $assets_info->setSize(filter($this->client_request->headers['content-length']));
                    $assets_info->setOriginalUrl(filter($this->client_request->url));
                    $assets_info->setMimeType(filter($this->client_request->headers['content-type']));
                    //save entity to db
                    $entityManager->persist($assets_info);
                    $entityManager->flush();
                } else {
                    $exist->setHeader($this->header);
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
        $return      = array();
        //update last served;
        $assets_info = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
            'original_url' => filter($this->client_request->url)
        ));
        
        #db($assets_info);
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
            $headers = unserialize($assets_info->getHeader());
            
            foreach ($this->client_request->headers as $key => $value) {
                if ($key) {
                    header($key . ': ' . $value);
                }
            }
            
            if (($this->client_request->isGET()) && file_exists($filename)) {
                readfile($filename);
            }
        } else
            return null;
    }
    
    // Locate the record from the database by original URL and deleted = FALSE
    public function validate()
    {
        global $entityManager;
        
        $assets_info = $entityManager->getRepository('Entities\AssetInfo')->findOneBy(array(
            'original_url' => filter($this->client_request->url),
            'deleted' => '0'
        ));
        
        if ($assets_info) {
            $header = unserialize($assets_info->getHeader());
        }
        
        
        if ($this->client_request->headers['last-modified']) {
            if (!isset($header) OR strtotime($header['last-modified']) != strtotime($this->client_request->headers['last-modified'])) {
                return false;
            } else {
                return true;
            }
        } else if (isset($this->client_request->headers['cache-control']) & !stristr($this->client_request->headers['cache-control'], 'no-cache') & !stristr($this->client_request->headers['cache-control'], 'max-age=0')) {
            return true;
        } else if (isset($this->client_request->headers['pragma']) & !stristr($this->client_request->headers['pragma'], 'no-cache')) {
            return true;
        } else if (isset($this->client_request->headers['expires']) & $this->client_request->headers['expires']!=0)  {
			return true;
		} else {
            return false;
        }
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