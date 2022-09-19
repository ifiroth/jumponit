<?php

namespace JOI\Service;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use JOI\Service\Debug;

class ProductManager {

    public function getNotLocatedProducts($id_seller = null): ?array {

        $lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');

        if ($id_seller) {
            $whereSeller = " AND s.`id_seller` = ". $id_seller;
        } else {
            $whereSeller = "";
        }

        $sql = new \DbQuery();

        $sql->select('p.`id_product`, s.`name` as seller_name, s.`postcode` AS seller_postal_code, fp.`id_feature_value`, pl.`name` as product_name');
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

    public function setLocationToProducts($id_seller = null) : int {

        $featureManager = new FeatureManager();
        $productManager = new ProductManager();
        $cityManager = new CityManager();

        $i = 0;

        $products = self::getNotLocatedProducts($id_seller);
        $mod_prefix = \Configuration::get('module_prefix');
        $id_feature = \Configuration::get($mod_prefix .'feature_id');

        \Configuration::updateValue($mod_prefix .'last_feature_value_import', time());

        if ($products) {
            foreach ($products as $product) {

                // Si le vendeur du produit a renseigner un code postal
                if ($product['seller_postal_code'] != '') {

                    $feature_value = $cityManager->getFeatureValuePostalCode($product['seller_postal_code']);
                    $id_feature_value = $feature_value->id;
                    $id_feature = $feature_value->id_feature;

                    // Une fois qu'on a toutes les infos, on peut lier le produit à son attribut.
                    $productManager->addFeatureProductImport($product['id_product'], (int) $id_feature, (int) $id_feature_value);
                    $i++;

                }
            }
        }
        return $i;
    }

    public function addFeatureProductImport($id_product, $id_feature, $id_feature_value) {

    }

    public function getProducts(): ?array {

        $lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');

        $sql = new \DbQuery();

        $sql->select('p.`id_product`, s.`name` as seller_name, s.`city`, fp.`id_feature_value`, pl.`name` as product_name');
        $sql->from('product', 'p');
        $sql->innerJoin('seller_product', 'sp', 'p.`id_product` = sp.`id_product`');
        $sql->innerJoin('seller', 's', 'sp.`id_seller` = s.`id_seller`');
        $sql->innerJoin('product_lang', 'pl', 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = '. $lang_id);
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_product` = p.`id_product`');
        $sql->orderBy('p.`id_product`');

        $products = \Db::getInstance()->executeS($sql);

        return $products ?: null;
    }


}
