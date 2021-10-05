<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\Memorization;

class WeeklyMemorization implements MemorizationContract
{
    private $memory = [];

    /**
     * @param $index
     *
     * @return array|mixed
     */
    public function getData(string $index): array
    {
        return !empty($this->memory[$index]) ? $this->memory[$index] : [];
    }

    /**
     * @param $index
     * @param $data
     */
    public function saveData(string $index, array $data): bool
    {
        $this->memory[$index] = $data;

        return true;
    }
}
