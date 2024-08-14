<?php 
declare(strict_types=1);
 
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
 
 
final class StackTest extends TestCase {

    #[Test]
 
    public function sumar(): void {
        $num1 = 1;
        $num2 = 2;
 
        // Comprobación de afirmaciones
        $this->assertSame(3, $num1 + $num2);
    }
 
}