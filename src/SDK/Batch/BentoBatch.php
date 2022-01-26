<?php

namespace bentonow\Bento\SDK\Batch;

use bentonow\Bento\SDK\BentoClient;
use bentonow\Bento\SDK\Batch\BatchImportResult;

class BentoBatch
{
  /**
   * The maximum batch size to allow.
   *
   * @var int
   */
  private $_maxBatchSize = 1000;

  /**
   * The batch endpoint.
   *
   * @var string
   */
  private $_url = '/batch';


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
   * **This does not trigger automations!** - If you wish to trigger automations, please batch import
   * events with the type set to `BentoEvents.SUBSCRIBE`, or `$subscribe`. Note that the batch event import
   * cannot attach custom fields and will ignore everything except the email.
   *
   * Creates a batch job to import subscribers into the system. You can pass in
   * between 1 and 1,000 subscribers to import. Each subscriber must have an email,
   * and may optionally have any additional fields. The additional fields are added
   * as custom fields on the subscriber.
   *
   * This method is processed by the Bento import queues and it may take between 1 and
   * 5 minutes for the results to appear in your dashboard.
   *
   * Returns the number of subscribers that were imported.
   *
   * @param mixed parameters
   * @returns \bentonow\Bento\SDK\Batch\BatchImportsResult
   */
  public function importSubscribers($parameters)
  {
    if (count($parameters['subscribers']) === 0) {
      throw new \Exception('You must send between 1 and 1,000 subscribers.');
    }

    if (count($parameters['subscribers']) > $this->_maxBatchSize) {
      throw new \Exception('You must send between 1 and 1,000 subscribers.');
    }

    $response = $this->_client->post($this->_url . '/subscribers', [
      'subscribers' => $parameters['subscribers']
    ]);

    return $this->_constructImportResult($response);
  }

  /**
   * Creates a batch job to import events into the system. You can pass in
   * between 1 and 1,000 events to import. Each event must have an email and
   * a type. In addition to this, you my pass in additional data in the
   * `details` property.
   *
   * Returns the number of events that were imported.
   *
   * @param mixed parameters
   * @returns \bentonow\Bento\SDK\Batch\BatchImportsResult
   */
  public function importEvents($parameters)
  {
    if (count($parameters['events']) === 0) {
      throw new \Exception('You must send between 1 and 1,000 events.');
    }

    if (count($parameters['events']) > $this->_maxBatchSize) {
      throw new \Exception('You must send between 1 and 1,000 events.');
    }

    $response = $this->_client->post($this->_url . '/events', [
      'events' => $parameters['events']
    ]);

    return $this->_constructImportResult($response);
  }

  private function _constructImportResult($response)
  {
    $decodedResponse = json_decode($response->getBody(), true);
    return new BatchImportResult($decodedResponse['results'], $decodedResponse['failed']);
  }
}
