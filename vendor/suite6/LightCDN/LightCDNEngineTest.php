<?php
namespace suite6\LightCDN;

@include_once "PHPUnit/Autoload.php";

/*
require 'vendor/suite6/LightCDN/LightCDNEngine.php';
require 'vendor/suite6/LightCDN/HTTPRequest.php';
require 'bootstrap.php';
*/

class LightCDNEngineTest extends \PHPUnit_Framework_TestCase
{
    
    private $operation;
    
    public function setup()
    {
        $request         = $this->prePareRequest();
        $this->operation = new LightCDNEngine($request);
    }
    
    public function testGetAsset()
    {
        $value = $this->operation->getAsset();
        $this->assertNotNull($value);
    }
    
    public function testGetServeAsset()
    {
        $value = $this->operation->getServeAsset();
        $this->assertNotNull($value);
    }
    
    public function testSave()
    {
        $value = $this->operation->save();
        $this->assertNotNull($value);
    }
    
    public function testServe()
    {
        $value = $this->operation->serve();
        $this->assertNotNull($value);
    }
    
    public function testValidate()
    {
        $value = $this->operation->validate();
        $this->assertNotNull($value);
    }
    
    public function testCleanUp()
    {
        $value = $this->operation->clean_up();
        $this->assertTrue($value);
    }
    
    public function prePareRequest()
    {
        
        $request = (object) array(
            'headers' => array(
                '0' => 'HTTP/1.1 200 OK',
                'expires' => 'Mon, 24 Jun 2013 15:45:46 GMT',
                'date' => 'Fri, 14 Jun 2013 08:29:18 GMT',
                'server' => 'nginx',
                'content-type' => 'image/png',
                'content-length' => '992',
                #'last-modified' => 'Mon, 25 Mar 2013 19:02:14 GMT', #1
                #'etag' 			=> '2000000055ace-358e-4df1c7ccaf99e1', #2
                
                #'cache-control' => 'max-age=5184000', #3
                #'cache-control' => 'no-cache', #4
                #'cache-control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0', # 5
                #'cache-control' => 'private, max-age=31536000', #6
                #'cache-control' => 'public, max-age=38', #7
                
                #'pragma'		=> 'max-age=5184000', #8
                #'pragma'		=> 'no-cache', #9
                #'pragma'		=> 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0',  #10
                
                'via' => '192.168.51.240',
                'x_cache' => 'HIT from localhost.localdomain',
                'accept-ranges' => 'bytes',
                'age' => '1',
                'x-via' => '1.1 gdzj27:8080 (Cdn Cache Server V2.0), 1.1 lydx153:9080 (Cdn Cache Server V2.0)',
                'connection' => 'close',
                
                'vary' => 'test',
                'content-language' => 'en',
                'accept-encoding' => 'text/html'
                
            ),
            'origin_server' => '',
            'original_url' => 'http://www.google.com/images/srpr/logo4w.png',
            'method' => 'GET',
            'url' => 'http://www.google.com:80/images/srpr/logo4w.png',
            'scheme' => 'http',
            'path' => '/images/srpr/logo4w.png',
            'query' => '',
            'host' => 'www.google.com',
            'port' => '80'
        );
        return $request;
    }
    
}