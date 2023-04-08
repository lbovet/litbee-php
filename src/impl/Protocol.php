<?php

namespace Litbee\Access;

class Protocol {

    private Crypto $crypto;

    public function __construct()
    {
        $this->crypto = new Crypto();
    }

    public function createRequest(string $contentId, int $nonce, int $priceInCents)
    {
        $request = $contentId . "," . $nonce . "," . $priceInCents;
        return "01" . $this->crypto->encryptRequest($request);
    }

    public function decodeRequest(string $encodedRequest, &$contentId, &$nonce, &$priceInCents)
    {
        $decrypted = $this->crypto->decryptRequest(substr($encodedRequest, 2));
        if($decrypted == null) {
            return;
        }
        $fields = explode(",", $decrypted);
        $contentId = $fields[0];
        $nonce = $fields[1];
        $priceInCents = $fields[2];
    }

    public function createToken(string $contentId, int $nonce)
    {
        $token = $contentId . "," . $nonce;
        return "01" . $this->crypto->encryptToken($token);
    }

    public function checkToken(string $encodedToken, string $contentId, int $nonce) : bool
    {
        $decrypted = $this->crypto->decryptToken(substr($encodedToken, 2));
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