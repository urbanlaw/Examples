<?php


class JsonResponse
{
    protected JsonRequest $request;
    protected int $code;
    protected string $header;
    protected string $body;

    public function __construct(JsonRequest $request, string $response)
    {
        $this->request = $request;
        $this->code = $request->Curl()->GetInfo(CURLINFO_RESPONSE_CODE);

        $headersSize = $request->Curl()->GetInfo(CURLINFO_HEADER_SIZE);
        $this->header = substr($response, 0, $headersSize);
        $this->body = substr($response, $headersSize);
    }

    /** Вернуть HTTP код ответа  */
    public function Code()
    {
        return $this->code;
    }

    /** Вернуть заголовок ответа */
    public function Header()
    {
        return $this->header;
    }

    /** Вернуть тело ответа */
    public function Body()
    {
        return $this->body;
    }

    /** Вернуть декодированное тело ответа */
    public function Decode()
    {
        return $this->DecodeJson();
    }

    /** Вернуть декодированное тело ответа */
    public function DecodeJson()
    {
        if($this->body)
        {
            return JSON::Decode($this->body);
        }
        return [];
    }

    /** Вернуть объект запроса */
    public function Request()
    {
        return $this->request;
    }

    /** Вывести дебаг инфу */
    public function Debug()
    {
        echo DebugReport::Factory()
            ->Add('Заголовок запроса', $this->Request()->Header())
            ->Add('Тело запроса', $this->Request()->Decode())
            ->Add('Заголовок ответа', $this->Header())
            ->Add('Тело ответа', $this->Decode())
        ;
    }
}
