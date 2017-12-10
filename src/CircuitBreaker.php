<?php declare(strict_types=1);

namespace Aguimaraes\Bureaucrat;

class CircuitBreaker implements PolicyInterface
{
    private $failureThreshold;
    private $failureAttempts;

    private $successThreshold;
    private $successAttempts;

    private $delay;

    private $on = [];

    private $timeout;

    public function isAvailable() : bool
    {
        return true;
    }

    public function withFailureThreshold(int $threshold, int $attempts = null) : CircuitBreaker
    {
        $this->failureThreshold = $threshold;
        $this->failureAttempts = $attempts;

        return $this;
    }

    public function withSuccessThreshold(int $threshold, int $attempts = null) : CircuitBreaker
    {
        $this->successThreshold = $threshold;
        $this->successAttempts = $attempts;

        return $this;
    }

    public function withDelay(int $delay, int $unit) : CircuitBreaker
    {
        $this->delay = $delay * $unit;

        return $this;
    }

    public function failOnException(string $exception) : CircuitBreaker
    {
        $this->on = \array_merge($this->on, [$exception]);

        return $this;
    }

    public function failOnTimeOut(int $timeout, int $unit) : CircuitBreaker
    {
        $this->timeout = $timeout * $unit;

        return $this;
    }

    public function run(callable $operation, $args = [])
    {
        // TODO: Implement run() method.
    }
}
