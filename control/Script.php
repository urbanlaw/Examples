<?php


class Script extends Control
{

    protected function Tag()
    {
        return 'script';
    }

    public function Type(string $type)
    {
        $this->attrs['type'] = $type;
        return $this;
    }

    public function Src(string $src)
    {
        if(strpos($src, '?') === false && strpos($src, '//') === false)
        {
            $src .= '?' . filemtime(ROOT . $src);
        }
        $this->attrs['src'] = $src;
        return $this;
    }

    public function Defer()
    {
        $this->attrs['defer'] = null;
        return $this;
    }

    public function __toString()
    {
        return $this->TagOpener().$this->TagCloser();
    }
}