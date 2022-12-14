<?php

namespace JOI\Service;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use JOI\Service\Debug;

class SellerManager
{

    static public function getNotLocatedSellers(): ?array
    {

        $sql = new \DbQuery();

        $sql->select('s.`id_seller`, s.`name`, s.`city`');
        $sql->from('seller', 's');
        $sql->where('s.`city` = ""');
        $sql->orderBy('s.`name`');

        $sellers = \Db::getInstance()->executeS($sql);

        return $sellers ?: null;
    }

    static public function getSellers(): ?array
    {

        $lang_id = (int) \Configuration::get('PS_LANG_DEFAULT');

        $sql = new \DbQuery();

        $sql->select('
            COUNT(p.`id_product`) AS product_count,
            s.`name`,
            s.`city`,
            SUM(CASE WHEN fp.`id_feature_value` IS NULL THEN 1 ELSE 0 END) product_not_located_count,
            s.`id_seller` AS id
        ');
        $sql->from('product', 'p');
        $sql->innerJoin('seller_product', 'sp', 'p.`id_product` = sp.`id_product`');
        $sql->innerJoin('seller', 's', 'sp.`id_seller` = s.`id_seller`');
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_product` = p.`id_product`');
        $sql->groupBy('s.`name`');
        $sql->orderBy('s.`name`');

        $sellers = \Db::getInstance()->executeS($sql);

        return $sellers ?: null;
    }
}
