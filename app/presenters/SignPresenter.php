<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\ISignInFormFactory;
use Nette\Application\UI;


final class SignPresenter extends BasePresenter
{
    protected function startup()
    {
        parent::startup();

        if (
            $this->user->isLoggedIn() &&
            !in_array($this->action, [
                'out',
            ], true)
        ) {
            if ($this->backlink) {
                $this->restoreRequest($this->backlink);
            }

            $this->redirectToDashboard();
        }
    }


    public function actionIn()
    {
    }


    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('user.sign.signedOut');

        $this->redirectToDashboard();
    }


    protected function createComponentSignInForm(ISignInFormFactory $factory)
    {
        $form = $factory->create();

        $form['form']->onSuccess[] = [$this, 'signInFormSucceeded'];

        return $form;
    }


    /**
     * @param UI\Form $form
     * @return void
     */
    public function signInFormSucceeded(UI\Form $form, $values)
    {
        if ($this->backlink) {
            $this->restoreRequest($this->backlink);
        }

        $this->redirectToDashboard();
    }
}
