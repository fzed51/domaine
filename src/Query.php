<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/01/2019
 * Time: 10:48
 */

namespace Domaine;

abstract class Query implements QueryInterface
{

    protected $value;
    private $initialisedValue = false;

    public function value()
    {
        if (!$this->initialisedValue) {
            throw  new \RuntimeException(
                static::class . "(Query) ne peut pas donner de valeur."
            );
        }
        return $this->value;
    }

    abstract public function __invoke();

    /**
     * Vide le contenu le la valeur de la Query
     */
    protected function clearValue()
    {
        $this->value = null;
        $this->initialisedValue = false;
    }

    protected function setValue($value)
    {
        $this->initialisedValue = true;
        $this->value = $value;
    }
}