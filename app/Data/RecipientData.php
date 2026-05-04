<?php

namespace App\Data;

class RecipientData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $zip,
        public string $city,
        public string $address_line,
        public string $phone,
        public array $billing

    ) {}
}