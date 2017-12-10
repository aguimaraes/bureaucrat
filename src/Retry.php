<?php declare(strict_types=1);

namespace Aguimaraes\Bureaucrat;

class Retry implements PolicyInterface
{
    private $limit;

    private $delay = 0;

    private $on = [];

    private $abortOn = [];

    private $retries = 0;

    public function atLeast(int $limit) : Retry
    {
        $this->limit = $limit;

        return $this;
    }

    public function forever() : Retry
    {
        $this->limit = 0;

        return $this;
    }

    public function once() : Retry
    {
        $this->limit = 1;

        return $this;
    }

    public function twice() : Retry
    {
        $this->limit = 2;

        return $this;
    }

    public function withDelay(int $delay, int $unit) : Retry
    {
        $this->delay = $delay * $unit;

        return $this;
    }

    public function onlyOnException(string $exception) : Retry
    {
        $this->on = array_merge($this->on, [$exception]);

        return $this;
    }

    public function abortOnException(string $exception) : Retry
    {
        $this->abortOn = array_merge($this->abortOn, [$exception]);

        return $this;
    }

    public function isInsideRetryLimit()
    {
        if (is_null($this->limit) || ($this->limit > 0 && $this->retries >= $this->limit)) {
            return false;
        }

        return true;
    }

    public function isExpectedException(\Exception $e)
    {
        return empty($this->on) || in_array(get_class($e), $this->on);
    }

    public function isAbortableException(\Exception $e)
    {
        return !empty($this->abortOn) && in_array(get_class($e), $this->abortOn);
    }

    public function canRetry(\Exception $e)
    {
        return $this->isExpectedException($e)
            && $this->isInsideRetryLimit()
            && !$this->isAbortableException($e);
    }

    public function run(callable $operation, $args = [])
    {
        try {
            return $operation(...$args);
        } catch (\Exception $e) {
            if ($this->canRetry($e)) {
                if ($this->delay > 0) {
                    usleep($this->delay);
                }
                $this->retries++;

                return $this->run($operation, $args);
            }
            throw new $e;
        }
    }
}
