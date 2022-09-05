<?php

namespace JOI\Controller\Admin\Product;

use JOI\Service\ProductManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{

    public function refreshAction() : Response
    {
        // $em = $this->getDoctrine()->getManager();
        $productLocationsSet = ProductManager::setLocationToProducts();

        $this->addFlash('success', $productLocationsSet .' vendeurs mis à jour');
        return $this->redirectToRoute('joi_admin');
    }

    public function detailAction() : Response
    {
        return $this->render('@Modules/jumponit/template/admin/seller/index.html.twig', [
            'nonLocatedSellers' => ProductManager::getNotLocatedProducts(),
        ]);
    }
}