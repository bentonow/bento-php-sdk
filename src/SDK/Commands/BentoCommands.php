<?php

namespace bentonow\Bento\SDK\Commands;

use bentonow\Bento\SDK\BentoClient;
use bentonow\Bento\SDK\Commands\BentoCommandTypes;

class BentoCommands
{
  /**
   * The commands endpoint.
   *
   * @var string
   */
  private $_url = '/fetch/commands';


  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * Create a new Bento Batch processor.
   *
   * @param \bentonow\Bento\SDK\BentoClient $client
   * @return void
   */
  public function __construct(BentoClient $client)
  {
    $this->_client = $client;
  }

  /**
   * **This does not trigger automations!** - If you wish to trigger automations, please use the
   * core module's `tagSubscriber` method.
   *
   * Adds a tag to the subscriber with the matching email.
   *
   * Note that both the tag and the subscriber will be created if either is missing
   * from system.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function addTag($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::ADD_TAG,
        'email' => $parameters['email'],
        'query' => $parameters['tagName'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error adding tag: ' . $parameters['tagName'] . ' to subscriber: ' . $parameters['email']);
    }
  }

  /**
   * Removes the specified tag from the subscriber with the matching email.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function removeTag($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::REMOVE_TAG,
        'email' => $parameters['email'],
        'query' => $parameters['tagName'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error removing tag: ' . $parameters['tagName'] . ' from subscriber: ' . $parameters['email']);
    }
  }

  /**
   * **This does not trigger automations!** - If you wish to trigger automations, please use the
   * core module's `updateFields` method.
   *
   * Adds a field to the subscriber with the matching email.
   *
   * Note that both the field and the subscriber will be created if either is missing
   * from system.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function addField($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::ADD_FIELD,
        'email' => $parameters['email'],
        'query' => $parameters['field'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error adding fields to subscriber: ' . $parameters['email']);
    }
  }

  /**
   * Removes a field to the subscriber with the matching email.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function removeField($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::REMOVE_FIELD,
        'email' => $parameters['email'],
        'query' => $parameters['fieldName'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error adding removing field: ' . $parameters['fieldName'] . ' from subscriber: ' . $parameters['email']);
    }
  }

  /**
   * **This does not trigger automations!** - If you wish to trigger automations, please use the
   * core module's `addSubscriber` method.
   *
   * Subscribes the supplied email to Bento. If the email does not exist, it is created.
   * If the subscriber had previously unsubscribed, they will be re-subscribed.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function subscribe($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::SUBSCRIBE,
        'email' => $parameters['email'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error subscribing: ' . $parameters['email']);
    }
  }

  /**
   * **This does not trigger automations!** - If you wish to trigger automations, please use the
   * core module's `removeSubscriber` method.
   *
   * Unsubscribes the supplied email to Bento. If the email does not exist, it is created and
   * immediately unsubscribed. If they had already unsubscribed, the `unsubscribed_at` property
   * is updated.
   *
   * @param mixed $parameters
   * @returns mixed
   */
  public function unsubscribe($parameters)
  {
    $response = $this->_constructCommandsResult($this->_client->post($this->_url, [
      'command' => [
        'command' => BentoCommandTypes::UNSUBSCRIBE,
        'email' => $parameters['email'],
      ]
    ]));

    if ($response != null) {
      return $response;
    } else {
      throw new \Exception('[BentoCommands] Error unsubscribing: ' . $parameters['email']);
    }
  }

  private function _constructCommandsResult($response)
  {
    $decodedResponse = json_decode($response->getBody(), true);
    return isset($decodedResponse['data']) ? $decodedResponse['data'] : null;
  }
}
