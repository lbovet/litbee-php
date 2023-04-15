<?php

namespace Litbee\Access;

class Protocol {

    private $crypto;

    public function __construct()
    {
        $this->crypto = new Crypto();
    }

    public function createRequest($contentId, $nonce, $priceInCents)
    {
        $request = $contentId . "," . $nonce . "," . $priceInCents;
        return "01" . $this->crypto->publicEncrypt($request);
    }

    public function decodeRequest($encodedRequest, &$contentId, &$nonce, &$priceInCents)
    {
        $decrypted = $this->crypto->privateDecrypt(substr($encodedRequest, 2));
        if($decrypted == null) {
            return;
        }
        $fields = explode(",", $decrypted);
        $contentId = $fields[0];
        $nonce = $fields[1];
        $priceInCents = $fields[2];
    }

    public function createToken($contentId, $nonce)
    {
        $token = $contentId . "," . $nonce;
        return "01" . $this->crypto->privateEncrypt($token);
    }

    public function checkToken($encodedToken, $contentId, $nonce)
    {
        $decrypted = $this->crypto->publicDecrypt(substr($encodedToken, 2));
        if($decrypted == null) {
            return false;
        }
        $fields = explode(",", $decrypted);
        $result = true;
        $result &= ($contentId == $fields[0]);
        $result &= ($nonce == $fields[1]);
        return $result;
    }
}

?>