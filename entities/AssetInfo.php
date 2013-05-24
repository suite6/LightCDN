<?php

use Doctrine\Common\Collections\ArrayCollection;

// AssetInfo.php

namespace Entities;

/**
 * @Entity @Table(name="assets_info")
 * @entity(repositoryClass="Repository\AssetInfoRepository")
 * */
class AssetInfo {

    public function __construct() {
        
    }

    /**
     * @Id
     * @Column(type="integer")
     * @generatedValue(strategy="IDENTITY")
     */
    protected $id = null;

    /**
     * @header @Column(type="text")
     * @var blob
     * */
    protected $header;

    /**
     * @deleted @Column(type="boolean")
     * @var tinyint
     * */
    protected $deleted = 0;

    /**
     * @last_served @Column(type="datetime")
     * @var timestamp
     * */
    protected $last_served;

    /**
     * @file_size @Column(type="bigint")
     * @var bigint
     * */
    protected $file_size;

    /**
     * @file_name @Column(type="string")
     * @var varchar
     * */
    protected $file_name;

    /**
     * @original_url @Column(type="string")
     * @var varchar
     * */
    protected $original_url;

    /**
     * @mime_type @Column(type="text")
     * @var text
     * */
    protected $mime_type;

    public function getName() {
        return $this->file_name;
    }

    public function setName($file_name) {
        $this->file_name = $file_name;
    }

    public function getHeader() {
        return $this->header;
    }

    public function setHeader($header) {
        $this->header = $header;
    }

    public function setSize($file_size) {
        $this->file_size = $file_size;
    }

    public function getSize() {
        return $this->file_size;
    }

    public function setOriginalUrl($original_url) {
        $this->original_url = $original_url;
    }

    public function getOriginalUrl() {
        return $this->original_url;
    }

    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    public function getMimeType() {
        return $this->mime_type;
    }

    public function setLastServed($last_served) {
        $this->last_served = $last_served;
    }

    public function getLastServed() {
        return $this->last_served;
    }

}