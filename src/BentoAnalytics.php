<?php

namespace bentonow\Bento;

use bentonow\Bento\Versions\BentoAPIV1;

class BentoAnalytics
{
  /**
   * The V1 class to use.
   *
   * @var \bentonow\Bento\Versions\BentoAPIV1
   */
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
