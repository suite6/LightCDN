<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HTTPRequest
 *
 * @author louis-eric
 */

namespace suite6\LightCDN;

class HTTPRequest {

    public $headers;
    public $origin_server;
    private $original_url;
    public $method;
    public $url;
    public $scheme;
    public $path;
    public $query;

    function __construct() {
        $this->original_url = $this->getReferrer();
		
		if(!$this->original_url)
		{
			return false;
		}
		
        $temp_headers = $this->getHeaders();
        foreach ($temp_headers as $key => $value)
            $this->headers[mb_strtolower($key)] = $value;

        $this->method = $this->getRequestMethod();
        $url_parts = parse_url($this->original_url);

        // We ignore the user name, password & fragment as caching the first two would be a
        // security risk and the fragment is useless in a GET


        $this->scheme = (key_exists('scheme', $url_parts)) ? $url_parts['scheme'] : 'http';
        $this->host = (key_exists('host', $url_parts)) ? mb_strtolower($url_parts['host']) : NULL;
        $this->port = (key_exists('port', $url_parts)) ? $url_parts['port'] : 80;
        $this->path = (key_exists('path', $url_parts)) ? $url_parts['path'] : '/';
        $this->query = (key_exists('query', $url_parts)) ? $url_parts['query'] : NULL;
		
		
		
        // This is teh URL we will use for file-matching and the get-back
		// If SSL (https) : do not set default port
		if($this->scheme=='https') {
			$this->url = $this->scheme . '://' . $this->host . $this->path;
		} else {
			 $this->url = $this->scheme . '://' . $this->host . ':' . $this->port . $this->path;
		}
			
       
		
		
		
        if (!($this->hasQuery()))
            $this->url .= '?' . $this->query;
    }

    // Overload this in a child descendent for tests
    public function getReferrer() {
        if (key_exists('HTTP_REFERRER', $_SERVER))
            return $_SERVER['HTTP_REFERRER'];
        else
            return NULL;
    }

    // Overload this in a child descendent for tests
    public function getHeaders() {
        return get_headers($this->original_url, 1);
    }

    // Overload this in a child descendent for tests
    public function getRequestMethod() {
        return mb_strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function hasHeader($header) {
        return key_exists(mb_strtolower($header), $this->headers);
    }

    public function getHeaderValue($header, $default = NULL) {
        if ($this->hasHeader($header))
            return $this->headers[mb_strtolower($header)];
        else
            return $default;
    }

    public function hasQuery() {
        return is_null($this->query);
    }

    public function isGET() {
        return $this->method === 'GET';
    }

    public function isHEAD() {
        return $this->method === 'HEAD';
    }

}

?>
