<?php

namespace tincap\Bot;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use tincap\Bot\Request\Request;

abstract class Bot
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $userAgent;

    /** @var FileCookieJar */
    protected $cookiesJar = false;

    /** @var string */
    protected $proxy = false;

    /** @var array */
    protected $container;

    public $config;

    /**
     * Bot constructor
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->container = [];
        $history = Middleware::history($this->container);
        $stack = HandlerStack::create();
        $stack->push($history);

        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * @return array
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return array
     */
    abstract public static function getMandatoryHeaders();

    /**
     * @return string
     */
    abstract public static function getHost();

    /**
     * @return FileCookieJar
     */
    public function getCookieJar()
    {
        return $this->cookiesJar;
    }

    /**
     * @param $ip
     * @param $password
     */
    public function setProxy($ip, $password)
    {
        $this->proxy = $this->formattingProxyToGuzzle($ip, $password);
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @param $cookiesJar
     */
    public function setCookiesJar($cookiesJar)
    {
        $this->cookiesJar = $cookiesJar;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param $ip
     * @param $password
     * @return string
     */
    public function formattingProxyToGuzzle($ip, $password)
    {
        return "http://{$password}@{$ip}";
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    abstract public static function getCookiesPath();

    /**
     * @param $method
     * @param $uri
     * @return Request
     */
    public function request($method, $uri)
    {
        return new Request($this, $method, $uri);
    }
}