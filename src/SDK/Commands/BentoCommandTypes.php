<?php

namespace bentonow\Bento\SDK\Commands;

abstract class BentoCommandTypes
{
  const ADD_FIELD = 'add_field';
  const ADD_TAG = 'add_tag';
  const REMOVE_FIELD = 'remove_field';
  const REMOVE_TAG = 'remove_tag';
  const SUBSCRIBE = 'subscribe';
  const UNSUBSCRIBE = 'unsubscribe';
}
