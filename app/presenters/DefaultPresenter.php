<?php

declare(strict_types=1);

namespace App\Presenters;

use AlesWita\VisualPaginator\VisualPaginator;
use AlesWita\VisualPaginator\VisualPaginatorFactory;
use App\Model\ProductsRepository;
use Nette;


final class DefaultPresenter extends BasePresenter
{
    /**
     * @var ProductsRepository
     * @inject
     */
    public $productsRepository;

    /**
     * @var VisualPaginatorFactory
     * @inject
     */
    public $visualPaginator;


    public function actionDefault()
    {
        $products = $this->productsRepository->findAll();

        $this->visualPaginator = $this['productsPaginator'];
        $paginator = $this->visualPaginator->getPaginator();
        $paginator->itemsPerPage = 18;

        $paginator->itemCount = $products->countStored();


        $products = $products->limitBy($paginator->itemsPerPage, $paginator->offset)->orderBy('createdAt', 'DESC');

        $this->template->products = $products;
        $this->template->pages = $paginator->pageCount;

        if ($this->isAjax()) {
            $this->redrawControl();
        }
    }


    /**
     * @return VisualPaginator\Control
     */
    protected function createComponentProductsPaginator(): VisualPaginator
    {
        $control = $this->visualPaginator->create();

        $control->ajax = true;
        $control->canSetItemsPerPage = true;
        $control->templateFile = __DIR__ . '/../components/Paginator/bootstrap.latte';

        return $control;
    }
}
