<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

define('_MOD_PREFIX_', 'JOI_');

use JOI\Service\CustomerManager;
use JOI\Service\SqlManager;
use JOI\Service\TabManager;
use JOI\Service\CityManager;
use JOI\Service\FeatureManager;
use JOI\Service\ProductManager;

require_once __DIR__.'/vendor/autoload.php';

class JumpOnIt extends Module
{
    public function __construct()
    {
        $this->name = 'jumponit';
        $this->version = '1.0.0';
        $this->author = '2F Conseils & Consulting';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        $this->tab = 'front_office_features';
        # TODO : Add a key
        $this->module_key = '';

        parent::__construct();

        \Configuration::updateValue('module_prefix', _MOD_PREFIX_);
        \Configuration::updateValue(_MOD_PREFIX_.'module_name', $this->name);
        \Configuration::updateValue(_MOD_PREFIX_.'feature_label', "Ville");
        \Configuration::updateValue(_MOD_PREFIX_.'last_imported_feature_value', false);
        \Configuration::updateValue(_MOD_PREFIX_.'last_city_import', false);
        \Configuration::updateValue(_MOD_PREFIX_.'last_seller_general_warning', false);

        $this->displayName = $this->trans('Jump on the best opportunities close to you !', [], 'Modules.JumpOnIt.General');
        $this->description = $this->trans('This module filter those products to show the closests ones.', [], 'Modules.JumpOnIt.General');

        $this->confirmUninstall = $this->trans('Are you sure that you want to uninstall ?', [], 'Modules.JumpOnIt.General');

        $this->sqlManager = new SqlManager();
        $this->tabManager = new TabManager();
        $this->cityManager = new CityManager();
        $this->featureManager = new FeatureManager();
        $this->productManager = new ProductManager();
    }

    public function initContent()
    {
        $this->title = $this->trans('Jump on it !.', [], 'Modules.JumpOnIt.General');
    }

    public function install()
    {
        if (Module::isInstalled('jmarketplace') && Module::isEnabled('jmarketplace'))
        {
            return parent::install()

                && $this->tabManager->install()
                && $this->sqlManager->install()
                && $this->featureManager->initFeature()
                && $this->registerHook('displayHeader')
                && $this->registerHook('displayTop')
                && $this->registerHook('displayBanner')
                && $this->registerHook('displayIqitMenu')
                //&& $this->registerHook('filterCategoryContent')
                //&& $this->registerHook('filterProductSearch')
                //&& $this->registerHook('productSearchProvider')
                //&& $this->registerHook('actionProductSave')
                ;

        } else {
            return Tools::displayError('Install: Missing module jmarketplace');
        }
    }

    public function uninstall()
    {
        return parent::uninstall()

            && $this->sqlManager->uninstall()
            && $this->tabManager->uninstall()
            && $this->featureManager->deleteFeature()
            && $this->unregisterHook('displayHeader')
            && $this->unregisterHook('displayIqitMenu')
            && $this->unregisterHook('displayTop')
            && $this->unregisterHook('displayBanner')
            //&& $this->unregisterHook('filterCategoryContent')
            //&& $this->unregisterHook('filterProductSearch')
            //&& $this->unregisterHook('productSearchProvider')
            && $this->unregisterHook('actionProductSave')
            ;
    }

    public function getContent()
    {
        $output = 'Id de l\'attribut : '. Configuration::get(_MOD_PREFIX_.'feature_id') .'<br>';

        return $output;
    }


    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path .'assets/js/geolocation.js', );
    }

    public function hookDisplayTop($params)
    {
        // On regarde si on a un code postal enregistré dans l'user ou le cookie

        if ($this->context->cookie->__isset('joi_postal_code')) {

            $postalCode = (int) $this->context->cookie->__get('joi_postal_code');

        } else {

            if ($this->context->customer->id) {

                $customerManager = new CustomerManager();
                $postalCode = $customerManager->getPostalCode($this->context->customer->id);

            } else {

                $postalCode = 0;
            }
        }

        $cityManager = new CityManager();
        if ($postalCode)
            $geolocalisedCity = $cityManager->getCityNameByPostalCode($postalCode);

        else {
            $geolocalisedCity = null;
        }

        $this->context->smarty->assign([
            'postal_code' => $postalCode,
            'geolocalised_city' => $geolocalisedCity,
        ]);

        return $this->display(__FILE__, 'template/hook/getLocation.tpl');
    }

    public function hookDisplayBanner(array $params) {
        // On regarde si on a un code postal enregistré dans l'user ou le cookie

        if ($this->context->cookie->__isset('joi_postal_code')) {

            $postalCode = (int) $this->context->cookie->__get('joi_postal_code');

        } else {

            if ($this->context->customer->id) {

                $customerManager = new CustomerManager();
                $postalCode = $customerManager->getPostalCode($this->context->customer->id);

            } else {

                $postalCode = 0;
            }
        }

        $cityManager = new CityManager();
        if ($postalCode)
            $geolocalisedCity = $cityManager->getCityNameByPostalCode($postalCode);

        else {
            $geolocalisedCity = null;
        }

        $this->context->smarty->assign([
            'postal_code' => $postalCode,
            'geolocalised_city' => $geolocalisedCity,
        ]);

        return $this->display(__FILE__, 'template/hook/getLocation.tpl');
    }

    public function hookDisplayIqitMenu(array $params) {
        if ($this->context->cookie->__isset('joi_postal_code')) {

            $postalCode = (int) $this->context->cookie->__get('joi_postal_code');

        } else {

            if ($this->context->customer->id) {

                $customerManager = new CustomerManager();
                $postalCode = $customerManager->getPostalCode($this->context->customer->id);

            } else {

                $postalCode = 0;
            }
        }

        $cityManager = new CityManager();
        if ($postalCode)
            $geolocalisedCity = $cityManager->getCityNameByPostalCode($postalCode);

        else {
            $geolocalisedCity = null;
        }

        $this->context->smarty->assign([
            'postal_code' => $postalCode,
            'geolocalised_city' => $geolocalisedCity,
        ]);

        return $this->display(__FILE__, 'template/hook/getLocation.tpl');
    }

    /*
    public function hookFilterCategoryContent(array $params)
    {
        dump($params);
    }
    */

    public function hookActionProductSave(array $params)
    {
        // TODO : Check for city and add feature_value in case

        dump($params);
    }

    /*
    public function hookProductSearchProvider(&$params) {

        $query = $params['query'];
        return new JOI_facetedSearchProductSearchProvider($this);
    }
    */

    /*
    public function hookFilterProductSearch(array &$params)
    {
        // Check if position filter is on

        // [ ... ]

        // Get $sellers from this position
        // TODO : Seller::getSellersByLocation($zipcode)

        // Instead


        $sellers = Seller::getSellers((int)Context::getContext()->shop->id);
        $selectedSellers = JOI_Seller::getSellersByLocation('75014', $sellers);

        // End of Instead

        // Update &$params and unset those with wrong location

        $products = $params['searchVariables']['products'];

        foreach ($products as $key => $product) {
            $seller_id = Seller::getSellerByProduct($product->getId());

            if (!in_array($seller_id, $selectedSellers)) {
                unset($params['searchVariables']['products'][$key]);
            }
        }


        //dump($params);

    }
    */

    public function isUsingNewTranslationSystem()
    {
        return true;
    }
}
