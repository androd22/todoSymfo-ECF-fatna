<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class TestExample extends TestCase
{

    public function testExampleAddition(){
        $a = 2;
        $b = 18;
        $c = $a + $b;
        $this->assertEquals(20, $c, 'test');
    }

}