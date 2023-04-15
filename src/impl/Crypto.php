<?php

namespace Litbee\Access;

class Crypto {

    public function publicEncrypt($request) {
        openssl_public_encrypt($request, $encrypted, $this->publicKey());
        return base64_encode($encrypted);
    }

    public function privateDecrypt($encryptedRequest)
    {
        openssl_private_decrypt(base64_decode($encryptedRequest), $decrypted, $this->privateKey());
        return $decrypted;
    }

    public function privateEncrypt($token)
    {
        openssl_private_encrypt($token, $encrypted, $this->privateKey());
        return base64_encode($encrypted);
    }

    public function publicDecrypt($encryptedToken)
    {
        openssl_public_decrypt(base64_decode($encryptedToken), $decrypted, $this->publicKey());
        return $decrypted;
    }


    private function publicKey() {
        return "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvWENCJgeb2BMbzQPnjW4
CRCRel12p2ZOb6SXTFby4AcKpQ8kIC9BlXDfOq/OvQ2him4oZza87+xjS6wSc7Rp
gGbhec033otuyiZ2ou97FqDYbOc3rQlR/a+c5cut3Sw8BhlfZR/9U9qvwC/nHOkW
S+3objCaLn9My10dc93f6Nx4fotCtzPY2L5qC4Bt4uAu05WPr3M/m8tKW1/vCnoL
YqsuHyesdQxZaTpPULMrSXCSkXUMTp7cJ43s+Nc+BAfHd3LbkTkYZCe8MeL3ZJ6Z
qIo9IL1pbY49Gjv+M3imaGIUpYMXFll1GhtNnTM2yG+YjBpTSgG+25fjKadyU1Ii
OQIDAQAB
-----END PUBLIC KEY-----
";
    }

    private function privateKey()
    {
        if(getenv("LITBEE_PRIVATE_KEY")) {
            $file = getenv("LITBEE_PRIVATE_KEY");
        } else {
            $file = __DIR__ . "/access.priv";
        }
        return openssl_pkey_get_private(file_get_contents($file), getenv("LITBEE_KEY_PWD"));
    }
}
