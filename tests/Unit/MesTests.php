<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MesTests extends TestCase
{
    

    public function testBasicTest()
    {
        $data = 'Je suis petit';
        $this->assertTrue(Str::startsWith($data, 'Je'));
        $this->assertFalse(Str::startsWith($data, 'Tu'));
        $this->assertSame(Str::startsWith($data, 'Tu'), false);
        $this->assertStringStartsWith('Je', $data);
        $this->assertStringEndsWith('petit', $data);
    }

    public function UnAutreTest()
    {
      
        $data = "N'jie";
        $result = strtoupper($data);
        // Assert (vérification)
        $this->assertEquals("N'JIE", $result);

    }

    public function testUnAutreTest()
{
    $data = "N'jie";

    $this->assertIsString($data);
    $this->assertEquals("N'jie", $data);
}


}
