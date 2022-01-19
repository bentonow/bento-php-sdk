<?php

namespace bentonow\Bento\SDK\Experimental;

use bentonow\Bento\SDK\BentoClient;

class BentoExperimental
{
  /**
   * The experimental endpoint.
   *
   * @var string
   */
  private $_url = '/experimental';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Experimental processor.
   *
   * @param \bentonow\Bento\SDK\BentoClient $client
   * @return void
   */
  public function __construct(BentoClient $client)
  {
    $this->_client = $client;
  }

  /**
   * **EXPERIMENTAL** -
   * This functionality is experimental and may change or stop working at any time.
   *
   * Attempts to validate the email. You can provide additional information to further
   * refine the validation.
   *
   * If a name is provided, it compares it against the US Census Data, and so the results
   * may be biased.
   *
   * @param mixed $parameters
   * @returns boolean
   */
  public function validateEmail($parameters)
  {
    $response = $this->_client->post($this->_url . '/validation', [
      'email' => $parameters['email'],
      'ip' => isset($parameters['ip']) ? $parameters['ip'] : null,
      'name' => isset($parameters['name']) ? $parameters['name'] : null,
      'user_agent' => isset($parameters['userAgent']) ? $parameters['userAgent'] : null,
    ]);

    $result = json_decode($response->getBody(), true);
    return $result['valid'];
  }

  /**
   * **EXPERIMENTAL** -
   * This functionality is experimental and may change or stop working at any time.
   *
   * Attempts to guess the gender of the person given a provided name. It compares
   * the name against the US Census Data, and so the results may be biased.
   *
   * It is possible for the gender to be unknown if the system cannot confidently
   * conclude what gender it may be.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function guessGender($parameters)
  {
    $response = $this->_client->post($this->_url . '/gender', $parameters);

    $result = json_decode($response->getBody(), true);
    return $result;
  }

  /**
   * **EXPERIMENTAL** -
   * This functionality is experimental and may change or stop working at any time.
   *
   * Attempts to provide location data given a provided IP address.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function geolocate($parameters)
  {
    $response = $this->_client->get($this->_url . '/geolocation', $parameters);

    $result = json_decode($response->getBody(), true);
    return $result;
  }

  /**
   * **EXPERIMENTAL** -
   * This functionality is experimental and may change or stop working at any time.
   *
   * Looks up the provided URL or IP Address against various blacklists to see if the site has been
   * blacklisted anywhere.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function checkBlacklist($parameters)
  {
    $response = $this->_client->get($this->_url . '/blacklist.json', $parameters);

    $result = json_decode($response->getBody(), true);
    return $result;
  }
}
