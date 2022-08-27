<?php

trait InputControlAutoValueTrait
{
    public function Auto($defaultValue = '')
    {
        $this->Value($defaultValue);
        if($this->attrs['name'])
        {
            $requestValue = EnvLib::REQUEST($this->attrs['name']);
            if($requestValue !== null)
            {
                $this->Value($requestValue);
            }
        }
        return $this;
    }
}

abstract class InputControl extends Control
{
    abstract protected function Type();

    protected function Tag()
    {
        return 'input';
    }

    public function __construct()
    {
        parent::__construct();
        $this->attrs['type'] = $this->Type();
    }

    public function Name($name)
    {
        $this->attrs['name'] = $name;
        return $this;
    }

    public function Value($value)
    {
        $this->attrs['value'] = htmlspecialchars($value);
        return $this;
    }

    public function __toString()
    {
        $this->OnPreRender();
        return '<'.$this->BasicStr().' />';
    }
}