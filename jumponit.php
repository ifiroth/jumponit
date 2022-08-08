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

        $this->displayName = $this->trans('Get the best opportunties next to you !', [], 'Modules.JumpOnIt.General');
        $this->description = $this->trans('This module filter those products to show the closests ones.', [], 'Modules.JumpOnIt.General');

        $this->confirmUninstall = $this->trans('Are you sure that you want to uninstall ?', [], 'Modules.JumpOnIt.General');
    }

    public function initContent()
    {
        $this->title = $this->trans('Jump on it !.', [], 'Modules.JumpOnIt.General');
    }

    public function install()
    {
        return parent::install()
            // && $this->registerHook('filterCategoryContent')
            && $this->registerHook('filterProductSearch')
            ;
    }

    public function uninstall()
    {
        return parent::uninstall()
            // && $this->unregisterHook('filterCategoryContent')
            && $this->unregisterHook('filterProductSearch')
            ;
    }
    /*
    public function hookFilterCategoryContent(array $params)
    {
        dump($params);
    }
    */

    public function hookFilterProductSearch(array $params)
    {
        dump($params);
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }
}