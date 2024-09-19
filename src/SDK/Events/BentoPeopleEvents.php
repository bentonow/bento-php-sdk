<?php

namespace bentonow\Bento\SDK\Events;

use bentonow\Bento\SDK\BentoClient;

class BentoPeopleEvents
{
    /**
     * The Events endpoint.
     *
     * @var string
     */
    private $_url = '/batch/events';


    /**
     * The BentoClient to use.
     *
     * @var \bentonow\Bento\SDK\BentoClient
     */
    private $_client;

    /**
     * Create a new Bento Events processor.
     *
     * @param \bentonow\Bento\SDK\BentoClient $client
     * @return void
     */
    public function __construct(BentoClient $client)
    {
        $this->_client = $client;
    }

    /**
     * This endpoint is used to send events to bento. Takes an array of events.
     * 1 - 1000 events per request, creates users if they don't exist already.
     * For example:
     * [
     *  'events' => [
     *      [
     *         'type' => '$completed_onboarding',
     *         'email' => 'test@test.com',
     *      ]
     *  ]
     * ]
     *
     *
     * @param mixed $parameters
     * @returns mixed
     */
    public function createEvents($parameters)
    {
        $response = $this->_client->post($this->_url, [
            'events' => $parameters
        ]);

        $result = json_decode($response->getBody(), true);
        return isset($result['data']) ? $result['data'] : null;
    }
}