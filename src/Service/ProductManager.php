<?php

namespace JOI\Service;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use JOI\Service\Debug;

class ProductManager {

    static public function getNotLocatedProducts($id_seller = null): ?array {

        $lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');

        if ($id_seller) {
            $whereSeller = " AND s.`id_seller` = ". $id_seller;
        } else {
            $whereSeller = "";
        }

        $sql = new \DbQuery();

        $sql->select('p.`id_product`, s.`name` as seller_name, s.`city`, fp.`id_feature_value`, pl.`name` as product_name');
        $sql->from('product', 'p');
        $sql->innerJoin('seller_product', 'sp', 'p.`id_product` = sp.`id_product`');
        $sql->innerJoin('seller', 's', 'sp.`id_seller` = s.`id_seller`');
        $sql->innerJoin('product_lang', 'pl', 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = '. $lang_id);
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_product` = p.`id_product`');
        $sql->where('fp.`id_feature_value` IS NULL'. $whereSeller);
        $sql->orderBy('p.`id_product`');

        $products = \Db::getInstance()->executeS($sql);

        return $products ?: null;
    }

    static public function setLocationToProducts($id_seller = null) : int {

        $i = 0;

        $products = self::getNotLocatedProducts($id_seller);
        $mod_prefix = \Configuration::get('module_prefix');
        $id_feature = \Configuration::get($mod_prefix .'feature_id');

        \Configuration::updateValue($mod_prefix .'last_feature_value_import', time());

        if ($products) {
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
        }


        return $i;
    }
}
