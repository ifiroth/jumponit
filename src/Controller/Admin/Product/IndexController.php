<?php

namespace JOI\Controller\Admin\Product;

use JOI\Service\CityManager;
use JOI\Service\ProductManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{
    public function refreshAction($id_seller = null) : Response
    {
        $productManager = new ProductManager();

        // $em = $this->getDoctrine()->getManager();
        $productLocationsSet = $productManager->setLocationToProducts($id_seller);

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

        if ($id_seller) {
            $redirectToRoute = 'joi_admin_seller_detail';
        } else {
            $redirectToRoute = 'joi_admin';
        }

        $this->addFlash($flash, $message);
        return $this->redirectToRoute($redirectToRoute);
    }

    public function detailAction() : Response
    {
        $cityManager = new CityManager();
        $productManager = new ProductManager();

        $products = $productManager->getProducts();
        dump($products);

        return $this->render('@Modules/jumponit/template/admin/product/index.html.twig', [
            'products' => $productManager->getProducts(),
            'action' => 'product',
            'cities' => $cityManager->getCities(),
        ]);
    }
}
