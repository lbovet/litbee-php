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
            $this->context = new Context(TOKEN_PARAM_NAME);
        }
        $this->contentId = sha1($this->context->contentId());

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
        $token = $this->context->token();
        if($token != null) {
            $this->granted = $this->protocol->checkToken($token, $this->contentId, $this->nonce);
        }
    }

    private function accessGateBaseUrl()
    {
        $gate = getenv("LITBEE_ACCESS_GATE");
        if(!$gate) {
            $gate = ACCESS_GATE_URL;
            $env = getenv("LITBEE_ENV");
            if($env) {
                $gate = str_replace("://", "://" . $env . ".", $gate);
            }
        }
        return $gate;
    }

    public function accessGateUrl()
    {
        $param = $this->protocol->createRequest($this->contentId, $this->nonce, $this->priceInCents);
        return $this->accessGateBaseUrl() . "?" . REQUEST_PARAM_NAME . "=" . urlencode($param);
    }

    private function buttonImageUrl()
    {
        $granted=$this->accessGranted() ? 1 : 0;
        return $this->accessGateBaseUrl() . "/button.php".
            "?c=" . $this->contentId .
            "&p=" . $this->priceInCents .
            "&g=" . $granted;
    }

    public function renderButton()
    {
        ob_start();
        $priceTag = str_replace("0.", "-.", sprintf("%.2f", $this->priceInCents / 100.0));
        $accessGateUrl = $this->accessGateUrl();
        $buttonImageUrl = $this->buttonImageUrl();
        include "impl/view/button.php";
        return ob_get_clean();
    }

    public function accessGranted() : bool
    {
        return $this->granted;
    }
}
?>