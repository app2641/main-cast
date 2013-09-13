<?php

namespace Cast;

class Database extends \PDO
{
   // Database drivers that support SAVEPOINTs.
    protected static $savepointTransactions = array("pgsql", "mysql");

    // The current transaction level.
    protected $transLevel = 0;

    protected function nestable() {
        return in_array($this->getAttribute(\PDO::ATTR_DRIVER_NAME),
                        self::$savepointTransactions);
    }

    public function beginTransaction() {
        if(!$this->nestable() || $this->transLevel == 0) {
            parent::beginTransaction();
        } else {
            $this->exec("SAVEPOINT {$this->transLevel}save");
        }

        $this->transLevel++;
    }

    public function commit() {
        $this->transLevel--;

        if(!$this->nestable() || $this->transLevel == 0) {
            parent::commit();
        } else {
            $this->exec("RELEASE SAVEPOINT {$this->transLevel}save");
        }
    }

    public function rollBack() {
        $this->transLevel--;

        if(!$this->nestable() || $this->transLevel == 0) {
            parent::rollBack();
        } else {
            $this->exec("ROLLBACK TO SAVEPOINT {$this->transLevel}save");
        }
    }


    // sql処理の実行
    public function state ($sql, $bind = array())
    {
        // stdclassの場合はarrayにキャスト
        if ($bind instanceof \stdClass) {
            $bind = (array) $bind;
        }

        if (! is_array($bind)) {
            $bind = array($bind);
        }


        $stmt = $this->prepare($sql);
        $stmt->execute($bind);

        return $stmt;
    }
}
