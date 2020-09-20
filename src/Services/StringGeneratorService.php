<?php

namespace App\Services;

/**
 * Данный класс выступает в виде сервиса для генерации уникального "code".
 * Алгоримт генерации кода основан на алгоритме base-36 с использование
 * рандомно раставленных символов
 */
class StringGeneratorService
{
    private const CHARS = 'gfshilm9onb3ywcd45q6prv0jkt18uxz27';

    /**
     * @param int $digit
     * @param int $length
     *
     * @throws \Exception
     * @return string
     */
    public function generateCode(int $digit, int $length): string
    {
        $lengthChar = $this->getLengthChars();

        do {
            $string = self::CHARS[($digit % $lengthChar)] . ($string ?? '');
            $digit  = (int)($digit / $lengthChar);
        } while ($digit != 0);

        return str_pad($string, $length, '0', STR_PAD_LEFT);
    }

    /**
     * @param int $length
     *
     * @return int
     */
    public function getLimits(int $length): int
    {
        $lengthChars = $this->getLengthChars();

        return $lengthChars ** $length;
    }

    /**
     * @return int
     */
    private function getLengthChars(): int
    {
        return strlen(self::CHARS);
    }
}