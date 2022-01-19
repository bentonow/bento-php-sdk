<?php

namespace bentonow\Bento\Tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;
use bentonow\Bento\SDK\Batch\BentoEvents;

final class BentoAnalyticsTest extends TestCase
{
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

  public function testCanTagSubscriber()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->tagSubscriber([
      'email' => 'test@bentonow.com',
      'tagName' => 'Test Tag',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":{"date":null,"details":{"tag":"Test Tag"},"email":"test@bentonow.com","type":"$tag"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }

  public function testCanSubscribeAnEmail()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->addSubscriber([
      'email' => 'test@bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":{"date":null,"email":"test@bentonow.com","type":"$subscribe","fields":[]},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }

  public function testCanUnsubscribeAnEmail()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->removeSubscriber([
      'email' => 'test@bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":{"date":null,"email":"test@bentonow.com","type":"$unsubscribe"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }

  public function testCanUpdateFieldsOnASubscriber()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->updateFields([
      'email' => 'test@bentonow.com',
      'fields' => [
        'firstName' => 'Test',
      ],
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":{"date":null,"email":"test@bentonow.com","type":"$update_fields","fields":{"firstName":"Test"}},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }

  public function testCanTrackPurchases()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->trackPurchase([
      'email' => 'test@bentonow.com',
      'purchaseDetails' => [
        'unique' => [
          'key' => 1234,
        ],
        'value' => [
          'amount' => 100,
          'currency' => 'USD',
        ],
        'cart' => [
          'abandoned_checkout_url' => ''
        ]
      ]
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":{"date":null,"email":"test@bentonow.com","type":"$purchase","details":{"unique":{"key":1234},"value":{"amount":100,"currency":"USD"},"cart":{"abandoned_checkout_url":""}}},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }

  public function testCanTrackCustomEvents()
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
      new Response(200, [], json_encode([
        'results' => 1,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->track([
      'email' => 'test@bentonow.com',
      'type' => '$custom.event',
      'details' => [
        'fromCustomEvent' => true,
      ],
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"events":[{"email":"test@bentonow.com","type":"$custom.event","details":{"fromCustomEvent":true}}],"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
    $this->assertEquals(true, $result);
  }
}
