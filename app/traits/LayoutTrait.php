<?php

declare(strict_types=1);

namespace App\Traits;

use App\Model\Entity\User;
use Contributte\Translation\Translator;
use stdClass;

trait LayoutTrait
{
    /** @persistent */
    public $lang;

    /**
     * @var Translator
     * @inject
     */
    public $translator;

    protected function beforeRender()
    {
        parent::beforeRender();

        $parameters = $this->contextParametersFactory->getParameter();
        $template = $this->template;
        $template->sidebar = $this->user->isLoggedIn() && $this->user->isInRole(User::ROLE_ADMIN);

        $template->brandLink = $this->link($this->homepageLink);
        $template->homePage ??= false;
    }


    protected function afterRender()
    {
        parent::afterRender();

        if ($this->isAjax() && $this->template->flashes) {
            $this->redrawControl('flashes');
        }
    }


    public function flashMessage($message, string $type = 'info', $count = null, array $param = []): stdClass
    {
        return parent::flashMessage($this->translator->translate($message, $count, $param), $type);
    }
}
