<?php

namespace Litbee\Access;

use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    public function testGranted()
    {
        $button = new Button();
        $this->assertTrue($button->isGranted());
    }
}
?>