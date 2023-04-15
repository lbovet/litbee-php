<?php

namespace Litbee\Access;

class Protocol {

    private $crypto;

    public function __construct()
    {
        $this->crypto = new Crypto();
    }

    public function createRequest($contentUrl, $nonce, $priceInCents)
    {
        $request = urlencode($contentUrl) . "," . $nonce . "," . $priceInCents;
        return "01" . $this->crypto->publicEncrypt($request);
    }

    public function decodeRequest($encodedRequest, &$contentUrl, &$nonce, &$priceInCents)
    {
        $decrypted = $this->crypto->privateDecrypt(substr($encodedRequest, 2));
        if($decrypted == null) {
            return;
        }
        $fields = explode(",", $decrypted);
        $contentUrl = urldecode($fields[0]);
        $nonce = $fields[1];
        $priceInCents = $fields[2];
    }

    public function createToken($contentUrl, $nonce)
    {
        $token = $contentUrl . "," . $nonce;
        return "01" . $this->crypto->privateEncrypt($token);
    }

    public function checkToken($encodedToken, $contentUrl, $nonce)
    {
        $decrypted = $this->crypto->publicDecrypt(substr($encodedToken, 2));
        if($decrypted == null) {
            return false;
        }
        $fields = explode(",", $decrypted);
        $result = ($contentUrl == $fields[0]) && ($nonce == $fields[1]);
        return $result;
    }
}

?>