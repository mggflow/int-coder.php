<?php

namespace MGGFLOW\IntCoder\Exceptions;

class FailedCodingUniqueness extends \Exception
{
    protected $message = 'Impossible to encode the integers range.';
}