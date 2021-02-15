<?php

namespace bentonow\Bento\Test;






use bentonow\Bento\Bento;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;

final class BentoClientTest extends TestCase
{
    protected $bento;
    protected $mock;

    public function setUp(): void
    {
        $this->mock = new MockHandler();

        $this->bento = new Bento('123ABC');
    }

    public function testIdentifySuccessful()
    {
        $this->mock->append(new Response(202, ['Content-Length' => 0]));

        $this->bento->identify('user@example.com');

        $this->assertEquals('user@example.com', $this->bento->getEmail());
    }
}
