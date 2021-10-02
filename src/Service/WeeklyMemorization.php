<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service;

class WeeklyMemorization implements MemorizationContract
{
    private $memory = array();

    /**
     * @param $index
     * @return array|mixed
     */
    public function getData(string $index): array
    {
        return !empty($this->memory[$index]) ? $this->memory[$index] : array();
    }

    /**
     * @param $index
     * @param $data
     * @return bool
     */
    public function saveData(string $index, array $data): bool
    {
        $this->memory[$index] = $data;
        return true;
    }
}
