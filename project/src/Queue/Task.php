<?php

namespace Queue\Queue;

class Task
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var int
     */
    private $delay;

    /**
     * @var string
     */
    private $datetime;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $json = [];

    /**
     * @var array
     */
    private $query_string = [];

    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $sleep;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param string $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getJson(): array
    {
        return $this->json;
    }

    /**
     * @param array $json
     */
    public function setJson(array $json)
    {
        $this->json = $json;
    }

    /**
     * @return array
     */
    public function getQueryString(): array
    {
        return $this->query_string;
    }

    /**
     * @param array $query_string
     */
    public function setQueryString(array $query_string)
    {
        $this->query_string = $query_string;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getSleep()
    {
        return $this->sleep;
    }

    /**
     * @param int $sleep
     */
    public function setSleep($sleep)
    {
        $this->sleep = $sleep;
    }
}