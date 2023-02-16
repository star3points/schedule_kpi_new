<?php

namespace App\Services\ExternalApi\Bitrix;

class BitrixUserTest implements BitrixUserInterface
{
    public function __construct()
    {

    }

    public function data()
    {
        return [];
    }

    public function getRole()
    {
        return 'Admin';
    }

    public function getId()
    {
        return 1;
    }

    public function getDepartment()
    {
        return 191;
    }

    public function isAdmin(): bool
    {
        return true;
    }

    public function isManagerOf($shopId): bool
    {
        return true;
    }

    public function isWorkerOf($shopId): bool
    {
        return true;
    }
}