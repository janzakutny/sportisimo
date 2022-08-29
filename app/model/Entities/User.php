<?php

namespace App\Model\Entity;

use DateTimeImmutable;
use Nette\Utils\Strings;
use Nextras\Orm\Relationships\ManyHasOne;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $password
 * @property DateTimeImmutable $registerDate {default now}
 * @property int $state {enum self::STATE_*} {default self::STATE_ACTIVED}
 * @property string|null $roles
 * 
 * @property ManyHasOne|Eshop $eshop {m:1 Eshop::$users}
 * 
 * @property-read bool $isActived {virtual}
 * @property-read bool $isDeleted {virtual}
 * @property-read array $userRoles {virtual}
 * @property-read string $fullName {virtual}
 * @property-read bool $isAuthenticated {virtual}
 * @property-read bool $isAdmin {virtual}
 */
class User extends Entity
{
    public const STATE_DELETED = 0;

    public const STATE_ACTIVED = 1;


    public const ROLE_AUTHENTICATED = 'authenticated';

    public const ROLE_ADMIN = 'admin';


    protected function getterIsActived()
    {
        return $this->state == self::STATE_ACTIVED;
    }


    protected function getterIsDeleted()
    {
        return $this->state == self::STATE_DELETED;
    }


    protected function getterUserRoles()
    {
        $roles = [];

        if ($this->roles) {
            $userRoles = explode(',', $this->roles);
            foreach ($userRoles as $ur) {
                $roles[] = Strings::trim($ur);
            }
        }

        return $roles;
    }


    protected function getterFullName()
    {
        return $this->name . ' ' . $this->surname;
    }


    protected function getterIsAuthenticated()
    {
        return $this->roles
            ? Strings::contains($this->roles, self::ROLE_AUTHENTICATED)
            : false;
    }


    protected function getterIsAdmin()
    {
        return $this->roles ? Strings::contains($this->roles, self::ROLE_ADMIN) : false;
    }
}
