<?php

namespace Litbee\Access;

class Context
{
    private $paramName;

    public function __construct($paramName) {
        $this->paramName = $paramName;
    }

    public function contentUrl() {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain =  $_SERVER['HTTP_HOST'];
        $resource = strtok($_SERVER["REQUEST_URI"], "?");
        return $protocol . $domain . $resource;
    }

    public function token() {
        if( isset($_GET[$this->paramName])) {
            return $_GET[$this->paramName];
        } else {
            return null;
        }
    }

    public function nonce() : int {
        return rand();
    }
}

?>