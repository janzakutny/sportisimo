<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Authenticator;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Security;


class SignInForm extends BaseFormControl
{
    /**
     * @var Security\User
     * @inject
     */
    public $user;


    protected function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->getElementPrototype()->data('ajax', 'false');

        $form->addEmail('em', 'forms.email.name')
            ->setRequired('forms.email.required')
            ->setHtmlAttribute('placeholder', 'forms.email.placeholder');

        $form->addPassword('password', 'forms.password.name')
            ->setHtmlAttribute('placeholder', 'forms.password.placeholder')
            ->setRequired('forms.password.required');

        $form->addCheckbox('remember', 'forms.sign.remember');

        $form->addSubmit('sign', 'forms.sign.login');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];

        return $form;
    }


    /**
     * @param UI\Form $form
     * @return void
     */
    public function signInFormSucceeded(UI\Form $form, $values)
    {
        if ($values->remember) {
            $this->user->setExpiration(Authenticator::LOGIN_EXPIRATION_REMEMBER);
        } else {
            $this->user->setExpiration(Authenticator::LOGIN_EXPIRATION);
        }

        try {
            $this->user->login($values->em, $values->password);
        } catch (Security\AuthenticationException $e) {
            $form->addError('forms.sign.invalidCredentials');
        }
    }
}
