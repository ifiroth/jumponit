<?php

class JOI_Seller // extends Seller
{
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function getSellersByLocation($zipcode, $sellers): array
    {
        $selectedSellers = [];

        foreach ($sellers as $seller) {
            // if ($seller['postcode'] == $zipcode) {
            if (true) {
                $selectedSellers[] = $seller['id_seller'];
            }
        }

        return $selectedSellers;
    }
}