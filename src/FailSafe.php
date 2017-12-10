<?php declare(strict_types=1);

namespace Aguimaraes\Bureaucrat;

class FailSafe
{
    /**
     * @var CircuitBreaker
     */
    private $circuitBreaker;

    /**
     * @var Retry
     */
    private $retry;

    /**
     * @param $policy
     *
     * @return FailSafe
     * @throws \InvalidArgumentException
     */
    public function with($policy) : FailSafe
    {
        if ($policy instanceof CircuitBreaker) {
            $this->circuitBreaker = $policy;

            return $this;
        }

        if ($policy instanceof Retry) {
            $this->retry = $policy;

            return $this;
        }

        throw new \InvalidArgumentException('Argument is an invalid policy');
    }

    /**
     * @param $policy
     *
     * @return FailSafe
     * @throws \InvalidArgumentException
     */
    public function and($policy) : FailSafe
    {
        return $this->with($policy);
    }

    public function run(callable $operation, array $args = [])
    {
        // TODO
    }
}
