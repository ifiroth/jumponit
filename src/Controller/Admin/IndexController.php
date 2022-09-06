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

        $joi_products = [
            'notLocatedProducts' => ProductManager::getNotLocatedProducts(),
            'last_feature_value_import' => \Configuration::get($this->mod_prefix .'last_feature_value_import'),
        ];

        $joi_sellers = [
            'notLocatedSellers' => SellerManager::getNotLocatedSellers(),
            'last_seller_general_warning' => \Configuration::get($this->mod_prefix .'last_seller_general_warning'),
        ];

        return $this->render('@Modules/jumponit/template/admin/index.html.twig', [

            'locations' => FeatureManager::countValue(),
            'feature' => FeatureManager::getFeature(\Configuration::get($this->mod_prefix .'feature_id')),
            'action' => 'index',
            'joi_products' => $joi_products,
            'joi_sellers' => $joi_sellers,
        ]);
    }
}
