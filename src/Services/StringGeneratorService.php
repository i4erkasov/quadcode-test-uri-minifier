<?php

namespace App\Services;

class StringGeneratorService
{
    private string $chars;

    /**
     * StringGeneratorService constructor.
     *
     * @param string $chars
     */
    public function __construct(string $chars)
    {
        $this->chars = $chars;
    }

    /**
     * @param int $length
     *
     * @throws \Exception
     * @return string
     */
    public function generateString(int $length): string
    {
        $lengthChar = $this->getLengthChars();

        while ($length-- > 0) {
            $string = ($string ?? '') . $this->chars[random_int(0, $lengthChar - 1)];
        }

        return $string ?? '';
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
        return strlen($this->chars);
    }
}