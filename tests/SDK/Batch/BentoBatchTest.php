<?php

namespace bentonow\Bento\Tests\SDK\Batch;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;
use bentonow\Bento\SDK\Batch\BentoEvents;

final class ClientTest extends TestCase
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

  public function testCanImportBetween1And1000Subscribers()
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
        'results' => 3,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->Batch->importSubscribers([
      'subscribers' => [
        [
          'email' => 'phpbatchsubscribertest-01',
          'age' => 21
        ],
        [
          'email' => 'phpbatchsubscribertest-02'
        ],
        [
          'email' => 'phpbatchsubscribertest-03',
          'name' => 'Third Test'
        ],
      ]
    ]);

    $this->assertEquals(3, $result->results);
    $this->assertEquals(0, $result->failed);
  }

  public function testCanImportBetween1And1000Events()
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
        'results' => 3,
        'failed' => 0
      ]))
    ]);

    $result = $bento->V1->Batch->importEvents([
      'events' => [
        [
          'email' => 'phpbatchsubscribertest-01',
          'type' => BentoEvents::SUBSCRIBE
        ],
        [
          'email' => 'phpbatchsubscribertest-02',
          'type' => BentoEvents::UNSUBSCRIBE
        ],
        [
          'email' => 'phpbatchsubscribertest-03',
          'details' => [
            'tag' => 'Teg Tag'
          ],
          'type' => BentoEvents::TAG
        ],
      ]
    ]);

    $this->assertEquals(3, $result->results);
    $this->assertEquals(0, $result->failed);
  }
}
