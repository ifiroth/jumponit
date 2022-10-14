<?php

namespace JOI\Service;

class QrcodeManager {

    public function getQrcodes($id) {

        $sql = new \DbQuery();

        $sql->select('q.`link`');
        $sql->from('joi_qrcode', 'q');

        return \Db::getInstance()->executeS($sql);
    }
}
