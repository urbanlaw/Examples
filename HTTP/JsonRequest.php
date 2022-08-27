<?php


class JsonRequest
{
    protected string $url;
    protected array $headerValues = [];
    protected $postData = null;
    protected array $queryData = [];
    protected array $urlData = [];
    protected Curl $curl;

    protected string $header;
    protected string $body = '';

    public static function Factory(string $url = '')
    {
        return new static($url);
    }

    public function __construct(string $url = '')
    {
        $this->SetUrl($url);
        $this->curl = Curl::Factory()
            ->SetOpt(CURLOPT_SSL_VERIFYPEER, false)
            ->SetOpt(CURLOPT_RETURNTRANSFER, true)
            ->SetOpt(CURLOPT_HEADER, true)
            ->SetOpt(CURLINFO_HEADER_OUT, true)
        ;

        $this->SetHeaderValue('Content-Type', 'application/json');
    }

    /** Вернуть объект CURL для чтения/редактирования */
    public function Curl(): Curl
    {
        return $this->curl;
    }

    /** Установить URL запроса */
    public function SetUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /** Установить переменную заголовка */
    public function SetHeaderValue(string $key, string $val)
    {
        $this->headerValues[$key] = $val;
        return $this;
    }

    /** Установить POST данные
     * @param $data - ассоциативный массив либо строка
     * @return $this
     */
    public function SetPostData($data)
    {
        $this->postData = $data;
        return $this;
    }

    /** Добавить GET данные в URL
     * @param array $data - ассоциативный массив
     * @return $this
     */
    public function AddQueryData(array $data)
    {
        $this->queryData = array_merge($this->queryData, $data);
        return $this;
    }

    /** Добавить данные для подстановки в URL, пример //example.ru/api/{method}/list
     * @param array $data - ассоциативный массив
     * @return $this
     */
    public function AddUrlData(array $data)
    {
        $this->urlData = array_merge($this->urlData, $data);
        return $this;
    }

    /** Выполнить запрос и вернуть объект ответа */
    public function Execute(): JsonResponse
    {
        $this->curl->SetOpt(CURLOPT_HTTPHEADER, $this->FormatHeaderValues($this->headerValues));

        $finalUrl = $this->url;
        foreach($this->urlData as $key => $val)
        {
            $finalUrl = str_replace('{' . $key . '}', $val, $finalUrl);
        }

        if($this->queryData)
        {
            $finalUrl .= '?' . http_build_query($this->queryData);
        }
        $this->curl->SetOpt(CURLOPT_URL, $finalUrl);

        if($this->postData !== null)
        {
            $this->body = $this->Encode($this->postData);

            $this->curl
                ->SetOpt(CURLOPT_POST, true)
                ->SetOpt(CURLOPT_POSTFIELDS, $this->body)
            ;
        }

        $response = $this->curl->Exec();
        $this->header = $this->curl->GetInfo(CURLINFO_HEADER_OUT);

        if($this->curl->Errno())
        {
            throw new Exception('Ошибка cURL: ' . $this->curl->Errno() . ' - ' . $this->curl->Error());
        }

        return new JsonResponse($this, $response);
    }

    /** Вернуть заголовок запроса */
    public function Header(): string
    {
        return $this->header;
    }

    /** Вернуть тело запроса */
    public function Body(): string
    {
        return $this->body;
    }

    /** Декодировать тело запроса */
    public function Decode()
    {
        if($this->body === '')
        {
            return '';
        }

        if($this->body)
        {
            switch($this->headerValues['Content-Type'])
            {
                case 'application/json':
                    return JSON::Decode($this->body);
                case 'multipart/form-data':
                    return $this->body;
                default:
                    throw new NotImplementedException('Unexpected content type');
            }
        }
    }

    /** Кодировать данные для отправки */
    private function Encode($data)
    {
        if($data === '')
        {
            return '';
        }

        switch($this->headerValues['Content-Type'])
        {
            case 'application/json':
                return JSON::Encode($data);
            case 'multipart/form-data':
                return $data;
            default:
                throw new NotImplementedException('Unexpected content type');
        }
    }

    /** Форматировать заголовок из ассоциативного массива в формат для отправки
     * @param array $headerValues - ассоциативный массив переменных
     * @return array - массив строк для отправки
     */
    private function FormatHeaderValues(array $headerValues): array
    {
        $arr = [];
        foreach($headerValues as $key => $value)
        {
            $arr[] = $key . ': ' . $value;
        }
        return $arr;
    }
}
