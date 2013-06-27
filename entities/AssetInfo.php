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
     * @var longtext
     * */
    protected $asset_headers;

    /**
     * @deleted @Column(type="boolean")
     * @var tinyint
     * */
    protected $deleted = 0;
	
	
	 /**
     * @first_served @Column(type="datetime")
     * @var timestamp
     * */
    protected $first_served;


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
	
	/**
     * @content_length @Column(type="text")
     * @var text
     * */
    protected $content_length;
	
	/**
     * @vary @Column(type="text")
     * @var text
     * */
    protected $vary;
	
	/**
     * @last_modified @Column(type="text")
     * @var text
     * */
    protected $last_modified;
	
	/**
     * @etag @Column(type="text")
     * @var text
     * */
    protected $etag;
	
	/**
     * @content_language @Column(type="text")
     * @var text
     * */
    protected $content_language;
	
	/**
     * @accept_encoding @Column(type="text")
     * @var text
     * */
    protected $accept_encoding;
	
	/**
     * @expires @Column(type="text")
     * @var text
     * */
    protected $expires;
	

    public function getName() {
        return $this->file_name;
    }

    public function setName($file_name) {
        $this->file_name = $file_name;
    }
	
	public function getAssetHeader() {
        return $this->asset_headers;
    }

    public function setAssetHeader($asset_headers) {
        $this->asset_headers = $asset_headers;
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
	
	public function setContentLength($content_length) {
        $this->content_length = $content_length;
    }

    public function getContentLength() {
        return $this->content_length;
    }
	
	public function setVary($vary) {
        $this->vary = $vary;
    }

    public function getVary() {
        return $this->vary;
    }
	

	public function setLastModified($last_modified) {
        $this->last_modified = $last_modified;
    }

    public function getLastModified() {
        return $this->last_modified;
    }
	
	public function setEtag($etag) {
        $this->etag = $etag;
    }

    public function getEtag() {
        return $this->etag;
    }
	
	public function setContentLanguage($content_language) {
        $this->content_language = $content_language;
    }

    public function getContentLanguage() {
        return $this->content_language;
    }
	
	public function setAcceptEncoding($accept_encoding) {
        $this->accept_encoding = $accept_encoding;
    }

    public function getAcceptEncoding() {
        return $this->accept_encoding;
    }
	
	public function setExpires($expires) {

		
        $this->expires = $expires;
    }

    public function getExpires() {
        return $this->expires;
    }
	
	public function setFirstServed($first_served) {
        $this->first_served = $first_served;
    }
	
	public function getFirstServed() {
        return $this->first_served;
    }

    public function setLastServed($last_served) {
        $this->last_served = $last_served;
    }

    public function getLastServed() {
        return $this->last_served;
    }

}