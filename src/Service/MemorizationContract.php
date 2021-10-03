<?php
declare(strict_types=1);

namespace Annual\CommissionTask\Service;

interface MemorizationContract
{
    /**
     * @param $index
     * @return array
     */
    public function getData(string $index): array;

    /**
     * @param $index
     * @param $data
     * @return bool
     */
    public function saveData(string $index, array $data): bool;
}
