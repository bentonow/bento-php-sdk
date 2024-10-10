<?php

namespace bentonow\Bento\SDK;

class BentoClient
{
  /**
   * The headers to create the client with.
   *
   * @var array<string, string>
   */
  private $_headers = [];

  /**
   * The base URL to make requests against.
   *
   * @var string
   */
  private $_baseUrl = 'https://app.bentonow.com/api/v1';

  /**
   * The UUID of the site that this client corresponds to.
   *
   * @var string
   */
  private $_siteUuid = '';

  /**
   * The Guzzle client to use for making requests.
   * 
   * @var \GuzzleHttp\Client
   */
  private $_client;

  /**
   * Create a new Bento client.
   *
   * @param mixed $options
   * @return void
   */
  public function __construct($options)
  {
    if (!empty($options['clientOptions']) && !empty($options['clientOptions']['baseUrl'])) {
      $this->_baseUrl = $options['clientOptions']['baseUrl'];
    }

    $this->_siteUuid = $options['siteUuid'];

    if (empty($this->_siteUuid)) {
      throw new \Exception('The Bento client was created without a site UUID.');
    }

    $this->_headers = $this->_extractHeaders($options['authentication']);

    $this->_client = new \GuzzleHttp\Client([
      'base_uri' => $this->_baseUrl,
      'headers' => $this->_headers
    ]);
  }

  /**
   * Wraps a GET request to the Bento API and automatically adds the required
   * headers.
   *
   * @param string endpoint
   * @param mixed payload
   * @return \Psr\Http\Message\ResponseInterface
   * */
  public function get($endpoint, $payload = [])
  {
    return $this->_client->request('GET', $this->_baseUrl . $endpoint, [
      'query' => array_merge(
        $payload,
        [
          'site_uuid' => $this->_siteUuid
        ]
      )
    ]);
  }

  /**
   * Wraps a POST request to the Bento API and automatically adds the required
   * headers.
   *
   * @param string endpoint
   * @param mixed payload
   * @return \Psr\Http\Message\ResponseInterface
   * */
  public function post($endpoint, $payload = [])
  {
    return $this->_client->request('POST', $this->_baseUrl . $endpoint, [
      'json' => array_merge(
        $payload,
        [
          'site_uuid' => $this->_siteUuid
        ]
      )
    ]);
  }

  /**
   * Extracts the `publishableKey` and `secretKey` from the `authentication` options and
   * adds the `Authorization` header.
   *
   * @param mixed $authentication
   * @return array
   */
  private function _extractHeaders($authentication)
  {
    if (empty($authentication['publishableKey'])) {
      throw new \Exception('The Bento client was created without a publishable key.');
    }

    if (empty($authentication['secretKey'])) {
      throw new \Exception('The Bento client was created without a secret key.');
    }

    $authenticationKey = base64_encode(
      $authentication['publishableKey'] . ':' . $authentication['secretKey']
    );

    return [
      'Authorization' => 'Basic ' . $authenticationKey,
      'Content-Type' => 'application/json',
      'User-Agent' => 'bento-php-'.$this->_siteUuid,
    ];
  }

  public function __get($name)
  {
    if ($name == 'client') {
      return $this->_client;
    }

    return null;
  }
}
