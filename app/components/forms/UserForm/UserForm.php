<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Entity\Eshop;
use App\Model\Entity\User as EntityUser;
use App\Model\EshopsRepository;
use App\Model\UsersRepository;
use App\Model\Utils\FormValidators;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Security\User;

class UserForm extends BaseFormControl
{
    /**
     * @var UsersRepository
     * @inject
     */
    public $usersRepository;

    /**
     * @var EshopsRepository
     * @inject
     */
    public $eshopsRepository;

    /**
     * @var FormValidators
     * @inject
     */
    public $validators;

    /**
     * @var User
     * @inject
     */
    public $user;

    /** @var EntityUser */
    private $userEntity;

    /** @var Eshop */
    private $eshop;


    public function __construct(Eshop $eshop, ?EntityUser $userEntity)
    {
        $this->eshop = $eshop;
        $this->userEntity = $userEntity;
    }


    public function render()
    {
        $this->template->userEntity = $this->userEntity;
        if ($this->userEntity) {
            $this->setFormDefaults();
        }

        $this->template->render();
    }


    protected function createComponentForm(): Form
    {
        if ($this->userEntity) {
            $this->validators->setExceptUserId($this->userEntity->id);
        }

        $form = parent::createComponentForm();

        $form->addText('name', 'user.name')
            ->setRequired('user.nameRequired');

        $form->addText('surname', 'user.surname')
            ->setRequired('user.surnameRequired');

        $form->addEmail('email', 'user.email')
            ->setRequired('user.emailRequired')
            ->addRule(Form::MAX_LENGTH, null, 64)
            ->addRule([$this->validators, 'isUserEmailUnique'], 'forms.email.alreadyExists');

        $password = $form->addPassword('password', 'forms.password.name')
            ->setHtmlAttribute('placeholder', 'forms.password.placeholder');

        $password->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'forms.password.min', 4);

        if ($this->userEntity && !$this->userEntity->password || !$this->userEntity) {
            $password->setRequired('forms.password.required');
        }
        $form->addPassword('confirmPassword', 'forms.password.confirm')->setOmitted()
            ->setHtmlAttribute('placeholder', 'forms.password.confirmPlaceholder')
            ->addConditionOn($password, Form::FILLED)
            ->setRequired('forms.password.confirmRequired')
            ->addRule(Form::EQUAL, 'forms.password.confirmEqual', $form['password']);

        $form->addMultiSelect('roles', 'user.roles', [
            EntityUser::ROLE_AUTHENTICATED => 'user.role.authenticated',
            EntityUser::ROLE_ADMIN => 'user.role.admin',
        ])
            ->setRequired();

        $form->addSubmit('save', 'forms.button.save')
            ->setHtmlAttribute('class', 'btn');

        $form->onSuccess[] = [$this, 'userFormSucceeded'];

        return $form;
    }


    public function userFormSucceeded(Form $form, $values)
    {
        if (!$this->user->isAllowed('User')) {
            throw new ForbiddenRequestException();
        }

        if (!$this->userEntity) {
            $this->usersRepository->createUser($this->eshop, $values);
        } else {
            $this->usersRepository->editUser($this->userEntity, $values);
        }
    }


    private function setFormDefaults()
    {
        $values = $this->userEntity->toArray();
        $values['roles'] = $this->userEntity->userRoles;
        $this['form']->setDefaults($values);
    }
}
