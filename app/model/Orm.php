<?php

declare(strict_types=1);


namespace App\Model;

use Nextras\Orm\Model\Model;

/**
 * @property-read UsersRepository $users
 * @property-read BrandsRepository $brands
 * @property-read EshopsRepository $eshops
 * @property-read ProductsRepository $products
 */
class Orm extends Model
{
}
