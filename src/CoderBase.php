<?php

namespace MGGFLOW\IntCoder;

use MGGFLOW\IntCoder\Exceptions\FailedCodingUniqueness;

class CoderBase
{
    protected int $minInt;
    protected int $maxInt;
    protected array $alphabet;
    protected int $codeLength;

    protected int $alphabetAmount;
    protected int $intRange;
    protected array $startCodePositions;
    protected int $serialNumber;
    protected int $codePosition;

    /**
     * @param int $minInt
     * @param int $maxInt
     * @param array $alphabet
     * @param int $codeLength
     * @throws FailedCodingUniqueness
     */
    public function __construct(
        int   $minInt, int $maxInt,
        array $alphabet, int $codeLength
    )
    {
        $this->minInt = $minInt;
        $this->maxInt = $maxInt;
        $this->alphabet = array_unique($alphabet);
        $this->codeLength = $codeLength;

        $this->calcAlphabetAmount();;
        $this->checkCodingUniqueness();
        $this->calcIntRange();
        $this->initStartPositions();
    }

    protected function calcAlphabetAmount()
    {
        $this->alphabetAmount = count($this->alphabet);
    }

    protected function checkCodingUniqueness()
    {
        if (pow($this->alphabetAmount, $this->codeLength) < ($this->maxInt - $this->minInt + 1)) {
            throw new FailedCodingUniqueness();
        }
    }

    protected function calcIntRange() {
        $this->intRange = $this->maxInt - $this->minInt;
    }

    protected function initStartPositions()
    {
        $this->startCodePositions = [];
        for ($i = 0; $i < $this->codeLength; $i++) {
            $this->startCodePositions[$i] = (($i ** 2) + ($i * 3) + ($i % 2)) % $this->alphabetAmount;
        }
    }

    protected function mirrorSerialNumber()
    {
        if ($this->serialNumber % 2 == 1) {
            $this->serialNumber = $this->intRange - $this->serialNumber + $this->intRange % 2;
        }
    }

    protected function resetCodePosition()
    {
        $this->codePosition = 1;
    }

    protected function incCodePosition()
    {
        $this->codePosition++;
    }

    protected function codePositionAllowable(): bool
    {
        return $this->codePosition < $this->codeLength;
    }
}