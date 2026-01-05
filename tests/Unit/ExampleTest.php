<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function testBasicTest()
{
    $data = [10, 20, 30];
    $result = array_sum($data);
    $this->assertEquals(60, $result);
}
}
