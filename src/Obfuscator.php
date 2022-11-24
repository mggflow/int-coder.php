<?php

namespace MGGFLOW\IntObfuscator;

use MGGFLOW\IntObfuscator\Exceptions\FailedCodingUniqueness;
use MGGFLOW\IntObfuscator\Exceptions\OutOfRange;
use MGGFLOW\IntObfuscator\Exceptions\UnknownSymbol;
use MGGFLOW\IntObfuscator\Exceptions\WrongCodeLength;

class Obfuscator
{
    protected int $minInt;
    protected int $maxInt;
    protected array $alphabet;
    protected int $codeLength;

    protected int $alphabetAmount;
    protected array $startCodePositions;

    protected int $number;
    protected int $serialNumber;
    protected int $codePosition;
    protected int $alphabetIndex;
    protected int $displacedAlphabetIndex;

    protected array $codeArray;
    protected string $code;

    public function __construct(
        int   $minInt, int $maxInt,
        array $alphabet, int $codeLength
    )
    {
        $this->minInt = $minInt;
        $this->maxInt = $maxInt;
        $this->alphabet = $alphabet;
        $this->codeLength = $codeLength;

        $this->calcAlphabetAmount();;
        $this->checkCodingUniqueness();
        $this->initStartPositions();
    }

    public function encode(int $number): string
    {
        $this->setNumber($number);
        $this->checkNumberInRange();
        $this->calcIntegerSerialNumber();
        $this->resetCode();

        $this->fillCode();

        return $this->getCode();
    }

    public function decode(string $code): int
    {
        $this->setCode($code);
        $this->checkCodeLength();
        $this->makeCodeArray();
        $this->setZeroSerialNumber();

        $this->fillSerialNumber();

        return $this->calcShiftedSerialNumber();
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
        for ($i = 0; $i < $this->codeLength; $i++) {
            $this->startCodePositions[] = $i % $this->alphabetAmount;
        }
    }

    protected function setNumber(int $number)
    {
        $this->number = $number;
    }

    protected function checkNumberInRange(){
        if ($this->number < $this->minInt or $this->number > $this->maxInt){
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

    protected function resetCodePosition()
    {
        $this->codePosition = 1;
    }

    protected function incCodePosition()
    {
        $this->codePosition++;
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
        $this->alphabetIndex = intdiv(
            $this->serialNumber, $this->alphabetAmount ** ($this->codeLength - $this->codePosition)
        );
    }

    protected function codePositionAllowable(): bool
    {
        return $this->codePosition < $this->codeLength;
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

    protected function setCode(string $code)
    {
        $this->code = $code;
    }

    protected function checkCodeLength(){
        if (strlen($this->code) != $this->codeLength){
            throw new WrongCodeLength();
        }
    }

    protected function makeCodeArray()
    {
        $this->codeArray = str_split($this->code);
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
        $this->calcCodeDisplacedAplhabetIndex();
        $this->calcCodeAlphabetIndex();
        $this->correctAlphabetIndex();
        $this->incSerialNumber();
    }

    protected function consumeLastSymbol()
    {
        $this->setLastSymbolCodePosition();
        $this->calcCodeDisplacedAplhabetIndex();
        $this->calcCodeLastAlphabetIndex();
        $this->correctAlphabetIndex();
        $this->incSerialNumber();
    }

    protected function calcCodeDisplacedAplhabetIndex()
    {
        $this->displacedAlphabetIndex = $this->findAlphabetIndex($this->codeArray[$this->codePosition - 1]);
    }

    protected function setLastSymbolCodePosition()
    {
        $this->codePosition = $this->codeLength;
    }

    protected function calcCodeAlphabetIndex()
    {
        $this->alphabetIndex = $this->displacedAlphabetIndex - $this->startCodePositions[$this->codePosition];
    }

    protected function calcCodeLastAlphabetIndex()
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

    protected function mirrorSerialNumber() {
        if ($this->serialNumber % 2 == 1){
            $this->serialNumber = $this->maxInt - $this->minInt + 1 - $this->serialNumber;
        }
    }

    protected function findAlphabetIndex(string $symbol)
    {
        $index = array_search($symbol, $this->alphabet);
        if ($index === false) {
            throw new UnknownSymbol();
        }

        return $index;
    }
}