<?php declare(strict_types=1);

namespace Aguimaraes\Bureaucrat;

interface PolicyInterface
{
    public function run(callable $operation, $args = []);
}
