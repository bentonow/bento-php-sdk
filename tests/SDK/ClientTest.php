<?php

namespace bentonow\Bento\Tests\SDK;

use bentonow\Bento\SDK\BentoClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;

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

  public function testClientGetSendsHeadersAndQueryParametersCorrectly()
  {
    $client = new BentoClient([
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
      new Response(200, ['Content-Length' => 0])
    ]);

    $client->get('/test', ['param' => 'value']);
    $requests = Server::received();

    $this->assertEquals(
      'application/json',
      $requests[0]->getHeader('Content-Type')[0]
    );
    $this->assertEquals(
      'Basic MTIzUEs6MTIzU0s=',
      $requests[0]->getHeader('Authorization')[0]
    );
    $this->assertEquals(
      'param=value&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );
  }

  public function testClientPostSendsHeadersAndQueryParametersCorrectly()
  {
    $client = new BentoClient([
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
      new Response(200, ['Content-Length' => 0])
    ]);

    $client->post('/test', ['param' => 'value']);
    $requests = Server::received();

    $this->assertEquals(
      'application/json',
      $requests[0]->getHeader('Content-Type')[0]
    );
    $this->assertEquals(
      'Basic MTIzUEs6MTIzU0s=',
      $requests[0]->getHeader('Authorization')[0]
    );
    $this->assertEquals(
      '{"param":"value","site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );
  }
}
