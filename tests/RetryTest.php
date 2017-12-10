<?php declare(strict_types=1);

namespace Tests;

use Aguimaraes\Bureaucrat\Retry;
use Aguimaraes\Bureaucrat\TimeUnit;
use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    public function testWithoutRetrying()
    {
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter) {
            return $testCounter += $arg;
        };

        $retry = new Retry();

        $result = $retry->run($callable, [1]);

        $this->assertEquals(1, $result);
        $this->assertFalse($retry->isInsideRetryLimit());
    }

    public function testRetryingOnce()
    {
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter) {
            $testCounter += $arg;
            if ($testCounter <= 1) {
                throw new \Exception();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->once();

        $result = $retry->run($callable, [1]);

        $this->assertEquals(2, $result);
        $this->assertFalse($retry->isInsideRetryLimit());
        $this->assertEquals(2, $testCounter);
    }

    public function testRetryingTwice()
    {
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter) {
            $testCounter += $arg;
            if ($testCounter <= 2) {
                throw new \Exception();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->twice();

        $result = $retry->run($callable, [1]);

        $this->assertEquals(3, $result);
        $this->assertFalse($retry->isInsideRetryLimit());
        $this->assertEquals(3, $testCounter);
    }

    public function testRetryingAtLeastThreeTimes()
    {
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter) {
            $testCounter += $arg;
            if ($testCounter <= 3) {
                throw new \Exception();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->atLeast(3);

        $result = $retry->run($callable, [1]);

        $this->assertEquals(4, $result);
        $this->assertFalse($retry->isInsideRetryLimit());
        $this->assertEquals(4, $testCounter);
    }

    public function testRetryingForever()
    {
        $limit = random_int(10, 100);
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter, $limit) {
            $testCounter += $arg;
            if ($testCounter < $limit) {
                throw new \Exception();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->forever();

        $result = $retry->run($callable, [1]);

        $this->assertEquals($limit, $result);
        $this->assertTrue($retry->isInsideRetryLimit());
        $this->assertEquals($limit, $testCounter);
    }

    public function testRetryForeverOnException()
    {
        $limit = random_int(10, 100);
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter, $limit) {
            $testCounter += $arg;
            if ($testCounter < $limit) {
                throw new \RuntimeException();
            }

            if ($testCounter <= $limit) {
                throw new \DomainException();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->forever()
            ->onlyOnException(\RuntimeException::class);

        $this->expectException(\DomainException::class);
        $result = $retry->run($callable, [1]);

        $this->assertEquals($limit, $result);
        $this->assertTrue($retry->isInsideRetryLimit());
        $this->assertEquals($limit, $testCounter);
    }

    public function testRetryingOnceWithDelay()
    {
        $testCounter = 0;
        $firstTime = microtime(true);
        $secondTime = null;
        $callable = function (int $arg) use (&$testCounter, &$secondTime) {
            $testCounter += $arg;
            if ($testCounter < 2) {
                throw new \Exception();
            }
            $secondTime = microtime(true);

            return $testCounter;
        };

        $retry = (new Retry())
            ->once()
            ->withDelay(250000, TimeUnit::MICROSECOND);

        $retry->run($callable, [1]);

        $this->assertGreaterThan(0.25, $secondTime - $firstTime);
        $this->assertFalse($retry->isInsideRetryLimit());
        $this->assertEquals(2, $testCounter);
    }

    public function testRetryingAndAbortingOnSpecificException()
    {
        $testCounter = 0;
        $callable = function (int $arg) use (&$testCounter) {
            $testCounter += $arg;
            if ($testCounter <= 2) {
                throw new \Exception();
            }

            if ($testCounter <= 3) {
                throw new \RuntimeException();
            }

            return $testCounter;
        };

        $retry = (new Retry())
            ->atLeast(3)
            ->abortOnException(\RuntimeException::class);

        $this->expectException(\RuntimeException::class);
        $retry->run($callable, [1]);

        $this->assertTrue($retry->isInsideRetryLimit());
        $this->assertEquals(3, $testCounter);
    }
}
