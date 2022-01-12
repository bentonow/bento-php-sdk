<?php

namespace bentonow\Bento\Versions;

use bentonow\Bento\SDK\BentoClient;

class BentoAPIV1
{
  private $_client;

  public function __construct($options)
  {
    $this->_client = new BentoClient($options);
  }
}
