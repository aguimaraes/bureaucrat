<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Aguimaraes\Bureaucrat\Retry;
use Aguimaraes\Bureaucrat\TimeUnit;
use Aguimaraes\Bureaucrat\CircuitBreaker;
use Aguimaraes\Bureaucrat\FailSafe;

class FailSafeTest extends TestCase
{
    public function testHappyPathWithCircuitBreakerAndRetry()
    {
        $this->markTestSkipped('Should go back to this');
        $callable = function ($arg) {
            return $arg;
        };

        $retry = (new Retry())
            ->onlyOnException(\RuntimeException::class)
            ->atLeast(3)
            ->withDelay(2, TimeUnit::SECOND)
            ->abortOnException(\DomainException::class);

        $circuitBreaker = (new CircuitBreaker())
            ->withFailureThreshold(3, 5)
            ->withSuccessThreshold(4, 5)
            ->withDelay(20, TimeUnit::SECOND)
            ->failOnException(\RuntimeException::class)
            ->failOnTimeOut(1, TimeUnit::MINUTE);

        $result = (new Failsafe())
            ->with($retry)
            ->and($circuitBreaker)
            ->run($callable, [true]);

        $this->assertTrue($result);
    }
}
