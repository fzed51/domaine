<?php
declare(strict_types=1);

namespace Domaine;

/**
 * User: Fabien Sanchez
 * Date: 08/01/2019
 * Time: 10:44
 */
interface CommandInterface
{
    public function value();
    public function __invoke();
}