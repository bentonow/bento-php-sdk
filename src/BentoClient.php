<?php

namespace Bento;


class BentoClient
{
    public $client;
    public $site_uuid;

    const DEFAULT_CONFIGURATION = [
        'site_uuid' => null,
        'push_endpoint' => 'https://app.bentonow.com/tracking/zapier/',
    ];

    public function __construct($site_uuid = false)
    {
        if ($site_uuid) {
            $this->site_uuid = $site_uuid;
        } else {
            $config = $this->configFromEnvironment();
            if (isset($config['site_uuid'])) {
                $this->site_uuid = $config['site_uuid'];
            }
        }

        if (!$this->site_uuid) {
            throw new \Exception('No site uuid found.');
        }


        $options = array_merge([], [
            'base_uri' => 'https://app.bentonow.com/'
        ]);

        $this->client = new \GuzzleHttp\Client($options);
    }



    public function send($payload = [])
    {
        $this->request('POST', '/tracking/zapier', $payload);
    }



    public function post($endpoint, $payload = [])
    {
        $this->request('POST', $endpoint, $payload);
    }

    private function request($method, $endpoint, $payload = [])
    {
        $options = [
            'body' => json_encode($payload),
            'headers' => [
                'Accept' => 'application/json',
                // 'Authorization' => "Push {$this->config->get('push_key')}",
                'Content-Type' => 'application/json; charset=UTF-8'
            ]
        ];

        $this->client->request($method, $endpoint, $options);
    }

    private function defaultConfig()
    {
        return self::DEFAULT_CONFIGURATION;
    }



    private function configFromEnvironment()
    {
        $config = [];
        $keys = array_keys($this->defaultConfig());

        foreach ($keys as $key) {
            $value = getenv("BENTO_" . strtoupper($key), true);

            if ($value) {
                $config[$key] = $value;
            }
        }

        return $config;
    }
}
