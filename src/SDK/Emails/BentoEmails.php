<?php

namespace bentonow\Bento\SDK\Emails;

use bentonow\Bento\SDK\BentoClient;
class BentoEmails
{
    /**
     * The Emails endpoint.
     *
     * @var string
     */
    private $_url = '/batch/emails';


    /**
     * The BentoClient to use.
     *
     * @var \bentonow\Bento\SDK\BentoClient
     */
    private $_client;

    /**
     * Create a new Bento Emails processor.
     *
     * @param \bentonow\Bento\SDK\BentoClient $client
     * @return void
     */
    public function __construct(BentoClient $client)
    {
        $this->_client = $client;
    }

    /**
     * Creates an email request. Requests are instant and queued into a priority queue.
     * Transaction emails appear in email logs or under the profile of the subscriber.
     * Takes an array of emails as an array. Minimum 1 maximum of 60 emails. Example:
     * [
     *  [
     *     "to": "test@bentonow.com",
     *     "from": "jesse@bentonow.com",
     *     "subject": "Reset Password",
     *     "html_body": "<p>Here is a link to reset your password ... {{ link }}</p>",
     *     "transactional": true,
     *     "personalizations": [
     *         "link": "https://example.com/test"
     *      ]
     *  ]
     * ]
     *
     * @param mixed $parameters
     * @returns mixed
     */
    public function createEmail($parameters)
    {
        $response = $this->_client->post($this->_url, [
            'emails' => $parameters
        ]);

        $result = json_decode($response->getBody(), true);
        return isset($result['data']) ? $result['data'] : null;
    }

}