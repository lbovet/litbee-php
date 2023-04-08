<?php

namespace Litbee\Access;

const ACCESS_GATE_URL = "https://litbee.ch/access";
const CONTENT_ID_KEY = "litbee.contentId";
const NONCE_KEY = "litbee.nonce";
const REQUEST_PARAM_NAME = "r";
const TOKEN_PARAM_NAME = "litbee";

class Content
{
    private Session $session;
    private Protocol $protocol;
    private Context $context;

    private string $contentId;
    private int $priceInCents;

    private int $nonce;
    private bool $granted = false;
    private bool $styleInserted = false;

    public function __construct(int $priceInCents = 0, Context $context = null, Session $session = null)
    {
        $this->priceInCents = $priceInCents;
        $this->protocol = new Protocol();

        if($context != null) {
            $this->context = $context;
        } else {
            $context = new Context(TOKEN_PARAM_NAME);
        }
        $this->contentId = sha1($context->contentId());

        // manage the nonce to restrict access only to the current user.
        if($session != null) {
            $this->session = $session;
        } else {
            $this->session = new Session();
        }
        if ( $this->session->has(CONTENT_ID_KEY) && $this->session->getItem(CONTENT_ID_KEY) == $this->contentId) {
            $this->nonce = $this->session->getItem(NONCE_KEY);
        } else {
            // generate a new nonce if new or changed page
            $this->nonce = $this->context->nonce();
            $this->session->setItem(CONTENT_ID_KEY, $this->contentId);
            $this->session->setItem(NONCE_KEY, $this->nonce);
        }

        // check the token if present and grant access if valid
        $token = $context->token();
        if($token != null) {
            $this->granted = $this->protocol->checkToken($token, $this->contentId, $this->nonce);
        }
    }

    public function accessGateUrl()
    {
        $param = $this->protocol->createRequest($this->contentId, $this->nonce, $this->priceInCents);
        return ACCESS_GATE_URL . "?" . REQUEST_PARAM_NAME . "=" . $param;
    }

    public function renderButton()
    {
        ob_start();
        if(!$this->styleInserted) {
            include "impl/view/style.php";
            $this->styleInserted = true;
        }
        $priceTag = str_replace("0.", "-.", sprintf("%.2f", $this->priceInCents / 100.0));
        $accessGateUrl = $this->accessGateUrl();
        include "impl/view/button.php";
        return ob_get_clean();
    }

    public function renderIcon()
    {
        ob_start();
        if(!$this->styleInserted) {
            include "impl/view/style.php";
            $this->styleInserted = true;
        }
        include "impl/view/icon.php";
        return ob_get_clean();
    }

    public function accessGranted() : bool
    {
        return $this->granted;
    }
}
?>