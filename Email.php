<?php


class Email
{
    public string $subject = '';
    public string $body = '';
    public string $to = '';
    public string $replyTo = '';

    static function Factory(string $recipient)
    {
        $email = new static();
        $email->to = $recipient;
        return $email;
    }

    public function SetSubject(string $v)
    {
        $this->subject = $v;
        return $this;
    }

    public function SetBody(string $v)
    {
        $this->body = $v;
        return $this;
    }

    public function Headers()
    {
        $headers = [
            'Content-type' => "text/html; charset=" . CHARSET,
            'From' => SITE_NAME . " <" . ROBOT_EMAIL . ">",
        ];

        if($this->replyTo)
        {
            $headers['Reply-To'] = '<' . $this->replyTo . '>';
        }

        return $headers;
    }

    public function __toString()
    {
        $html = '';
        $html .= '=== ' . $this->subject . ' ===<br/><br/>';
        $html .= $this->body;
        return $html;
    }

    public static function Send(Email $email)
    {
        if(!Valid::Email($email->to))
        {
            return false;
        }
        if($email->replyTo && !Valid::Email($email->replyTo))
        {
            return false;
        }

        $to = static::FormatTo($email->to);
        $subject = static::FormatSubject($email->subject);
        $body = static::FormatText($email->body);
        $headers = static::FormatHeaders($email->Headers());
        return mail($to, $subject, $body, $headers);
    }

    public static function SendToSupport(Email $email)
    {
        $copy = clone($email);
        $copy->replyTo = $copy->to;
        $copy->to = SUPPORT_EMAIL;
        return static::Send($copy);
    }

    public static function SendWithNotice(Email $email)
    {
        return static::Send($email) && static::SendToSupport($email);
    }

    public static function ToAdmin(string $text, $data = null)
    {
        $email = static::Factory(ADMIN_EMAIL);
        $email->subject = 'Передайте Админу:';

        $email->body = $text . '<br/><br/>';
        if($data !== null)
        {
            $email->body .= '<pre>' . print_r($data, true) . '</pre><br/><br/>';
        }

        ob_start();
        debug_print_backtrace();
        $email->body .= '<pre>' . ob_get_clean() . '</pre>';

        static::Send($email);
    }

    public static function ToAdminException(Throwable $exception)
    {
        $email = static::Factory(ADMIN_EMAIL);
        $email->subject = 'Передайте Админу:';
        $email->body = '<pre>' . $exception->__toString() . '</pre>';
        static::Send($email);
    }

    private static function FormatTo($to)
    {
        return trim($to);
    }

    private static function FormatSubject($subject)
    {
        return "=?" . CHARSET . "?b?" . base64_encode(trim($subject)) . "?=";
    }

    private static function FormatText($text)
    {
        return trim($text);
    }

    private static function FormatHeaders($headers)
    {
        $arr = [];
        foreach($headers as $key => $val)
        {
            $arr[] = $key . ': ' . $val;
        }
        return implode("\r\n", $arr);
    }
}