<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

class TransactionCollection
{
    public $allEntities = [];

    /**
     * DataCollection constructor.
     *
     * @param array $entities
     */
    public function __construct($entities = [])
    {
        $this->parseData($entities);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->allEntities;
    }

    /**
     * @return array
     */
    public function each(callable $callBack)
    {
        return array_map($callBack, $this->allEntities);
    }

    /**
     * @param array $entities
     */
    private function parseData($entities = [])
    {
        foreach ($entities as $each) {
            $this->allEntities[] = new Transaction($each);
        }
    }
}
