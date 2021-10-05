<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\Memorization;

interface MemorizationContract
{
    public function getData(string $index): array;

    /**
     * @param $index
     * @param $data
     */
    public function saveData(string $index, array $data): bool;
}
