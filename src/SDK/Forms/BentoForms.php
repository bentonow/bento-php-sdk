<?php

namespace bentonow\Bento\SDK\Forms;

use bentonow\Bento\SDK\BentoClient;

class BentoForms
{
  /**
   * The forms endpoint.
   *
   * @var string
   */
  private $_url = '/fetch/responses';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Forms processor.
   *
   * @param \bentonow\Bento\SDK\BentoClient $client
   * @return void
   */
  public function __construct(BentoClient $client)
  {
    $this->_client = $client;
  }

  /**
   * Returns all of the responses for the form with the specified identifier.
   *
   * @param string formIdentifier
   * @returns mixed
   */
  public function getResponses($formIdentifier)
  {
    $response = $this->_client->get($this->_url, [
      'id' => $formIdentifier,
    ]);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }
}
