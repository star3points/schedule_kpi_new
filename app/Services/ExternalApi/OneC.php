<?php

namespace App\Services\ExternalApi;

use Illuminate\Support\Facades\Http;

class OneC
{
    protected string $apiUrl;
    public function __construct()
    {
        $this->apiUrl = env('ONE_C_URL');
    }

    public function getShopList()
    {
        return $this->call(
            'Lichi/hs/API_V1/goodsdetails/1',
            null,
            ['namecatalog'=>'Склады']
        )['goodsdetails'];
    }

    public function getSales(\DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->call(
            '/Lichi/hs/API_KPI/DataKPI',
            null,
            [
                'datafrom' => $dateFrom->format('Ymd'),
                'datato' => $dateTo->format('Ymd')
            ]
        );
    }

    public function call(string $methodPath, array $get = null, array $post = null): array
    {
        $client = Http::withOptions([
            'auth' => [env('ONE_C_LOGIN'), env('ONE_C_PASSWORD')],
            'timeout' => 10000000
        ]);
        if (is_null($post)) {
            $response = $client->get($this->apiUrl . $methodPath, $get ?? []);
        } else {
            $response = $client->post($this->apiUrl . $methodPath, $post);
        }
        if (!$response->failed()) {
            return $response->json();
        } else {
            throw new \Exception('OneCFailed: ' . $response->body());
        }
    }
}