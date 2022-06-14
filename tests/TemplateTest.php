<?php

require_once 'common.php';

use deemru\TemplateLibrary;

class TemplateTest extends PHPUnit\Framework\TestCase
{
    public function testSum(): void
    {
        $this->assertSame( 5, TemplateLibrary::sum( 1, 4 ) );
        $this->assertNotSame( 5, TemplateLibrary::sum( 2, 4 ) );
    }
}