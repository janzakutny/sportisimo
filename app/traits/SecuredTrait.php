<?php

declare(strict_types=1);

namespace App\Traits;

use Kdyby\Autowired\PhpDocParser;
use Nette;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

trait SecuredTrait
{
    /** @var string */
    public $signLink = ':Sign:in';

    /** @var string */
    public $homepageLink = ':Default:';

    /** @var array|null */
    private $actionAnnotationUser;

    /** @var array|null */
    private $presenterAnnotationUser;


    /**
     * Check if user has privileges to action
     * @throws Nette\Application\ForbiddenRequestException
     */
    public function checkRequirements($element): void
    {
        $user = (array) $element->getAnnotation('User');
        if (in_array('loggedIn', $user, true) && !$this->getUser()->isLoggedIn()) {
            if ($this->user->logoutReason === User::LOGOUT_INACTIVITY) {
                $this->flashMessage('user.sign.signedOutDueInactivity');
            }

            if (array_key_exists('flash', $user) && $user['flash']) {
                $this->flashMessage($user['flash']);
            }

            $this->redirect($this->signLink, [
                'backlink' => $this->storeRequest(),
            ]);
        }

        try {
            $actionAnnotationUser = $this->getActionAnnotationUser();

            if ((!array_key_exists('role', $actionAnnotationUser) || $actionAnnotationUser['role'] !== 'guest') &&
                !$this->user->isAllowed($this->getResource($user), $this->getPrivilege($user))
            ) {
                if ($this->user->isLoggedIn()) { // if user is logged in throws exception
                    throw new Nette\Application\ForbiddenRequestException('homepage.messages.forbidden');
                }

                if (array_key_exists('flash', $user) && $user['flash']) {
                    $this->flashMessage($user['flash']);
                }

                if ($this->user->logoutReason === Nette\Security\UserStorage::LOGOUT_INACTIVITY) {
                    $this->flashMessage('user.sign.signedOutDueInactivity');
                }

                $this->redirect($this->signLink, [
                    'backlink' => $this->storeRequest(),
                ]);
            }
        } catch (Nette\InvalidStateException $e) {
            $this->error();
        }

        parent::checkRequirements($element);
    }


    /**
     * Return resource from annotation
     * @param array $annotation  [resource => string]
     */
    private function getResource(array $annotation)
    {
        $resource = null;
        $actionAnnotation = $this->getActionAnnotationUser();
        $presenterAnnotation = $this->getPresenterAnnotationUser();

        if (array_key_exists('resource', $annotation)) {
            $resource = $annotation['resource'];
        } elseif (array_key_exists('resource', $actionAnnotation)) {
            $resource = $actionAnnotation['resource'];
        } elseif (array_key_exists('resource', $presenterAnnotation)) {
            $resource = $presenterAnnotation['resource'];
        }

        return $resource ?: $this->name;
    }


    /**
     * Return privilege from annotation
     * @param array $annotation  [privilege => string]
     */
    private function getPrivilege(array $annotation): string
    {
        if (array_key_exists('privilege', $annotation)) {
            return $annotation['privilege'];
        } elseif (array_key_exists('privilege', $this->getActionAnnotationUser())) {
            return $this->getActionAnnotationUser()['privilege'];
        }

        return $this->action;
    }


    private function getPresenterAnnotationUser(): array
    {
        if ($this->presenterAnnotationUser === null) {
            $this->presenterAnnotationUser = (array) $this->parseUserAnnotation($this->getReflection());
        }

        return $this->presenterAnnotationUser;
    }


    private function getActionAnnotationUser(): array
    {
        if (
            $this->actionAnnotationUser === null &&
            $this->getReflection()->hasMethod('action' . ucfirst($this->action))
        ) {
            $this->actionAnnotationUser = (array) $this->parseUserAnnotation($this->getReflection()
                ->getMethod('action' . ucfirst($this->action)));
        } elseif ($this->actionAnnotationUser === null) {
            $this->actionAnnotationUser = [];
        }

        return $this->actionAnnotationUser;
    }


    private function parseUserAnnotation($element): ?ArrayHash
    {
        if ($element !== null && $element->getDocComment()) {
            $res = PhpDocParser::parseComment($element->getDocComment());

            return isset($res['User']) ? end($res['User']) : null;
        }

        return null;
    }
}
