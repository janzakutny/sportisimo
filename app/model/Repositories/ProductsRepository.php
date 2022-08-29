<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Product;

class ProductsRepository extends Repository
{
    public static function getEntityClassNames(): array
    {
        return [Product::class];
    }


    public function createProduct($values)
    {
        $product = new Product;
        $this->attach($product);

        $product->name = $values->name;
        $product->description = $values->description;
        $product->brand = $values->brand;

        return $this->persistAndFlush($product);
    }


    public function editProduct(Product $product, $values)
    {
        $product->name = $values->name;
        $product->description = $values->description;
        $product->brand = $values->brand;

        return $this->persistAndFlush($product);
    }
}
