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
}