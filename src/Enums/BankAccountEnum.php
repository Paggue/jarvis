<?php

namespace Lara\Jarvis\Enums;

abstract class BankAccountEnum
{
    const STATUS = ['created' => 0, 'invalid' => 1, 'confirmed' => 2, 'processing' => 3, 'blocked' => 4];
}

