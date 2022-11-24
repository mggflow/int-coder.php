<?php

namespace MGGFLOW\IntObfuscator\Exceptions;

class WrongCodeLength extends \Exception
{
    protected $message = 'Code length is wrong';
}