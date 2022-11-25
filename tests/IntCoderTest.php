<?php

namespace Tests;

use MGGFLOW\IntCoder\Code2Int;
use MGGFLOW\IntCoder\Int2Code;
use PHPUnit\Framework\TestCase;

class IntCoderTest extends TestCase
{
    protected int $min;
    protected int $max;
    protected int $codeLength;

    protected Int2Code $encoder;
    protected Code2Int $decoder;

    protected array $codes;

    protected function setUp(): void
    {
        $this->min = 3537;
        $this->max = PHP_INT_MAX;
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
        $this->codeLength = 13;

        $this->encoder = new Int2Code($this->min, $this->max, $alphabet, $this->codeLength);
        $this->decoder = new Code2Int($this->min, $this->max, $alphabet, $this->codeLength);

        $this->codes = [];
    }

    public function testRangeStart()
    {
        for ($i = $this->min; $i <= $this->min + 10_000; $i++) {
            $code = $this->encoder->encode($i);
            $decoded = $this->decoder->decode($code);

            $this->check($i, $decoded, $code);
        }
    }

    public function testRangeMiddle()
    {
        for ($i = ((int)($this->max / 2) - 5_000); $i <= ((int)($this->max / 2) + 5_000); $i++) {
            $code = $this->encoder->encode($i);
            $decoded = $this->decoder->decode($code);

            $this->check($i, $decoded, $code);
        }
    }

    public function testRangeEnd()
    {
        for ($i = $this->max - 10_000; $i < $this->max; $i++) {
            $code = $this->encoder->encode($i);
            $decoded = $this->decoder->decode($code);

            $this->check($i, $decoded, $code);
        }
    }

    protected function check(int $number, int $decoded, string $code) {
        $this->assertEquals($number, $decoded);
        $this->assertEquals(strlen($code), $this->codeLength);
        $this->assertNotContains($code, $this->codes);
        $this->codes[] = $code;
    }
}