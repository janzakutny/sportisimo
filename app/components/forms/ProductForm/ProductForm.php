<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\BrandsRepository;
use App\Model\Entity\Brand;
use App\Model\Entity\Product;
use App\Model\Entity\Eshop;
use App\Model\ProductsRepository;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Security\User;

class ProductForm extends BaseFormControl
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

    /** @var Product|null */
    private $product;

    /**
     * @var User
     * @inject
     */
    public $user;

    /** @var Eshop */
    private $eshop;

    /** @var array */
    private $brands = [];


    public function __construct(Eshop $eshop, ?Product $product)
    {
        $this->eshop = $eshop;
        $this->product = $product;
    }


    public function render()
    {
        $this->template->product = $this->product;
        $this->template->render();
    }


    protected function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('name', 'page.product.name')
            ->setDefaultValue($this->product->name ?? null)
            ->setRequired();

        $form->addSelect('brand', 'page.navigation.brands', $this->getBrandsList())
            ->setPrompt('page.product.selectBrand')
            ->setDefaultValue($this->product->brand->id ?? null)
            ->setRequired();

        $form->addText('description', 'page.product.description', 6)
            ->setDefaultValue($this->product->description ?? null);

        $form->addSubmit('save', 'forms.button.save')
            ->setHtmlAttribute('class', 'btn');

        $form->onSuccess[] = [$this, 'productFormSucceeded'];

        return $form;
    }


    public function productFormSucceeded(Form $form, $values)
    {
        if (!$this->user->isAllowed('Admin')) {
            throw new ForbiddenRequestException();
        }

        if (!$this->product) {
            $this->productsRepository->createProduct($values);
        } else {
            $this->productsRepository->editProduct($this->product, $values);
        }
    }


    private function getBrandsList(): array
    {
        if (empty($this->brands)) {
            $brands = $this->brandsRepository->findBy(['state' => Brand::STATE_ACTIVE])->orderBy('name');
            foreach ($brands as $brand) {
                $this->brands[$brand->id] = $brand->name;
            }
        }

        return $this->brands;
    }
}
