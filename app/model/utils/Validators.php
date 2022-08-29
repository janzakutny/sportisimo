<?php

declare(strict_types=1);

namespace App\Model\Utils;

use App\Model\Entity\User;
use App\Model\UsersRepository;
use Nette;

class Validators
{
    use Nette\SmartObject;

    /** @var UsersRepository */
    private $usersRepository;


    public function __construct(
        UsersRepository $usersRepository,
    ) {
        $this->usersRepository = $usersRepository;
    }


    /**
     * @param string $key
     * @param string $value
     * @param int $exceptUserId
     * @return bool
     */
    public function isUserEmailUnique($key, $value, $exceptUserId = null): bool
    {
        $by = [
            $key => $value,
        ];

        if ($exceptUserId) {
            $by['id!='] = $exceptUserId;
        }

        $by['state!='] = User::STATE_DELETED;

        $user = $this->usersRepository->getBy($by);

        return !$user;
    }
}
