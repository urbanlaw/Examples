<?php

/*
 * Static factory
 */

class UI
{
    public static function Sanitize($v) { return htmlspecialchars($v); }

    public static function TextInput() { return new TextInput(); }

    public static function PasswordInput() { return new PasswordInput(); }

    public static function AreaInput() { return new AreaInput(); }

    public static function CheckInput() { return new CheckInput(); }

    public static function RadioInput() { return new RadioInput(); }

    public static function HiddenInput() { return new HiddenInput(); }

    public static function SelectInput() { return new SelectInput(); }

    public static function Submit() { return new Submit(); }

    public static function Button() { return new Button(); }

    public static function ScanButton() { return new ScanButton(); }

    public static function RequestButton() { return new RequestButton(); }

    public static function LazyButton() { return new LazyButton(); }

    public static function LazyWidget() { return new LazyWidget(); }

    public static function InputLabel(string $text, Control $input) { return new InputLabel($text, $input); }

    public static function StarRating() { return new StarRating(); }

    public static function ProdCard() { return new ProdCard(); }

    public static function Alerts() { return new Alerts(); }

    public static function Script() { return new Script(); }
}