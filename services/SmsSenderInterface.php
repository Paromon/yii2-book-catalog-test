<?php

declare(strict_types=1);

namespace app\services;

interface SmsSenderInterface
{
    public function send(string $phone, string $message): void;
}
