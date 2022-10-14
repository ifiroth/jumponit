<?php

namespace JOI\Controller\Admin\Qrcode;

use JOI\Service\CityManager;
use JOI\Service\FeatureManager;
use JOI\Service\QrcodeManager;
use JOI\Service\Utils;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use QRcode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        $this->qrcodeManager = new QrcodeManager();
    }

    public function indexAction() : Response
    {
       $qrcodes = $this->qrcodeManager->getQrcodes();

        return $this->render('@Modules/jumponit/template/admin/qrcode/index.html.twig', [
            'qrcodes' => $this->qrcodeManager->getQrcodes(),
        ]);
    }

    public function createAction() : Response
    {
        $this->addFlash('success', 'Qrcode créé');
        return $this->redirectToRoute('joi_admin_qrcode');
    }
}
