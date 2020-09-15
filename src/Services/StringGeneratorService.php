<?php

namespace App\Services;

class StringGeneratorService
{
    private string $chars;

    public function __construct(string $chars)
    {
        $this->chars = $chars;
    }

    /**
     * @param $length
     *
     * @return string
     */
    public function generateString($length): string
    {
        $lengthCharSet = $this->getLengthCharSet();

        for ($i = 0; $i < $length; $i++) {
            $string = ($string ?? '') . $this->chars[mt_rand(0, $lengthCharSet - 1)];
        }

        return $string ?? '';
    }

    /**
     * @param int $length
     *
     * @return int
     */
    public function getCountOptions(int $length): int
    {
        $lengthCharSet = $this->getLengthCharSet();

        return $lengthCharSet ** $length;
    }

    /**
     * @return int
     */
    private function getLengthCharSet(): int
    {
        return strlen($this->chars);
    }
}