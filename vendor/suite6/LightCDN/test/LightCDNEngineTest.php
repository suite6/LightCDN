<?php

namespace suite6\LightCDN;

require 'vendor/suite6/LightCDN/LightCDNEngine.php';
require 'vendor/suite6/LightCDN/HTTPRequest.php';
require 'bootstrap.php';

class LightCDNEngineTest extends \PHPUnit_Framework_TestCase {

    private $operation;

    public function setup() {
        $request = $this->prePareRequest();
        $this->operation = new LightCDNEngine($request);
    }

    public function testSave() {
        $value = $this->operation->save();
        $this->assertNotNull($value);
    }

    public function testServe() {
        $value = $this->operation->serve();
        $this->assertNotNull($value);
    }

    public function testValidate() {
        $value = $this->operation->validate();
        $this->assertNotNull($value);
    }

    public function testCleanUp() {
        $value = $this->operation->clean_up();
        $this->assertTrue($value);
    }

    public function prePareRequest() {

        $request = (object) array(
                    'headers' => array(
                        '0' => 'HTTP/1.1 200 OK',
                        'expires' => 'Tue, 21 May 2013 05:05:35 GMT',
                        'date' => ' Fri, 22 Mar 2013 05:05:35 GMT',
                        'server' => 'nginx',
                        'content-type' => 'image/png',
                        'content-length' => '992',
                        'last-modified' => 'Tue, 30 Nov 2010 05:27:30 GMT',
                        'cache-control' => 'max-age=5184000',
                        'via' => '192.168.51.240',
                        'x_cache' => 'HIT from localhost.localdomain',
                        'accept-ranges' => 'bytes',
                        'age' => '1',
                        'x-via' => '1.1 gdzj27:8080 (Cdn Cache Server V2.0), 1.1 lydx153:9080 (Cdn Cache Server V2.0)',
                        'connection' => 'close',
                    ),
                    'origin_server' => '',
                    'original_url' => 'http://img3.cache.netease.com/www/logo/logo_png.png',
                    'method' => 'GET',
                    'url' => 'http://img3.cache.netease.com:80/www/logo/logo_png.png',
                    'scheme' => 'http',
                    'path' => '/www/logo/logo_png.png',
                    'query' => '',
                    'host' => 'img3.cache.netease.com',
                    'port' => '80'
        );
        return $request;
    }

}