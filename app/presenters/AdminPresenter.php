<?php

declare(strict_types=1);

namespace App\Presenters;

use AlesWita\VisualPaginator\VisualPaginator;
use App\Forms\IBrandFormFactory;
use App\Model\Entity\Brand;
use App\Model\BrandsRepository;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use AlesWita\VisualPaginator\VisualPaginatorFactory;
use App\Forms\IProductFormFactory;
use App\Model\Entity\Product;
use App\Model\ProductsRepository;

class AdminPresenter extends BasePresenter
{
    /**
     * @var BrandsRepository
     * @inject
     */
    public $brandsRepository;

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

    /** @var Brand|null */
    private $brand;

    /** @var Product|null */
    private $product;


    private function loadBrand(int $id)
    {
        if (!$this->brand) {
            $this->brand = $this->brandsRepository->getBy(['id' => $id, 'eshop->id' => $this->eshop->id]);
        }

        if (!$this->brand) {
            $this->error('page.brand.notFound');
        }

        return $this->brand;
    }


    private function loadProduct(int $id)
    {
        if (!$this->product) {
            $this->product = $this->productsRepository->getBy(['id' => $id, 'brand->eshop->id' => $this->eshop->id]);
        }

        if (!$this->product) {
            $this->error('page.product.notFound');
        }

        return $this->product;
    }


    public function actionBrands(int $order = null, int $perPage = null)
    {
        if ($perPage === null || !in_array($perPage, [10, 20, 30], true)) {
            $perPage = 10;
        }

        if ($order === null) {
            $orderItem = 'id';
            $orderDirection = 'ASC';
        } else {
            $orderItem = 'name';
            $orderDirection = $order == 0 ? 'ASC' : 'DESC';
        }

        $brands = $this->brandsRepository->findBy(['state' => Brand::STATE_ACTIVE]);

        $this->visualPaginator = $this['brandsPaginator'];
        $paginator = $this->visualPaginator->getPaginator();
        $paginator->itemsPerPage = $perPage;

        $paginator->itemCount = $brands->countStored();


        $brands = $brands->limitBy($paginator->itemsPerPage, $paginator->offset)->orderBy($orderItem, $orderDirection);

        $this->template->brands = $brands;
        $this->template->pages = $paginator->pageCount;
        $this->template->order = $order;
        $this->template->perPage = $perPage;

        if ($this->isAjax()) {
            $this->redrawControl();
        }
    }


    public function actionProducts(int $order = null, int $perPage = null)
    {
        if ($perPage === null || !in_array($perPage, [10, 20, 30], true)) {
            $perPage = 10;
        }

        if ($order === null) {
            $orderItem = 'id';
            $orderDirection = 'ASC';
        } else {
            $orderItem = 'name';
            $orderDirection = $order == 0 ? 'ASC' : 'DESC';
        }

        $products = $this->productsRepository->findBy(['brand->state' => Brand::STATE_ACTIVE]);

        $this->visualPaginator = $this['productsPaginator'];
        $paginator = $this->visualPaginator->getPaginator();
        $paginator->itemsPerPage = $perPage;

        $paginator->itemCount = $products->countStored();


        $products = $products->limitBy($paginator->itemsPerPage, $paginator->offset)->orderBy($orderItem, $orderDirection);

        $this->template->products = $products;
        $this->template->pages = $paginator->pageCount;
        $this->template->order = $order;
        $this->template->perPage = $perPage;

        if ($this->isAjax()) {
            $this->redrawControl();
        }
    }


    public function actionAddBrand()
    {
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    public function actionEditBrand(int $id)
    {
        $brand = $this->loadBrand($id);
        $this->template->brand = $brand;
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    public function actionAddProduct()
    {
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    public function actionEditProduct(int $id)
    {
        $product = $this->loadProduct($id);
        $this->template->product = $product;
        $this->template->showModal = false;

        if ($this->isAjax() && !$this->isControlInvalid()) {
            $this->template->showModal = true;
            $this->redrawControl('modalWindow');
        }
    }


    protected function createComponentBrandForm(IBrandFormFactory $factory)
    {
        $form = $factory->create($this->eshop, $this->brand);
        $form['form']->onSuccess[] = function (Form $form, $values) {
            $this->flashMessage('page.brand.beenSaved');

            if ($this->isAjax()) {
                $this->redrawControl();
            } else {
                $this->redirect('brands');
            }
        };

        return $form;
    }


    protected function createComponentProductForm(IProductFormFactory $factory)
    {
        $form = $factory->create($this->eshop, $this->product);
        $form['form']->onSuccess[] = function (Form $form, $values) {
            $this->flashMessage('page.product.beenSaved');
            if ($this->isAjax()) {
                $this->redrawControl();
            } else {
                $this->redirect('products');
            }
        };

        return $form;
    }


    /**
     * @return VisualPaginator\Control
     */
    protected function createComponentBrandsPaginator(): VisualPaginator
    {
        $control = $this->visualPaginator->create();

        $control->ajax = false;
        $control->canSetItemsPerPage = true;
        $control->templateFile = __DIR__ . '/../components/Paginator/bootstrap.latte';

        return $control;
    }


    /**
     * @return VisualPaginator\Control
     */
    protected function createComponentProductsPaginator(): VisualPaginator
    {
        $control = $this->visualPaginator->create();

        $control->ajax = false;
        $control->canSetItemsPerPage = true;
        $control->templateFile = __DIR__ . '/../components/Paginator/bootstrap.latte';

        return $control;
    }


    public function handleDeleteBrand(int $brandId)
    {
        if (!$this->user->isAllowed('Admin')) {
            throw new ForbiddenRequestException();
        }

        $brand = $this->loadBrand($brandId);

        if (!$brand) {
            return;
        }

        $brand->state = Brand::STATE_DELETED;

        $this->brandsRepository->persistAndFlush($brand);

        $this->flashMessage('page.brand.beenDeleted');
        $this->redrawControl();
    }


    public function handleDeleteProduct(int $productId)
    {
        if (!$this->user->isAllowed('Admin')) {
            throw new ForbiddenRequestException();
        }

        $product = $this->loadProduct($productId);

        if (!$product) {
            return;
        }

        $this->productsRepository->removeAndFlush($product);

        $this->flashMessage('page.brand.beenDeleted');
        $this->redrawControl();
    }
}
