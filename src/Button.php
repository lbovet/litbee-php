<?php

namespace Litbee\Access;

class Button
{
    private bool $granted = false;
    private int $nonce;
    private string $contentId;

    public function __construct(string $contentId) {
        $this->contentId = $contentId;
        $this->nonce = rand();
        $_SESSION["litbee.nonce"] = $this->nonce;
    }

    public function isGranted() : bool
    {
        return $this->granted;
    }
}
?>