<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoEventsTest extends TestCase
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

    public function testPostEventSingleEvent()
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
                'results' => 1,
                'failed' => 0,
            ]
        ];

        Server::enqueue([
            new Response(200, [], json_encode(['data' => $returnData]))
        ]);

        $result = $bento->V1->Events->createEvents([
            [
                "type" => '$completed_onboarding',
                "email" => "test@test.com"
            ]
        ]);

        $requests = Server::received();
        $this->assertEquals(
            '{"events":[{"type":"$completed_onboarding","email":"test@test.com"}],"site_uuid":"test"}',
            $requests[0]->getBody()->getContents()
        );

        $this->assertEquals($returnData, $result);
    }

    public function testPostEventMultipleEvents()
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
                'results' => 2,
                'failed' => 0,
            ]
        ];

        Server::enqueue([
            new Response(200, [], json_encode(['data' => $returnData]))
        ]);

        $result = $bento->V1->Events->createEvents([
            [
                "type" => '$completed_onboarding',
                "email" => "test@test.com"
            ],
            [
                "type" => '$DownloadDownloaded',
                "email" => "test@test.com"
            ],
        ]);

        $requests = Server::received();

        $this->assertEquals(
            '{"events":[{"type":"$completed_onboarding","email":"test@test.com"},{"type":"$DownloadDownloaded","email":"test@test.com"}],"site_uuid":"test"}',
            $requests[0]->getBody()->getContents()
        );

        $this->assertEquals($returnData, $result);
    }
}
