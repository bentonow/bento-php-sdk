<?php

namespace bentonow\Bento\SDK\Tags;

use bentonow\Bento\SDK\BentoClient;

class BentoTags
{
  /**
   * The tags endpoint.
   *
   * @var string
   */
  private $_url = '/fetch/tags';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Tags processor.
   *
   * @param \bentonow\Bento\SDK\BentoClient $client
   * @return void
   */
  public function __construct(BentoClient $client)
  {
    $this->_client = $client;
  }

  /**
   * Returns all of the fields for the site.
   *
   * @returns mixed
   */
  public function getTags()
  {
    $response = $this->_client->get($this->_url);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }

  /**
   * Creates a tag inside of Bento.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function createTag($parameters)
  {
    $response = $this->_client->post($this->_url, [
      'tag' => $parameters
    ]);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }
}
