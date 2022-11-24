<?php

namespace Tests;

use MGGFLOW\IntCoder\Code2Int;
use MGGFLOW\IntCoder\Int2Code;
use PHPUnit\Framework\TestCase;

class ObfuscatorTest extends TestCase
{
    public function testFull()
    {
        $min = 3537;
        $max = 2 ** 15;
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

        $coder = new Int2Code($min, $max, $alphabet, $codeLength);
        $decoder = new Code2Int($min, $max, $alphabet, $codeLength);

        $codes = [];
        for ($i = $min; $i <= $max; $i++) {
            $code = $coder->encode($i);
            $decoded = $decoder->decode($code);

            $this->assertEquals($i, $decoded);
            $this->assertEquals(strlen($code), $codeLength);
            $this->assertNotContains($code, $codes);
            $codes[] = $code;
        }
    }
}