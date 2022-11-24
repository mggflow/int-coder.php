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

    protected function initStartPositions()
    {
        $this->startCodePositions = [];
        for ($i = 0; $i < $this->codeLength; $i++) {
            $this->startCodePositions[$i] = $i % $this->alphabetAmount;
        }
    }

    protected function mirrorSerialNumber()
    {
        if ($this->serialNumber % 2 == 1) {
            $this->serialNumber = $this->maxInt - $this->minInt + 1 - $this->serialNumber;
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