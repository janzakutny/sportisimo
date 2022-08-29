<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\IUserFormFactory;
use App\Model\Entity\User;
use App\Model\UsersRepository;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;

class UserPresenter extends BasePresenter
{
    /**
     * @var UsersRepository
     * @inject
     */
    public $usersRepository;

    /** @var User|null */
    private $userEntity;


    private function loadUserEntity(int $userId)
    {
        if (!$this->userEntity) {
            $this->userEntity = $this->usersRepository->getById($userId);
        }

        if (!$this->userEntity) {
            $this->error('user.notFound');
        }

        return $this->userEntity;
    }


    public function actionUsers()
    {
        $this->template->users = $this->usersRepository->findBy(['state' => User::STATE_ACTIVED]);
    }


    public function actionAdd()
    {
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    public function actionEdit(int $id)
    {
        $user = $this->loadUserEntity($id);
        $this->template->userEntity = $user;
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    public function handleDeleteUser(int $userId)
    {
        if (!$this->user->isAllowed('User')) {
            throw new ForbiddenRequestException();
        }

        $user = $this->loadUserEntity($userId);

        if (!$user) {
            return;
        }

        $user->state = User::STATE_DELETED;
        $this->usersRepository->persistAndFlush($user);

        $this->flashMessage('user.beenDeleted');
        $this->redrawControl();
    }


    protected function createComponentUserForm(IUserFormFactory $factory)
    {
        $form = $factory->create($this->eshop, $this->userEntity);
        $form['form']->onSuccess[] = function (Form $form, $values) {
            $this->flashMessage('user.beenSaved');

            if ($this->isAjax()) {
                $this->redrawControl();
            } else {
                $this->redirect('users');
            }
        };

        return $form;
    }
}
