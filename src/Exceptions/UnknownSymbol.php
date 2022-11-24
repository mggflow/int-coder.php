<?php

namespace MGGFLOW\IntObfuscator\Exceptions;

class UnknownSymbol extends \Exception
{
    protected $message = 'Code has unknown symbol.';
}