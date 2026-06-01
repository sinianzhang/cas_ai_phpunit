<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\Email;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\BackupStaticProperties;

#[BackupStaticProperties(true)]
final class EmailTest extends TestCase
{
    private static $counter = 0;
    
    public function testCanBeCreatedFromValidEmail(): void
    {
        $string = 'user@example.org';

        $email = Email::fromString($string);

        $this->assertSame($string, $email->asString());
    }

    public function testCannotBeCreatedFromInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testVariableA(): void
    {
        self::$counter = 5;
        $this->assertSame(5, self::$counter);
    }

    public function testVariableB(): void
    {
        // Ohne #[BackupStaticProperties(true)] wäre self:$counter hier immer noch 5!
        $this->assertSame(0, self::$counter);
    }
}