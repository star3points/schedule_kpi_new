<?php

namespace App\Services\TableConstructor;

use Illuminate\Support\Collection;

interface TableConstructorInterface
{
    public function prepareShops(Collection $data);

    public function prepareShop(Collection $data);

}