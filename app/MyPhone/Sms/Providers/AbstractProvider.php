<?php

namespace App\MyPhone\Sms\Providers;

use App\MyPhone\Sms\Contracts\Provider;
use GuzzleHttp\Client;
use InvalidArgumentException;

abstract class AbstractProvider implements Provider
{

    /**
     * The HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The custom parameters to be sent with the request.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The provider configs.
     *
     * @var array
     */
    protected $config = [];


    /**
     * Create a new provider instance.
     *
     * @param  array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }


    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }


    protected function request($method, $uri = '/', array $parameters = [])
    {
        return $this->getHttpClient()
                    ->request($method, $this->config['api_url'] . $uri, ['form_params' => array_merge($this->parameters, $parameters)]);
    }


    /**
     * Set the Guzzle HTTP client instance.
     *
     * @param Client|\GuzzleHttp\Client $client
     *
     * @return $this
     */
    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;

        return $this;
    }


    /**
     * Set the custom parameters of the request.
     *
     * @param  array $parameters
     *
     * @return $this
     */
    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }


    /**
     * Send Sms
     *
     * @param array $receivers
     * @param       $text
     * @param       $sender
     * @param array $parameters
     *
     * @return mixed
     */
    public function send(array $receivers, $text, $sender, array $parameters = [])
    {
        throw new InvalidArgumentException('Driver Not Support Send Messages.');
    }


    /**
     * Track Sms
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function track(array $parameters = [])
    {
        throw new InvalidArgumentException('Driver Not Support Tracking Messages.');
    }


    /**
     * Receive Messages
     *
     * @param array $parameters
     *
     * @return array
     */
    public function receive(array $parameters = [])
    {
        throw new InvalidArgumentException('Driver Not Support Receive Messages.');
    }
}