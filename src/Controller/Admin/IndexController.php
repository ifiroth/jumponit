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

        return $this->render('@Modules/jumponit/template/admin/index.html.twig', [
            'notLocatedProducts' => ProductManager::getNotLocatedProducts(),
            'notLocatedSellers' => SellerManager::getNotLocatedSellers(),
            'locations' => FeatureManager::countValue(),
            'seller' => SellerManager::getNotLocatedSellers(),
            'last_imported_feature_value' => \Configuration::get($this->mod_prefix .'last_imported_feature'),
            'feature' => FeatureManager::getFeature(\Configuration::get($this->mod_prefix .'feature_id')),
            'action' => 'index',
        ]);
    }
}