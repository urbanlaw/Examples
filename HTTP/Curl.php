<?php


class Curl
{
    private $handler;

    static function Factory()
    {
        return new static;
    }

    public function __construct()
    {
        $this->handler = curl_init();
    }

    public function __destruct()
    {
        curl_close($this->handler);
    }

    public function SetOpt(int $option, $value)
    {
        curl_setopt($this->handler, $option, $value);
        return $this;
    }

    public function GetInfo(?int $option = null)
    {
        if(!$option)
        {
            return curl_getinfo($this->handler);
        }
        return curl_getinfo($this->handler, $option);
    }

    public function Errno()
    {
        return curl_errno($this->handler);
    }

    public function Error()
    {
        return curl_error($this->handler);
    }

    public function Exec()
    {
        return curl_exec($this->handler);
    }
}
