<?php

namespace App\Services\ExternalApi\Bitrix;

use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use Lichispb\LLE\BX\Rest as BxRest;
use Lichispb\LLE\BX\User;
use Lichispb\LLE\BX\User as BxUser;

class BitrixUser implements BitrixUserInterface
{
    protected array $admins = [68393, 7201, 139631, 112554, 153816];
    protected array $userData = [];
    public function __construct()
    {
        $this->userData = (new BxRest(new Http()))->userGet((new BxUser())->getId());
        $shop = Shop::where('bx_id', '=', $this->userData['UF_DEPARTMENT'][0])->first();
        if (in_array($this->userData['ID'], $this->admins)) {
            $this->role = 'Admin';
        } elseif ($shop) {
            $this->shop = $shop;
            $this->role = 'Worker';
        } else {
            $this->role = 'NoData';
        }

        //test
        if ($this->userData['ID'] == 68393 && false) {
            $this->userData = [
                'ID' => 1,
                'NAME' => 'Firstname',
                'LAST_NAME' => 'Lastname',
                'UF_DEPARTMENT' => [167]
            ];
            $this->role = 'Worker';
            $shop = Shop::where('bx_id', '=', $this->userData['UF_DEPARTMENT'][0])->first();
            $this->shop = $shop;
        }
    }

    public function data()
    {
        return $this->userData;
    }

    public function getRole()
    {
        return 'Admin';
    }

    public function getId()
    {
        return $this->userData['ID'];
    }

    public function getDepartment()
    {
        return $this->userData['UF_DEPARTMENT'][0];
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