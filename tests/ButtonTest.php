<?php

namespace Litbee\Access;

use PHPUnit\Framework\TestCase;
use Mockery;

$_SESSION = [];

class ButtonTest extends TestCase
{
    public function testGranted()
    {
        // Simulate a button on a page

        $context = Mockery::mock(Context::class);
        $context->shouldReceive('contentId')->andReturn("content1");
        $context->shouldReceive('nonce')->andReturn(45);
        $context->shouldReceive('token')->andReturn(null);

        $session = Mockery::mock(Session::class);
        $session->shouldReceive('has')->andReturn(false);
        $session->shouldReceive('setItem');

        $content = new Content(50, $context, $session);

        $this->assertFalse($content->accessGranted());

        // Check access link

        $accessLink = $content->accessGateUrl();

        $protocol = new Protocol();

        $protocol->decodeRequest(urldecode(explode("=", $accessLink)[1]), $contentId, $nonce, $priceInCents);
        $this->assertEquals(sha1("content1"), $contentId);
        $this->assertEquals(45, $nonce);
        $this->assertEquals(50, $priceInCents);

        // Simulate valid token

        $context2 = Mockery::mock(Context::class);
        $context2->shouldReceive('contentId')->andReturn("content1");
        $context2->shouldReceive('token')->andReturn($protocol->createToken($contentId, $nonce));

        $session2 = Mockery::mock(Session::class);
        $session2->shouldReceive('has')->andReturn(true);
        $session2->shouldReceive('getItem')->andReturn(sha1("content1"), 45);

        $content2 = new Content(50, $context2, $session2);

        $this->assertTrue($content2->accessGranted());
    }

    public function testRenderButton() {
        $context = Mockery::mock(Context::class);
        $context->shouldReceive('contentId')->andReturn("content1");
        $context->shouldReceive('nonce')->andReturn(45);
        $context->shouldReceive('token')->andReturn(null);

        $session = Mockery::mock(Session::class);
        $session->shouldReceive('has')->andReturn(false);
        $session->shouldReceive('setItem');

        $content = new Content(70, $context, $session);

        $this->assertRegexp('/-.70/', $content->renderButton());
    }
}
?>