<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoTagsTest extends TestCase
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

  public function testGetTagsWorksWithoutAnyParameters()
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
      [
        'id' => '174',
        'type' => 'tags',
        'attributes' => [
          'name' => 'test1',
          'created_at' => '2021-04-09T01:29:46.385Z',
          'discarded_at' => null,
        ],
      ]
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Tags->getTags();

    $requests = Server::received();

    $this->assertEquals(
      'site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnData, $result);
  }

  public function testPostTagsWorksWithAName()
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
      [
        'id' => '174',
        'type' => 'tags',
        'attributes' => [
          'name' => 'test tag',
          'created_at' => '2021-04-09T01:29:46.385Z',
          'discarded_at' => null,
        ],
      ]
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Tags->createTag([
      'name' => 'test tag'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"tag":{"name":"test tag"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnData, $result);
  }
}
