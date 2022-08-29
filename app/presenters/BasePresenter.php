<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Misc\ContextParametersFactory;
use App\Model\Entity\Eshop;
use App\Model\EshopsRepository;
use App\Traits\LayoutTrait;
use App\Traits\SecuredTrait;
use Kdyby\Autowired\AutowireComponentFactories;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    use LayoutTrait;
    use SecuredTrait;
    use AutowireComponentFactories;

    /** @persistent */
    public $backlink;

    /**
     * @var ContextParametersFactory
     * @inject
     */
    public $contextParametersFactory;

    /**
     * @var EshopsRepository
     * @inject
     */
    public $eshopRepository;

    /** @var Eshop */
    protected $eshop;


    protected function startup()
    {
        parent::startup();

        $this->translator->setLocale($this->lang ?: 'cs');

        $this->loadEshop();
    }


    protected function redirectToDashboard()
    {
        $this->redirect($this->homepageLink);
    }


    protected function loadEshop()
    {
        if (!$this->eshop) {
            $this->eshop = $this->eshopRepository->getBy(['domain' => $this->contextParametersFactory->getParameter('url')]);
        }

        return $this->eshop;
    }
}
