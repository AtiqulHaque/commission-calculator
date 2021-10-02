<?php
declare(strict_types=1);

namespace Paysera\CommissionTask\Service\ExchangeRateService;

use Dotenv\Dotenv;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRateService implements ExchangeRateContract
{
    public $client = null;
    public $formatter = null;
    public $cacheData = null;

    /** @var ExchangeRateFormatterContract $formatter */

    public function __construct($baseUrl)
    {
        $this->client = new Client(['base_uri' => $baseUrl]);
    }

    /**
     * @param ExchangeRateFormatterContract $driver
     * @return $this
     */
    public function setFormatter(ExchangeRateFormatterContract $driver)
    {
        $this->formatter = $driver;
        return $this;
    }

    /**
     * @param $currency
     * @param bool $cache
     * @return int
     * @throws GuzzleException
     */
    public function getRate($currency, $cache = true)
    {
        if ($cache && !empty($this->cacheData)) {
            return $this->formatter->format($this->cacheData, $currency);
        }

        $response = $this->client->request('GET', "latest");

        if ($response->getStatusCode() == 200) {
            $body = file_get_contents("/home/atiqul/docker-project/commission-calculator/exchangeResponse.txt");
           // $body = $body->getContents();
            if ($this->isJson($body)) {
                $rates = json_decode($body, true);
                $this->cacheData = $rates;
                return $this->formatter->format($this->cacheData, $currency);
            }
        }
        throw new Exception("Get Invalid Data from rate exchange service");
    }

    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
