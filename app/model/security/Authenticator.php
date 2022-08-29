<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use Nette\Security as NS;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nette\SmartObject;


class Authenticator implements NS\Authenticator, NS\IdentityHandler
{
    use SmartObject;

    public const IDENTITY_DELETED = 10;

    public const IDENTITY_TIMESTAMPFILE = 'identity';

    public const MIN_PASSWORD_LENGTH = 8;

    public const PASSWORD_PATTERN = '^(.*)(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)(.*)$';

    public const LOGIN_EXPIRATION = '+ 30 minutes';

    public const LOGIN_EXPIRATION_REMEMBER = '+ 14 days';

    /** @var UsersRepository */
    private $usersRepository;

    /** @var Passwords */
    private $passwords;

    /** @var string */
    private $tempDir;


    public function __construct(UsersRepository $usersRepository, Passwords $passwords, $tempDir)
    {
        $this->usersRepository = $usersRepository;
        $this->passwords = $passwords;
        $this->tempDir = $tempDir;
    }


    /**
     * Performs an authentication
     * @param string $email
     * @param string $password
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(string $email, string $password): IIdentity
    {
        $user = $this->usersRepository->getBy(['email' => $email]);

        if (!$user || $user->isDeleted) {
            throw new NS\AuthenticationException('User with this email was not found.', NS\Authenticator::IDENTITY_NOT_FOUND);
        }
        if (!$this->passwords->verify($password, $user->password)) {
            throw new NS\AuthenticationException('Invalid password.', NS\Authenticator::INVALID_CREDENTIAL);
        } elseif ($this->passwords->needsRehash($user->password)) {
            $user->password = $this->passwords->hash($password);
            $this->usersRepository->persistAndFlush($user);
        }

        return $this->createIdentity($user);
    }


    public function createIdentity(Entity\User $user): SimpleIdentity
    {
        $data = $this->prepareIdentityBasicData($user);

        return new SimpleIdentity($user->id, $user->userRoles, $data);
    }


    private function prepareIdentityBasicData(Entity\User $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
            'timestamp' => $this->getIdentityTimestamp(),
        ];
    }


    private function getIdentityTimestamp()
    {
        $file = $this->tempDir . '/' . self::IDENTITY_TIMESTAMPFILE;

        if (!file_exists($file)) {
            $this->refreshIdentityTimestamp();
        }

        return filemtime($file);
    }


    public function refreshIdentityTimestamp()
    {
        $file = $this->tempDir . '/' . self::IDENTITY_TIMESTAMPFILE;

        $handle = fopen($file, 'w');
        fwrite($handle, '');
        fclose($handle);
    }


    public function sleepIdentity(IIdentity $identity): IIdentity
    {
        return $identity;
    }


    public function wakeupIdentity(IIdentity $identity): ?IIdentity
    {
        assert($identity instanceof SimpleIdentity);

        $timestamp = $this->getIdentityTimestamp();
        if ($identity->timestamp === $timestamp) {
            return $identity;
        }

        $user = $this->usersRepository->get($identity->id);
        if (
            !$user || $user->state == Entity\User::STATE_DELETED || $user->state === Entity\User::STATE_INACTIVED
        ) {
            return null;
        }

        $data = $this->prepareIdentityBasicData($user);

        $identity->timestamp = $data['timestamp'];
        $identity->email = $data['email'];
        $identity->lastSign = $data['lastSign'];

        $identity->setRoles($user->userRoles);

        return $identity;
    }
}
