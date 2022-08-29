<?php

declare(strict_types=1);

namespace App\Forms;

use App\Components\BaseControl;
use Nette\Application\UI;
use Nette\Application\UI\Template;

/**
 * Base class for all forms.
 * Handles the form rendering using form templates.
 */
abstract class BaseFormControl extends BaseControl
{
    /** @var bool automatically derive template path from class name */
    protected $autoSetupTemplateFile = false;


    protected function createComponentForm(): UI\Form
    {
        $form = new UI\Form;

        $form->setTranslator($this->translator);

        $form->addProtection('forms.protection.csrf');

        return $form;
    }


    protected function createTemplate(): Template
    {
        $template = parent::createTemplate();
        $path = $this->getTemplateFilePath();
        if ($template instanceof \Nette\Bridges\ApplicationLatte\Template && is_file($path)) {
            $template->setFile($path);
        }
        $template->_form = $template->form = $this['form'];
        return $template;
    }


    public function render()
    {
        $template = $this->template;

        if (
            $template instanceof \Nette\Bridges\ApplicationLatte\Template
            && ($template->getFile() === null || !is_file($template->getFile()))
        ) {
            return call_user_func_array([$this['form'], 'render'], func_get_args());
        } else {
            $template->render();
        }
    }
}
