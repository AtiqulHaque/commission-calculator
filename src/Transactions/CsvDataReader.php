<?php

declare(strict_types=1);

namespace Annual\CommissionTask\Transactions;

class CsvDataReader extends DataReader
{
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setFormatter(FormatterContract $formatter): CsvDataReader
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function parseData(): CsvDataReader
    {
        if (file_exists($this->baseUrl)) {
            if (($handle = fopen($this->baseUrl, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->content[] = $this->formatter->format($data);
                }
                fclose($handle);
            }
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->content;
    }
}
