<?php declare(strict_types=1);

namespace Aguimaraes\Bureaucrat;

class CircuitBreakerOpenException extends \Exception
{
    /**
     * @var CircuitBreaker
     */
    private $circuitBreaker;

    public function __construct(CircuitBreaker $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
    }
}
