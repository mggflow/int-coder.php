<?php

namespace MGGFLOW\IntCoder;

use MGGFLOW\IntCoder\Exceptions\UnknownSymbol;
use MGGFLOW\IntCoder\Exceptions\WrongCodeLength;

class Code2Int extends CoderBase
{
    private array $codeArray;
    protected int $displacedAlphabetIndex;
    protected int $alphabetIndex;

    public function decode(string $code): int
    {
        $this->createCodeArray($code);
        $this->checkCodeLength();
        $this->setZeroSerialNumber();

        $this->fillSerialNumber();

        return $this->calcShiftedSerialNumber();
    }

    protected function createCodeArray(string $code)
    {
        $this->codeArray = str_split($code);
    }

    protected function checkCodeLength()
    {
        if (count($this->codeArray) != $this->codeLength) {
            throw new WrongCodeLength();
        }
    }

    protected function setZeroSerialNumber()
    {
        $this->serialNumber = 0;
    }

    protected function fillSerialNumber()
    {
        for ($this->resetCodePosition(); $this->codePositionAllowable(); $this->incCodePosition()) {
            $this->consumeBeforeLastSymbol();
        }

        $this->consumeLastSymbol();

        $this->mirrorSerialNumber();
    }

    protected function consumeBeforeLastSymbol()
    {
        $this->calcDisplacedAplphabetIndex();
        $this->calcAlphabetIndex();
        $this->correctAlphabetIndex();
        $this->incSerialNumber();
    }

    protected function consumeLastSymbol()
    {
        $this->setLastSymbolPosition();
        $this->calcDisplacedAplphabetIndex();
        $this->calcLastAlphabetIndex();
        $this->correctAlphabetIndex();
        $this->incSerialNumber();
    }

    protected function calcDisplacedAplphabetIndex()
    {
        $this->displacedAlphabetIndex = $this->findAlphabetIndex();
    }

    protected function setLastSymbolPosition()
    {
        $this->codePosition = $this->codeLength;
    }

    protected function calcAlphabetIndex()
    {
        $this->alphabetIndex = $this->displacedAlphabetIndex - $this->startCodePositions[$this->codePosition];
    }

    protected function calcLastAlphabetIndex()
    {
        $this->alphabetIndex = $this->displacedAlphabetIndex - $this->startCodePositions[0];
    }

    protected function correctAlphabetIndex()
    {
        if ($this->alphabetIndex < 0) $this->alphabetIndex += $this->alphabetAmount;
    }

    protected function incSerialNumber()
    {
        $this->serialNumber += $this->alphabetIndex * $this->alphabetAmount ** ($this->codeLength - $this->codePosition);
    }

    protected function calcShiftedSerialNumber(): int
    {
        return $this->minInt + $this->serialNumber;
    }

    protected function mirrorSerialNumber()
    {
        if ($this->serialNumber % 2 == 1) {
            $this->serialNumber = $this->maxInt - $this->minInt + 1 - $this->serialNumber;
        }
    }

    protected function findAlphabetIndex()
    {
        $index = array_search($this->codeArray[$this->codePosition - 1], $this->alphabet);
        if ($index === false) {
            throw new UnknownSymbol();
        }

        return $index;
    }
}