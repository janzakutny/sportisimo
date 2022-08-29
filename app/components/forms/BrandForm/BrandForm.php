<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\BrandsRepository;
use App\Model\Entity\Brand;
use App\Model\Entity\Eshop;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Security\User;

class BrandForm extends BaseFormControl
{
    /**
     * @var BrandsRepository
     * @inject
     */
    public $brandsRepository;

    /** @var Brand|null */
    private $brand;

    /**
     * @var User
     * @inject
     */
    public $user;

    /** @var Eshop */
    private $eshop;


    public function __construct(Eshop $eshop, ?Brand $brand)
    {
        $this->eshop = $eshop;
        $this->brand = $brand;
    }


    public function render()
    {
        $this->template->brand = $this->brand;
        $this->template->render();
    }


    protected function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('name', 'page.brand.name')
            ->setDefaultValue($this->brand->name ?? null)
            ->setRequired();

        $form->addText('description', 'page.brand.description', 6)
            ->setDefaultValue($this->brand->description ?? null);

        $form->addSubmit('save', 'forms.button.save')
            ->setHtmlAttribute('class', 'btn');

        $form->onSuccess[] = [$this, 'brandFormSucceeded'];

        return $form;
    }


    public function brandFormSucceeded(Form $form, $values)
    {
        if (!$this->user->isAllowed('Admin')) {
            throw new ForbiddenRequestException();
        }

        if (!$this->brand) {
            $this->brandsRepository->createBrand($this->eshop, $values);
        } else {
            $this->brandsRepository->editBrand($this->brand, $values);
        }
    }
}
