<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 10/01/2019
 * Time: 12:00
 */

namespace Domaine;

/**
 * Class QueryToPdo
 * @package Domaine
 */
abstract class QueryToPdo extends Query
{
    use PdoAccess;

    /**
     * QueryToPdo constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
