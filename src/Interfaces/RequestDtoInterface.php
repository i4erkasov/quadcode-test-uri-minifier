<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RequestDtoInterface
{
    /**
     * RequestDtoInterface constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request);
}
