<?php

namespace JOI\Service;

class CustomerManager {

    public function getPostalCode($id) {

        $sql = new \DbQuery();

        $sql->select('c.`postal_code`');
        $sql->from('customer', 'c');
        $sql->where('c.`id_customer` = '. (int) $id);

        return \Db::getInstance()->getRow($sql)['postal_code'];
    }
}
