<?php

namespace Litbee\Access;

class Context
{
    private string $paramName;

    public function __construct(string $paramName) {
        $this->paramName = $paramName;
    }

    public function contentId() {
        return strtok($_SERVER["REQUEST_URI"], "?");
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