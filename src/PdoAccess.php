<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/01/2019
 * Time: 10:55
 */

namespace Domaine;

trait PdoAccess
{
    /**
     * pdo connexion
     * @var \PDO
     */
    protected $pdo;
    /**
     * pdoCharset UTF-8|CP1252|ISO-8859-15|ISO-8859-1|ASCII
     * @var string
     */
    protected $pdoCharset = 'UTF-8';
    protected $fetchType = \PDO::FETCH_ASSOC;
    /**
     * memorisation des pdostatemnts en cas de reutilisation multiple
     * @var \PDOStatement[]
     */
    private $pdoStatements = [];
    /**
     * requête SQL
     * @var string
     */
    private $reqSql;

    /**
     * donne le nom du driver
     * @return string
     */
    protected function getDriver(): string
    {
        return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * retourne un enregistrement en fonction de paramètres
     * @param array $params
     * @return \stdClass|array|null
     * @throws \Exception
     */
    protected function fetch(array $params)
    {
        $stm = $this->execute($params);
        $fetch = $stm->fetch($this->fetchType);
        if ($fetch === false) {
            return null;
        }
        return $fetch;
    }

    /**
     * exécute la requète en fonction de paramètre
     * @param array $params
     * @return \PDOStatement
     * @throws \Exception
     */
    protected function execute(array $params): \PDOStatement
    {
        $req = $this->getReqSql();
        $stm = $this->prepare($req);
        if (empty($params)) {
            $ok = $stm->execute();
        } else {
            for ($i = 0; $i < count($params); $i++) {
                if (is_string($params[$i])) {
                    $val = $params[$i];
                    $params[$i] = $this->corrigeInputEncoding($val);
                }
            }
            $ok = $stm->execute($params);
        }
        if (!$ok) {
            throw new \Exception(
                "Impossible de d'exécuter une requête dans "
                . static::class
            );
        }
        return $stm;
    }

    /**
     * @return mixed
     */
    private function getReqSql()
    {
        return $this->reqSql;
    }

    /**
     * @param mixed $reqSql
     */
    protected function setReqSql($reqSql): void
    {
        $this->reqSql = $reqSql;
    }

    /**
     * prépare une requète
     * @param string $req
     * @return \PDOStatement
     * @throws \Exception
     */
    private function prepare(string $req): \PDOStatement
    {
        $ref = md5($req);
        if (!isset($this->pdoStatements[$ref])) {
            $stm = $this->pdo->prepare($req);
            if ($stm === false) {
                throw new \Exception(
                    "Impossible de d'initialiser une requête dans "
                    . static::class
                );
            }
            $this->pdoStatements[$ref] = $stm;
        }
        return $this->pdoStatements[$ref];
    }

    /**
     * corrige l'encodage d'une chaine
     * @param string $string
     * @return string
     */
    private function corrigeInputEncoding($string)
    {
        if (!is_string($string)) {
            return $string;
        }
        $encodageSupporte = [];
        $encodageSupporte[] = "UTF-8";
        $encodageSupporte[] = "CP1252";
        $encodageSupporte[] = "ISO-8859-15";
        $encodageSupporte[] = "ISO-8859-1";
        $encodageSupporte[] = "ASCII";
        $encodageDetecte = mb_detect_encoding($string, $encodageSupporte, true);
        if ($encodageDetecte != $this->pdoCharset) {
            return mb_convert_encoding(
                $string,
                $this->pdoCharset,
                $encodageDetecte
            );
        }
        return $string;
    }

    /**
     * retourne un ensemble d'enregistrement en fonction de paramètres
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function fetchAll(array $params): array
    {
        $stm = $this->execute($params);
        $fetchAll = $stm->fetchAll($this->fetchType);
        return $fetchAll;
    }
}
