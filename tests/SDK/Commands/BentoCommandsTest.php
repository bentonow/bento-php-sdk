<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoCommandsTest extends TestCase
{
  private $_resolvedObject = [
    'id' => '444792518',
    'type' => 'visitors',
    'attributes' => [
      'uuid' => '090289b2a1cf40e8a85507eb9ae73684',
      'email' => 'test@bentonow.com',
      'fields' => null,
      'cached_tag_ids' => ['1096'],
      'unsubscribed_at' => null,
    ],
  ];

  public function setUp(): void
  {
    Server::start();

    register_shutdown_function(static function () {
      Server::stop();
    });
  }

  public function tearDown(): void
  {
    Server::flush();
  }

  public function testCanAddATag()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($this->_resolvedObject))
    ]);

    $result = $bento->V1->Commands->addTag([
      'email' => 'test@bentonow.com',
      'tagName' => 'test-tag',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"add_tag","email":"test@bentonow.com","query":"test-tag"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($this->_resolvedObject, $result);
  }

  public function testCanRemoveATag()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($this->_resolvedObject))
    ]);

    $result = $bento->V1->Commands->removeTag([
      'email' => 'test@bentonow.com',
      'tagName' => 'test-tag',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"remove_tag","email":"test@bentonow.com","query":"test-tag"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($this->_resolvedObject, $result);
  }

  public function testCanAddAField()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    $myData = array_merge($this->_resolvedObject, [
      'fields' => [
        'testKey' => 'testValue'
      ]
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($myData))
    ]);

    $result = $bento->V1->Commands->addField([
      'email' => 'test@bentonow.com',
      'field' => [
        'key' => 'testKey',
        'value' => 'testValue'
      ]
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"add_field","email":"test@bentonow.com","query":{"key":"testKey","value":"testValue"}},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($myData, $result);
  }

  public function testCanRemoveAField()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($this->_resolvedObject))
    ]);

    $result = $bento->V1->Commands->removeField([
      'email' => 'test@bentonow.com',
      'fieldName' => 'testField'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"remove_field","email":"test@bentonow.com","query":"testField"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($this->_resolvedObject, $result);
  }

  public function testCanSubscribe()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($this->_resolvedObject))
    ]);

    $result = $bento->V1->Commands->subscribe([
      'email' => 'test@bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"subscribe","email":"test@bentonow.com"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($this->_resolvedObject, $result);
  }

  public function testCanUnsubscribe()
  {
    $bento = new BentoAnalytics([
      'authentication' => [
        'secretKey' => '123SK',
        'publishableKey' => '123PK',
      ],
      'siteUuid' => 'test',
      'clientOptions' => [
        'baseUrl' => Server::$url,
      ],
    ]);

    Server::enqueue([
      new Response(200, [], json_encode($this->_resolvedObject))
    ]);

    $result = $bento->V1->Commands->unsubscribe([
      'email' => 'test@bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"command":{"command":"unsubscribe","email":"test@bentonow.com"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($this->_resolvedObject, $result);
  }
}
