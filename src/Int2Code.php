<?php

namespace MGGFLOW\IntCoder;

use MGGFLOW\IntCoder\Exceptions\OutOfRange;

class Int2Code extends CoderBase
{
    protected int $number;
    protected string $code;
    protected int $codePosition;
    protected int $alphabetIndex;
    protected int $displacedAlphabetIndex;

    public function encode(int $number): string
    {
        $this->setNumber($number);
        $this->checkNumberInRange();
        $this->calcIntegerSerialNumber();
        $this->resetCode();

        $this->fillCode();

        return $this->getCode();
    }

    protected function setNumber(int $number)
    {
        $this->number = $number;
    }

    protected function checkNumberInRange()
    {
        if ($this->number < $this->minInt or $this->number > $this->maxInt) {
            throw new OutOfRange();
        }
    }

    protected function calcIntegerSerialNumber()
    {
        $this->serialNumber = $this->number - $this->minInt;
        $this->mirrorSerialNumber();
    }

    protected function resetCode()
    {
        $this->code = '';
    }

    protected function fillCode()
    {
        for ($this->resetCodePosition(); $this->codePositionAllowable(); $this->incCodePosition()) {
            $this->setBeforeLastCodeSymbol();
        }
        $this->setLastCodeSymbol();
    }

    protected function setLastCodeSymbol()
    {
        $this->setZeroCodePosition();
        $this->calcLastAlphabetIndex();
        $this->calcDisplacedAlphabetIndex();
        $this->addCodeSymbol();
    }

    protected function setBeforeLastCodeSymbol()
    {
        $this->calcBeforeLastAlphabetIndex();
        $this->calcDisplacedAlphabetIndex();
        $this->addCodeSymbol();
    }

    protected function setZeroCodePosition()
    {
        $this->codePosition = 0;
    }

    protected function calcLastAlphabetIndex()
    {
        $this->alphabetIndex = $this->serialNumber % $this->alphabetAmount;
    }

    protected function calcBeforeLastAlphabetIndex()
    {
        $divider = $this->alphabetAmount ** ($this->codeLength - $this->codePosition);

        if ($divider > $this->serialNumber) {
            $this->alphabetIndex = 0;
            return;
        }

        $this->alphabetIndex = intdiv(
            $this->serialNumber, $divider
        );
    }

    protected function calcDisplacedAlphabetIndex()
    {
        $this->displacedAlphabetIndex = (
                $this->startCodePositions[$this->codePosition] + $this->alphabetIndex
            ) % $this->alphabetAmount;
    }

    protected function addCodeSymbol()
    {
        $this->code .= $this->alphabet[$this->displacedAlphabetIndex];
    }

    protected function getCode(): string
    {
        return $this->code;
    }
}