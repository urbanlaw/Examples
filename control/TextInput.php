<?php

class TextInput extends InputControl
{
    use InputControlAutoValueTrait;

    protected function Type()
    {
        return 'text';
    }

    public function Placeholder(string $placeholder)
    {
        $this->attrs['placeholder'] = $placeholder;
        return $this;
    }

    public function MaxLength(int $v)
    {
        $this->attrs['maxlength'] = $v;
        return $this;
    }

    public function AutocompleteOff()
    {
        $this->attrs['autocomplete'] = 'off';
        return $this;
    }
}