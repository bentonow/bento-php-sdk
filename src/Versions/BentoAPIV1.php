<?php

namespace bentonow\Bento\Versions;

use bentonow\Bento\SDK\BentoClient;
use bentonow\Bento\SDK\Batch\BentoBatch;

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

  public function __construct($options)
  {
    $this->_client = new BentoClient($options);
    $this->_batch = new BentoBatch($this->_client);
  }

  public function __get($name)
  {
    if ($name == 'Batch') {
      return $this->_batch;
    }

    return null;
  }
}
