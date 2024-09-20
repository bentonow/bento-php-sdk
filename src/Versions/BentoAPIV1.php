<?php

namespace bentonow\Bento\Versions;

use bentonow\Bento\SDK\BentoClient;
use bentonow\Bento\SDK\Batch\BentoBatch;
use bentonow\Bento\SDK\Batch\BentoEvents;
use bentonow\Bento\SDK\Events\BentoPeopleEvents;
use bentonow\Bento\SDK\Commands\BentoCommands;
use bentonow\Bento\SDK\Experimental\BentoExperimental;
use bentonow\Bento\SDK\Fields\BentoFields;
use bentonow\Bento\SDK\Forms\BentoForms;
use bentonow\Bento\SDK\Subscribers\BentoSubscribers;
use bentonow\Bento\SDK\Tags\BentoTags;
use bentonow\Bento\SDK\Emails\BentoEmails;

class BentoAPIV1
{

  /**
   * The BentoClient to use.
   *
   * @var \bentonow\Bento\SDK\BentoClient
   */
  private $_client;

  /**
   * The BentoBatch to use.
   *
   * @var \bentonow\Bento\SDK\Batch\BentoBatch
   */
  private $_batch;

  /**
   * The BentoCommands to use.
   *
   * @var \bentonow\Bento\SDK\Commands\BentoCommands
   */
  private $_commands;

  /**
  * The BentoEmails to use.
  *
  * @var \bentonow\Bento\SDK\Emails\BentoEmails
  */
  private $_emails;

  /**
  * The BentoEvents to use.
  *
  * @var \bentonow\Bento\SDK\Events\BentoPeopleEvents
  */
  private $_events;

  /**
   * The BentoExperimental to use.
   *
   * @var \bentonow\Bento\SDK\Experimental\BentoExperimental
   */
  private $_experimental;

  /**
   * The BentoFields to use.
   *
   * @var \bentonow\Bento\SDK\Fields\BentoFields
   */
  private $_fields;

  /**
   * The BentoForms to use.
   *
   * @var \bentonow\Bento\SDK\Forms\BentoForms
   */
  private $_forms;

  /**
   * The BentoSubscribers to use.
   *
   * @var \bentonow\Bento\SDK\Subscribers\BentoSubscribers
   */
  private $_subscribers;

  /**
   * The BentoTags to use.
   *
   * @var \bentonow\Bento\SDK\Tags\BentoTags
   */
  private $_tags;

  public function __construct($options)
  {
    $this->_client = new BentoClient($options);
    $this->_batch = new BentoBatch($this->_client);
    $this->_commands = new BentoCommands($this->_client);
    $this->_emails = new BentoEmails($this->_client);
    $this->_events = new BentoPeopleEvents($this->_client);
    $this->_experimental = new BentoExperimental($this->_client);
    $this->_fields = new BentoFields($this->_client);
    $this->_forms = new BentoForms($this->_client);
    $this->_subscribers = new BentoSubscribers($this->_client);
    $this->_tags = new BentoTags($this->_client);
  }

  public function __get($name)
  {
    if ($name == 'Batch') {
      return $this->_batch;
    }

    if ($name == 'Commands') {
      return $this->_commands;
    }

    if ($name == 'Emails') {
      return $this->_emails;
    }

    if ($name == 'Events') {
      return $this->_events;
    }

    if ($name == 'Experimental') {
      return $this->_experimental;
    }

    if ($name == 'Fields') {
      return $this->_fields;
    }

    if ($name == 'Forms') {
      return $this->_forms;
    }

    if ($name == 'Subscribers') {
      return $this->_subscribers;
    }

    if ($name == 'Tags') {
      return $this->_tags;
    }

    return null;
  }

  /**
   * **This TRIGGERS automations!** - If you do not wish to trigger automations, please use the
   * `Commands.addTag` method.
   *
   * Tags a subscriber with the specified email and tag. If either the tag or the user
   * do not exist, they will be created in the system. If the user already has the tag,
   * another tag event will be sent, triggering any automations that take place upon a
   * tag being added to a subscriber. Please be aware of the potential consequences.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function tagSubscriber($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [
        'date' => isset($parameters['date']) ? $parameters['date'] : null,
        'details' => [
          'tag' => $parameters['tagName'],
        ],
        'email' => $parameters['email'],
        'type' => BentoEvents::TAG,
      ]
    ]);

    return $result->results == 1;
  }

  /**
   * **This TRIGGERS automations!** - If you do not wish to trigger automations, please use the
   * `Commands.subscribe` method.
   *
   * Creates a subscriber in the system. If the subscriber already exists, another subscribe event
   * will be sent, triggering any automations that take place upon subscription. Please be aware
   * of the potential consequences.
   *
   * You may optionally pass any fields that you wish to be set on the subscriber during creation
   * as well as a `Date` which will backdate the event. If no date is supplied, then the event will
   * default to the current time.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function addSubscriber($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [
        'date' => isset($parameters['date']) ? $parameters['date'] : null,
        'email' => $parameters['email'],
        'type' => BentoEvents::SUBSCRIBE,
        'fields' => $parameters['fields'] ?? [],
      ]
    ]);

    return $result->results == 1;
  }

  /**
   * **This TRIGGERS automations!** - If you do not wish to trigger automations, please use the
   * `Commands.unsubscribe` method.
   *
   * Unsubscribes an email in the system. If the email is already unsubscribed, another unsubscribe event
   * will be sent, triggering any automations that take place upon an unsubscribe happening. Please be aware
   * of the potential consequences.
   *
   * You may optionally pass a `Date` which will backdate the event. If no date is supplied, then the event
   * will default to the current time.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function removeSubscriber($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [
        'date' => isset($parameters['date']) ? $parameters['date'] : null,
        'email' => $parameters['email'],
        'type' => BentoEvents::UNSUBSCRIBE,
      ]
    ]);

    return $result->results == 1;
  }

  /**
   * **This TRIGGERS automations!** - If you do not wish to trigger automations, please use the
   * `Commands.addField` method.
   *
   * Sets the passed-in custom fields on the subscriber, creating the subscriber if it does not exist.
   * If the fields are already set on the subscriber, the event will be sent, triggering any automations
   * that take place upon fields being updated. Please be aware of the potential consequences.
   *
   * You may optionally pass a `Date` which will backdate the event. If no date is supplied, then the event
   * will default to the current time.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function updateFields($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [
        'date' => isset($parameters['date']) ? $parameters['date'] : null,
        'email' => $parameters['email'],
        'type' => BentoEvents::UPDATE_FIELDS,
        'fields' => $parameters['fields'],
      ]
    ]);

    return $result->results == 1;
  }

  /**
   * **This TRIGGERS automations!** - There is no way to achieve this same behavior without triggering
   * automations.
   *
   * Tracks a purchase in Bento, used to calculate LTV for your subscribers. The values that are received
   * should be numbers, in cents. For example, `$1.00` should be `100`.
   *
   * You may optionally pass a `Date` which will backdate the event. If no date is supplied, then the event
   * will default to the current time.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function trackPurchase($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [
        'date' => isset($parameters['date']) ? $parameters['date'] : null,
        'email' => $parameters['email'],
        'type' => BentoEvents::PURCHASE,
        'details' => $parameters['purchaseDetails'],
      ]
    ]);

    return $result->results == 1;
  }

  /**
   * **This TRIGGERS automations!** - There is no way to achieve this same behavior without triggering
   * automations.
   *
   * Tracks a custom event in Bento.
   *
   * You may optionally pass a `Date` which will backdate the event. If no date is supplied, then the event
   * will default to the current time.
   *
   * Because this method uses the batch API, the tag may take between 1 and 3 minutes
   * to appear in the system.
   *
   * Returns `true` if the event was successfully dispatched. Returns `false` otherwise.
   *
   * @param mixed parameters
   * @returns boolean
   */
  public function track($parameters)
  {
    $result = $this->_batch->importEvents([
      'events' => [$parameters]
    ]);

    return $result->results == 1;
  }
}
