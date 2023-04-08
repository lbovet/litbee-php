<?php

namespace Litbee\Access;

class Session
{

    private $started=false;

    public function start()
    {
        if(!$this->started){
            session_start();
            $this->started=true;
        }
        return $this;
    }

    public function setItem($key,$value)
    {
        $_SESSION[$key]=$value;
        return $this;
    }


    public function getItem($key)
    {
        if(!isset($_SESSION[$key])){
            throw new \Exception("Session item $key does not exist");
        }

        return $_SESSION[$key];
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function end()
    {
        session_destroy();
        $this->started=false;
        return $this;
    }
}