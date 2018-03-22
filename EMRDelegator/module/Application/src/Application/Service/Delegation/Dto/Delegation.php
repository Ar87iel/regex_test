<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 4/9/13 10:34 AM
 */

namespace Application\Service\Delegation\Dto;


use Zend\Http\Header\Cookie;

class Delegation {
    /** @var string */
    protected $url;
    /** @var Cookie */
    protected $cookie;

    /**
     * @param \Zend\Http\Header\Cookie $cookie
     * @return Delegation
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
        return $this;
    }

    /**
     * @return \Zend\Http\Header\Cookie
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @param string $url
     * @return Delegation
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


}