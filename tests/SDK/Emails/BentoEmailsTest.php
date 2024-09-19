<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoEmailsTest extends TestCase
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

  public function testCreateEmails()
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

    $result = $bento->V1->Emails->createEmail([
        [
            'to' => 'test@bentonow.com',
            'from' => 'jesse@bentonow.com',
            'subject' => 'Reset Password',
            'html_body' => "<p>Here is a link to reset your password ... {{ link }}</>",
            'transactional' => true,
            'personalizations' => [
                'link' => 'https://example.com/test'
            ]
        ]
    ]);

    $requests = Server::received();

      $this->assertEquals(
          '{"emails":[{"to":"test@bentonow.com","from":"jesse@bentonow.com","subject":"Reset Password","html_body":"<p>Here is a link to reset your password ... {{ link }}<\/>","transactional":true,"personalizations":{"link":"https:\/\/example.com\/test"}}],"site_uuid":"test"}',
          $requests[0]->getBody()->getContents()
      );

    $this->assertEquals($returnData, $result);
  }

}
