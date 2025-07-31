<?php

namespace App\ValueObjects;

class ClassRegisterStatus
{
    public bool $canRegister;
    public string $description;

    public function __construct()
    {
        $this->canRegister = false;
        $this->description = '';
    }
}
