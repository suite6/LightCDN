<?php
namespace suite6\LightCDN;

use Entities\AssetInfo;

class LightCDNClientRequest
{
	private $request_client_to_cache;
	private $httpCode;
	private $serverData;
	
	public function __construct($request=null)
	{
		global $settings;
        if (!is_null($request))
            $this->request_client_to_cache = $request;
        
	}
	
	# execute request_client_to_cache and fetch data from server
	public function execute()
    {
        # Execute
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->request_client_to_cache->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $return   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); # get http code
        curl_close($ch);
		
		$this->httpCode = $httpCode;
		$this->serverData = $return;
	}
	
	# Return httpCode
	public function getHttpCode()
    {
		return $this->httpCode;
	}
	
	# Return serverData
	public function getServerData()
    {
		return $this->serverData;
	}
	
		
}