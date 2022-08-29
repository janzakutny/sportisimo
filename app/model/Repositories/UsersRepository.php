<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Eshop;
use App\Model\Entity\User;
use Nette\Security\Passwords;
use Nextras\Orm\Repository\IDependencyProvider;


/**
 * @property-read UsersMapper $mapper
 * @method UsersMapper getMapper()
 */
class UsersRepository extends Repository
{
    public static $roles = [
        'guest' => 'user.role.quest',
        'authenticated' => 'user.role.authenticated',
        'admin' => 'user.role.admin',
    ];

    /** @var Passwords */
    private $passwords;


    public function __construct(
        UsersMapper $mapper,
        IDependencyProvider $dependencyProvider = null,
        Passwords $passwords,
    ) {
        parent::__construct($mapper, $dependencyProvider);
        $this->passwords = $passwords;
    }


    public static function getEntityClassNames(): array
    {
        return [User::class];
    }


    public function get(int $id, bool $notDeleted = true): ?User
    {
        $user = $this->getById($id);

        if (!$user || $notDeleted && $user->state == Entity\User::STATE_DELETED) {
            return null;
        }

        return $user;
    }


    public function createUser(Eshop $eshop, $values)
    {
        $user = new User;
        $this->attach($user);

        $user->eshop = $eshop->id;

        if ($values->password) {
            $user->password = $this->passwords->hash($values->password);
        }

        $user->roles = implode(', ', $values->roles);

        $user->assign($values, [
            'name', 'surname', 'email',
        ]);

        $this->persistAndFlush($user);
    }


    public function editUser(User $user, $values)
    {
        if ($values->password) {
            $user->password = $this->passwords->hash($values->password);
        }

        $user->roles = implode(', ', $values->roles);

        $user->assign($values, [
            'name', 'surname', 'email',
        ]);

        $this->persistAndFlush($user);
    }
}
