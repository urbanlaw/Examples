<?php


abstract class Control
{
    protected HtmlAttrs $attrs;

    protected abstract function Tag();

    protected function CssClass()
    {
        return '';
    }

    public static function Factory()
    {
        return new static;
    }

    public function __construct()
    {
        $this->attrs = new HtmlAttrs();
    }

    public function Id(string $id)
    {
        $this->attrs['id'] = $id;
        return $this;
    }

    public function Class(string $class)
    {
        $this->attrs['class'] = $class;
        return $this;
    }

    public function Data(string $field, $value)
    {
        $this->attrs['data-'.$field] = $value;
        return $this;
    }

    protected function BasicStr()
    {
        $this->AddClass($this->CssClass());
        return $this->Tag() . $this->attrs;
    }

    protected function TagOpener()
    {
        return '<' . $this->BasicStr() . '>';
    }

    protected function TagCloser()
    {
        return '</' . $this->Tag() . '>';
    }

    protected function OnPreRender()
    {
    }

    protected function AddClass(string $class)
    {
        if($class)
        {
            $this->attrs['class'] = TextLib::Merge(' ', $this->attrs['class'], $class);
        }
    }

    public abstract function __toString();
}
