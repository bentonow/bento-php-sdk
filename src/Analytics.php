<?php

namespace bentonow\Bento;

use bentonow\Bento\Versions\BentoAPIV1;

class Analytics
{
  private $_v1;

  public function __construct($options)
  {
    $this->_v1 = new BentoAPIV1($options);
  }

  public function __get($name)
  {
    if ($name == 'V1') {
      return $this->_v1;
    }

    return null;
  }
}
