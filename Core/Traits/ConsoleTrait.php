<?php

namespace Core\Traits;

trait ConsoleTrait
{
    public function line(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
