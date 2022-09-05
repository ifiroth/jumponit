<?php

namespace JOI\Service;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use JOI\Service\Debug;

class ProductManager {

    public function getNotLocatedProducts(): ?array {

        $sql = new \DbQuery();

        $sql->select('p.`id_product`, s.`name`, s.`city`, fp.`id_feature_value`');
        $sql->from('product', 'p');
        $sql->innerJoin('seller_product', 'sp', 'p.`id_product` = sp.`id_product`');
        $sql->innerJoin('seller', 's', 'sp.`id_seller` = s.`id_seller`');
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_product` = p.`id_product`');
        $sql->where('fp.`id_feature_value` IS NULL');
        $sql->orderBy('p.`id_product`');

        $products = \Db::getInstance()->executeS($sql);

        return $products ?: null;
    }

    public function setLocationToProducts($products = null) : int {

        $i = 0;
        $products = ($products == null) ? $this->getNotLocatedProducts() : $products;
        $id_feature = \Configuration::get(_MOD_PREFIX_.'feature_id');

        foreach ($products as $product) {

            $hasValueId = FeatureManager::hasValueId($product['city']);

            if ($product['city'] != '') {

                // Si la feature n'existe pas, on la crée
                if (!$hasValueId) {

                    $id_feature_value = FeatureManager::createValue($product['city']);

                // Sinon, on récupère juste son id
                } else {

                    $id_feature_value = $hasValueId;
                }

                // Une fois qu'on a toutes les infos, on peut lier le produit à son attribut.
                Product::addFeatureProductImport($product['id_product'], (int) $id_feature, (int) $id_feature_value);
                $i++;

            }
        }

        return $i;
    }
}
