<?php

namespace bentonow\Bento\SDK\Batch;

class BatchImportResult
{
  /**
   * The number of successful imports.
   *
   * @var int
   */
  private $_results = 0;

  /**
   * The number of failed imports.
   *
   * @var int
   */
  private $_failed = 0;

  public function __construct($results, $failed)
  {
    $this->_results = $results;
    $this->_failed = $failed;
  }

  public function __get($name)
  {
    if ($name == 'results') {
      return $this->_results;
    }

    if ($name == 'failed') {
      return $this->_failed;
    }

    return null;
  }
}
