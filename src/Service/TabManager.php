<?php

namespace JOI\Service;

use Tab;

class TabManager {

    public function install(): bool
    {

        $slug = 'joi_admin';
        $name = 'Localisation des produits';

        $tab = Tab::getInstanceFromClassName($slug);
        $tab->active = 1;
        $tab->id_parent = Tab::getIdFromClassName('ShopParameters');
        $tab->module = \Configuration::get('JOI_module_name');
        $tab->wording_domain = 'Admin.Navigation.Menu';
        $tab->class_name = $slug;
        $tab->route_name = $slug;
        $tab->wording = $name;
        $tab->name[\Configuration::get('PS_LANG_DEFAULT')] = $name;
        $tab->add();

        return true;
    }

    public function uninstall(): bool {

        $tab = Tab::getInstanceFromClassName('joi_admin');
        $tab->delete();
        return true;
    }
}
