<?php

namespace tincap\Bot\Request;


use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use tincap\Bot\Bot;

class Request
{
    /** @var array */
    private $_headers;

    /** @var array */
    private $_posts;

    /** @var array */
    private $_params;

    /** @var string */
    private $_uri;

    /** @var string */
    private $_method;

    /** @var string */
    private $_host;

    /** @var Bot */
    private $_bot;

    /** @var Response */
    public $response;

    /**
     * Request constructor.
     * @param $bot
     * @param $method
     * @param $uri
     * @param array $posts
     * @param array $params
     */
    public function __construct($bot, $method, $uri, $posts = [], $params = [])
    {
        $this->_bot = $bot;
        $this->_method = $method;
        $this->_uri = trim($uri, '/');
        $this->_host = rtrim($this->_bot::getHost(), '/');
        $this->_posts = $posts;
        $this->_params = $params;

        $this->prepareRequest();
    }

    /**
     * Подгатавливаем client
     */
    public function prepareRequest() {
        foreach ($this->_bot::getMandatoryHeaders() as $key => $value) {
            $this->setHeader($key, $value);
        }

        $this->setHeader('User-Agent', $this->_bot->getUserAgent());
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key, $value) {
        $this->_headers[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addPost($key, $value)
    {
        $this->_posts[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * @param $host
     * @return Request
     */
    public function setHost($host)
    {
        $this->_host = rtrim($host, '/');
        return $this;
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResponse()
    {
        if ($this->_host == '') {
            $host = '';
        } else {
            $host = $this->_host . '/';
        }

        $url = $host . $this->_uri . '?' . http_build_query($this->_params);

        $this->response = $this->_bot->getClient()->request($this->_method, $url, [
            RequestOptions::HEADERS => $this->_headers,
            RequestOptions::FORM_PARAMS => $this->_posts,
            RequestOptions::PROXY => $this->_bot->getProxy(),
            RequestOptions::COOKIES => $this->_bot->getCookieJar(),
        ]);

        return $this->response;
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContent()
    {
        return $this->getResponse()->getBody()->getContents();
    }
}