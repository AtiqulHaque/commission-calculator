<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Service\DataReaderService;

abstract class DataReader implements DataReaderContract
{
    public $formatter = null;

    protected $content = [];

    protected $baseUrl;
}
