<?php

namespace Bento\Test;

use PHPUnit\Framework\TestCase;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

final class BentoClientTest extends TestCase
{
    protected $bento;
    protected $mock;

    public function setUp(): void
    {
        $this->mock = new MockHandler();



        $this->bento = new \Bento('123ABC');
    }

    public function testIdentifySuccessful()
    {
        $this->mock->append(new Response(202, ['Content-Length' => 0]));
        $this->push->identify('user@example.com');
        $this->assertEquals('/tracking/zapier', $this->mock->getLastRequest()->getUri()->getPath());
    }
}
