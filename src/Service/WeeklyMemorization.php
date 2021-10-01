<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service;

class WeeklyMemorization implements MemorizationContract
{
    private $memory = array();

    public function getData()
    {
        return $this->memory;
    }

    public function saveData($data)
    {
        $this->memory = $data;
    }
}
