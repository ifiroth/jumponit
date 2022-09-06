<?php

namespace JOI\Controller\Admin\Product;

use JOI\Service\CityManager;
use JOI\Service\ProductManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{

    public function refreshAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();
        $productLocationsSet = ProductManager::setLocationToProducts();

        switch ($productLocationsSet)
        {
            case 1:
                $flash = 'success';
                $message = $productLocationsSet .' produit mis à jour';
                break;

            case 0:
                $flash = 'warning';
                $message = 'Aucun produit mis à jour';
                break;

            default:
                $flash = 'success';
                $message = $productLocationsSet .' produits mis à jour';
                break;
        }

        $this->addFlash($flash, $message);
        return $this->redirectToRoute('joi_admin');
    }

    public function refreshSingleAction($seller) : Response
    {
        $productLocationsSet = ProductManager::setLocationToProducts($seller);

        switch ($productLocationsSet)
        {
            case 1:
                $flash = 'success';
                $message = $productLocationsSet .' produit mis à jour';
                break;

            case 0:
                $flash = 'warning';
                $message = 'Aucun produit mis à jour';
                break;

            default:
                $flash = 'success';
                $message = $productLocationsSet .' produits mis à jour';
                break;
        }

        $this->addFlash($flash, $message);

        return $this->redirectToRoute('joi_admin_seller_detail');
    }

    public function detailAction() : Response
    {
        $cityManager = new CityManager();

        return $this->render('@Modules/jumponit/template/admin/product/index.html.twig', [
            'notLocatedProducts' => ProductManager::getNotLocatedProducts(),
            'action' => 'product',
            'cities' => $cityManager->getCities(),
        ]);
    }
}
