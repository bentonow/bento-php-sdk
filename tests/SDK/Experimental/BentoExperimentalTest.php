<?php

namespace bentonow\Bento\Tests\SDK\Commands;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Server\Server;
use PHPUnit\Framework\TestCase;
use bentonow\Bento\BentoAnalytics;

final class BentoExperimentalTest extends TestCase
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

  public function testValidateEmailWorksWithAnEmail()
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
      new Response(200, [], json_encode(['valid' => true]))
    ]);

    $result = $bento->V1->Experimental->validateEmail([
      'email' => 'test@bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"email":"test@bentonow.com","ip":null,"name":null,"user_agent":null,"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals(true, $result);
  }

  public function testValidateEmailFailsWith0000()
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
      new Response(200, [], json_encode(['valid' => false]))
    ]);

    $result = $bento->V1->Experimental->validateEmail([
      'email' => 'test@bentonow.com',
      'ip' => '0.0.0.0'
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"email":"test@bentonow.com","ip":"0.0.0.0","name":null,"user_agent":null,"site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals(false, $result);
  }

  public function testGuessGenderWorksWithMale()
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

    $returnObject = [
      'confidence' => 0.9631336405529953,
      'gender' => 'male'
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->guessGender([
      'name' => 'Jesse',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"name":"Jesse","site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testGuessGenderWorksWithFemale()
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

    $returnObject = [
      'confidence' => 0.9230769230769231,
      'gender' => 'female'
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->guessGender([
      'name' => 'Barb',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"name":"Barb","site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testGuessGenderWorksWithUnknown()
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

    $returnObject = [
      'confidence' => null,
      'gender' => 'unknown'
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->guessGender([
      'name' => 'Who?',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      '{"name":"Who?","site_uuid":"test"}',
      $requests[0]->getBody()->getContents()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testGeolocateReturnsNullFor127001()
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

    $returnObject = null;

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->geolocate([
      'ip' => '127.0.0.1',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'ip=127.0.0.1&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testGeolocateWorksWithOtherIPAddress()
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

    $returnObject = [
      'ip' => 'XXX.XX.XXX.XX',
      'request' => 'XXX.XX.XXX.XX',
      'latitude' => 0.0,
      'city_name' => 'Earth',
      'longitude' => 0.0,
      'postal_code' => '00000',
      'region_name' => '00',
      'country_name' => 'Country',
      'country_code2' => 'CO',
      'country_code3' => 'COU',
      'continent_code' => 'EA',
      'real_region_name' => 'Earth',
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->geolocate([
      'ip' => '0.0.0.0',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'ip=0.0.0.0&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testBlacklistReturnsABlacklistForAnyIPAddress()
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

    $returnObject = [
      'query' => '127.0.0.1',
      'description' => 'If any of the following blacklist providers contains true you have a problem on your hand.',
      'results' => [
        'spamhaus' => false,
        'nordspam' => true,
      ]
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->checkBlacklist([
      'ip' => '127.0.0.1',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'ip=127.0.0.1&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnObject, $result);
  }

  public function testBlacklistReturnsNoBlacklistForADomain()
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

    $returnObject = [
      'query' => 'bentonow.com',
      'description' => 'If any of the following blacklist providers contains true you have a problem on your hand.',
      'results' => [
        'just_registered' => false,
        'spamhaus' => false,
        'nordspam' => false,
        'spfbl' => false,
        'sorbs' => false,
        'abusix' => false,
      ]
    ];

    Server::enqueue([
      new Response(200, [], json_encode($returnObject))
    ]);

    $result = $bento->V1->Experimental->checkBlacklist([
      'ip' => 'bentonow.com',
    ]);

    $requests = Server::received();

    $this->assertEquals(
      'ip=bentonow.com&site_uuid=test',
      $requests[0]->getUri()->getQuery()
    );

    $this->assertEquals($returnObject, $result);
  }
}
