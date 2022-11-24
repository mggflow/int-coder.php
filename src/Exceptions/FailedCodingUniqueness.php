<?php

namespace MGGFLOW\IntObfuscator\Exceptions;

class FailedCodingUniqueness extends \Exception
{
    protected $message = 'Impossible to encode the integers range.';
}