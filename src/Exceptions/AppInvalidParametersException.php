<?php

namespace App\Exceptions;

use Symfony\Component\Validator\Exception\ExceptionInterface;

class AppInvalidParametersException extends \InvalidArgumentException implements ExceptionInterface
{

}