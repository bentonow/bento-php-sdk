<?php

namespace bentonow\Bento\SDK\Subscribers;

use bentonow\Bento\SDK\BentoClient;

class BentoSubscribers
{
  /**
   * The subscribers endpoint.
   *
   * @var string
   */
  private $_url = '/fetch/subscribers';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Subscribers processor.
   *
   * @param \bentonow\Bento\SDK\BentoClient $client
   * @return void
   */
  public function __construct(BentoClient $client)
  {
    $this->_client = $client;
  }

  /**
   * Returns the subscriber with the specified email or UUID.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function getSubscribers($parameters = [])
  {
    $response = $this->_client->get($this->_url, $parameters);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }

  /**
   * Creates a subscriber inside of Bento.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function createSubscriber($parameters)
  {
    $response = $this->_client->post($this->_url, [
      'subscriber' => $parameters
    ]);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }
}
