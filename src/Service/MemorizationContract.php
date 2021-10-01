<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service;

interface MemorizationContract
{
    public function getData();

    public function saveData($data);
}
