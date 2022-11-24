<?php

namespace Tests;

use MGGFLOW\IntObfuscator\Obfuscator;
use PHPUnit\Framework\TestCase;

class ObfuscatorTest extends TestCase
{
    public function testFull()
    {
        $min = 23537;
        $max = 2 ** 16;
        $alphabet = [
            'a', 'b', 'c', 'd',
            'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p',
            'q', 'r', 's', 't',
            'u', 'v', 'w',
            'x', 'y', 'z',
            '0', '1', '2', '3',
            '4', '5', '6', '7',
            '8', '9'
        ];
        $codeLength = 8;

        $obfs = new Obfuscator($min, $max, $alphabet, $codeLength);

        $codes = [];
        for ($i = $min; $i <= $max; $i++) {
            $code = $obfs->encode($i);
            $decoded = $obfs->decode($code);

            $this->assertEquals($i, $decoded);
            $this->assertEquals(strlen($code), $codeLength);
            $this->assertNotContains($code, $codes);
            $codes[] = $code;
        }
    }
}