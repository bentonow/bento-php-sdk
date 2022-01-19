<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoSubscribersTest extends TestCase
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

  public function testGetSubscribersWorksWithoutAnyParameters()
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
      new Response(200, [], json_encode(['data' => null]))
    ]);

    $result = $bento->V1->Subscribers->getSubscribers();

    $requests = Server::received();

    $this->assertEquals(
      'site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals(null, $result);
  }

  public function testGetSubscribersWorksWithAUUID()
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

    $returnData = [
      'id' => '236',
      'type' => 'visitors',
      'attributes' => [
        'uuid' => '1234',
        'email' => 'jesse@bentonow.com',
        'fields' => [],
        'cached_tag_ids' => [],
      ],
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Subscribers->getSubscribers([
      'uuid' => '1234'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'uuid=1234&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnData, $result);
  }

  public function testGetSubscribersWorksWithAnEmail()
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

    $returnData = [
      'id' => '236',
      'type' => 'visitors',
      'attributes' => [
        'uuid' => '0f566d05f47a59bff25f147df3a6233d',
        'email' => 'test@bentonow.com',
        'fields' => [],
        'cached_tag_ids' => [],
      ],
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Subscribers->getSubscribers([
      'email' => 'test@bentonow.com'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'email=test%40bentonow.com&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnData, $result);
  }

  public function testPostSubscribersWorksWithAnEmail()
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

    $returnData = [
      'id' => '444792648',
      'type' => 'visitors',
      'attributes' => [
        'uuid' => '4b6bede6f4271f8d033ca9a2d4f365eb',
        'email' => 'test@bentonow.com',
        'fields' => null,
        'cached_tag_ids' => [],
        'unsubscribed_at' => null
      ],
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Subscribers->createSubscriber([
      'email' => 'test@bentonow.com'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"subscriber":{"email":"test@bentonow.com"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnData, $result);
  }
}
