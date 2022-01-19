<?php

namespace bentonow\Bento\SDK\Fields;

use bentonow\Bento\SDK\BentoClient;
use bentonow\Bento\SDK\Fields\BentoCommandTypes;

class BentoFields
{
  /**
   * The fields endpoint.
   *
   * @var string
   */
  private $_url = '/fetch/fields';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Fields processor.
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
  public function getFields()
  {
    $response = $this->_client->get($this->_url);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }

  /**
   * Creates a field inside of Bento. The name of the field is automatically generated
   * from the key that is passed in upon creation. For example
   *  - Key: `thisIsAKey`
   *    Name: `This Is A Key`
   *  - Key: `this is a key`
   *    Name: `This Is A Key`
   *  - Key: `this-is-a-key`
   *    Name: `This Is A Key`
   *  - Key: `this_is_a_key`
   *    Name: `This Is A Key`
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function createField($parameters)
  {
    $response = $this->_client->post($this->_url, [
      'field' => $parameters
    ]);

    $result = json_decode($response->getBody(), true);
    return isset($result['data']) ? $result['data'] : null;
  }
}
