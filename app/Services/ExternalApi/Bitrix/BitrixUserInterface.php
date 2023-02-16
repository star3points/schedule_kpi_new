<?php

namespace App\Services\ExternalApi\Bitrix;

interface BitrixUserInterface
{
    public function data();
    public function getRole();
    public function getId();
    public function getDepartment();
    public function isAdmin(): bool;
    public function isManagerOf($shopId): bool;
    public function isWorkerOf($shopId): bool;

}