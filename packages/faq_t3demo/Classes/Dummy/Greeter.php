<?php 

declare(strict_types=1);

namespace B13\FaqT3demo\Dummy;

final class Greeter
{
    public function greet(string $name): string
    {
        return 'Hello, ' . $name . '!';
    }

    public function greetWithTimeOfDay(string $name, int $hour): string
    {
        if ($hour <= 12) {
            return 'Good morning, ' . $name . '!';
        }

        if ($hour <= 18) {
            return 'Good afternoon, ' . $name . '!';
        }

        return 'Good evening, ' . $name . '!';
    }
}