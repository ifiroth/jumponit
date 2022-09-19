<?php

namespace JOI\Controller\Admin;

use JOI\Service\FeatureManager;
use JOI\Service\SellerManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use JOI\Service\ProductManager;

class IndexController extends FrameworkBundleAdminController
{
    public function __construct ()
    {
        $this->mod_prefix = \Configuration::get('module_prefix');
    }

    public function indexAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();
        $productManager = new ProductManager();
        $sellerManager = new SellerManager();
        $featureManager = new FeatureManager();

        $joi_products = [
            'notLocatedProducts' => $productManager->getNotLocatedProducts(),
            'last_feature_value_import' => \Configuration::get($this->mod_prefix .'last_feature_value_import'),
        ];

        $joi_sellers = [
            'notLocatedSellers' => $sellerManager->getNotLocatedSellers(),
            'last_seller_general_warning' => \Configuration::get($this->mod_prefix .'last_seller_general_warning'),
        ];

        $joi_cities = [
            'last_city_import' => \Configuration::get($this->mod_prefix .'last_city_import'),
        ];

        return $this->render('@Modules/jumponit/template/admin/index.html.twig', [

            'locations' => $featureManager->countValue(),
            'feature' => $featureManager->getFeature(\Configuration::get($this->mod_prefix .'feature_id')),
            'action' => 'index',
            'joi_products' => $joi_products,
            'joi_sellers' => $joi_sellers,
            'joi_cities' => $joi_cities,
        ]);
    }
}
