<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Entity\Eshop;
use App\Model\Entity\User;

interface IUserFormFactory
{
    /**
     * @return UserForm
     */
    public function create(Eshop $eshop, ?User $user);
}
