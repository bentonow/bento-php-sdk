<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoFieldsTest extends TestCase
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

  public function testGetFieldsReturnsFields()
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
        'id' => '2327',
        'type' => 'visitors-fields',
        'attributes' => [
          'name' => 'Phone',
          'key' => 'phone',
          'whitelisted' => null,
          'created_at' => '2021-08-21T02:08:30.364Z',
        ],
      ],
      [
        'id' => '2326',
        'type' => 'visitors-fields',
        'attributes' => [
          'name' => 'Last Name',
          'key' => 'last_name',
          'whitelisted' => null,
          'created_at' => '2021-08-21T02:08:30.356Z',
        ],
      ],
      [
        'id' => '2325',
        'type' => 'visitors-fields',
        'attributes' => [
          'name' => 'First Name',
          'key' => 'first_name',
          'whitelisted' => null,
          'created_at' => '2021-08-21T02:08:30.344Z',
        ],
      ]
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Fields->getFields();

    $requests = Server::received();

    $this->assertEquals(
      'GET',
      $requests[0]->getMethod()
    );

    $this->assertEquals($returnData, $result);
  }

  public function testPostFieldsWorksWithAKey()
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
        'id' => '2327',
        'type' => 'visitors-fields',
        'attributes' => [
          'name' => 'Test',
          'key' => 'test',
          'whitelisted' => null,
          'created_at' => '2021-08-21T02:08:30.364Z',
        ],
      ],
    ];

    Server::enqueue([
      new Response(200, [], json_encode(['data' => $returnData]))
    ]);

    $result = $bento->V1->Fields->createField([
      'key' => 'test'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"field":{"key":"test"},"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnData, $result);
  }
}
