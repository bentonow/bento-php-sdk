<?php

namespace bentonow\Bento\SDK\Batch;

abstract class BentoEvents
{
  const PURCHASE = '$purchase';
  const SUBSCRIBE = '$subscribe';
  const TAG = '$tag';
  const UNSUBSCRIBE = '$unsubscribe';
  const UPDATE_FIELDS = '$update_fields';
}
