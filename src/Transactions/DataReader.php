<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

abstract class DataReader implements DataReaderContract
{
    public $dataReadDriver = null;

    public $formatter = null;

    protected $content = [];

    protected $baseUrl;
}
