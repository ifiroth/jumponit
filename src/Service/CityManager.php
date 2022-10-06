<?php

namespace JOI\Service;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class CityManager {

    public function __construct() {
        $this->mod_prefix = \Configuration::get('module_prefix');
        $this->feature_id = \Configuration::get($this->mod_prefix .'feature_id');
        $this->id_lang = \Configuration::get('PS_LANG_DEFAULT');
    }

    public function getDisabledCities() : ?array {
        return null;
    }

    public function getCities(array $orderClauses = [], array $whereClauses = [], int $page = 0, int $limit = 50) : ?array {

        $sortableFields = $searchableFields = ['postal_code', 'nom_comm', 'nom_dept', 'nom_reg'];

        $sql = new \DbQuery();

        $sql->select('
            c.`id_feature_value`,
            c.`postal_code`,
            c.`nom_comm`,
            c.`nom_dept`,
            c.`nom_reg`,
            c.`id_city`,
            fp.`id_product` AS product_count,
            sp.`id_seller` AS seller_count
        ');
        $sql->from('joi_city', 'c');
        $sql->leftJoin('feature_product', 'fp', 'fp.`id_feature_value` = c.`id_feature_value`');
        $sql->leftJoin('seller_product', 'sp', 'sp.`id_product` = fp.`id_product`');
        $sql->groupBy('c.`nom_comm`');

        function formatOrder(string $f, string $d) : string {
            return 'c.`'. $f .'` '. $d;
        }

        function formatWhere(string $k, string $v) : string {
            return 'c.`'. $k .'` LIKE "%'. $v .'%"';
        }

        if (is_array($orderClauses)) {

            $order = [];

            foreach ($orderClauses as $field => $direction) {

                if (in_array($field, $sortableFields)) $order[] = formatOrder($field, $direction);
            }

            $sql->orderBy(implode(',', $order));

        } else {

            $sql->orderBy('c.`id_feature_value` DESC, c.`nom_comm` ASC');
        }

        if (is_array($whereClauses)) {

            $where = [];

            foreach ($whereClauses as $key => $value) {

                if (in_array($key, $searchableFields)) $where[] = formatWhere($key, $value);
            }

            $sql->where(implode(' AND ', $where));
        }

        $sql->limit($limit, ($page * $limit));

        // TODO : Optimize query with COUNT(fp.`id_product`) AS product_count, + id_seller

        $cities = \Db::getInstance()->executeS($sql);

        return $cities ?: null;
    }

    public function getCitiesCount() : int {
        $sql = new \DbQuery();

        $sql->select('
            c.`id_city`
        ');
        $sql->from('joi_city', 'c');

        return \Db::getInstance()->numRows($sql);
    }

    public function importCities() : int {

        $mod_prefix = \Configuration::get('module_prefix');
        \Configuration::updateValue($mod_prefix .'last_city_import', time());

        $cityFile = file_get_contents(__DIR__ .'/../../utils/inseeCities.json');
        $cities = json_decode($cityFile, true);

        $sqlManager = new SqlManager();
        $sqlManager->reset('city');

        $i = 0;

        foreach ($cities as $city) {
            $db = \Db::getInstance();
            $fields = $city['fields'];

            $shape = $fields['geo_shape']['coordinates'];

            while (is_array($shape[0])) {

                $previousArray = $shape;
                $shape = $shape[0];

                if (!is_array($shape[0]))
                {
                    $shape = $previousArray;
                    break;
                }
            }

            // TODO : Switch to Neo4j to gain database access time

            $db->insert('joi_city', [
                'postal_code' => (int) $fields['postal_code'],
                'code_dept' => (int) $fields['code_dept'],
                'code_reg' => (int) $fields['code_reg'],
                'nom_comm' => htmlentities($fields['nom_comm'], ENT_QUOTES),
                'nom_dept' => htmlentities($fields['nom_dept'], ENT_QUOTES),
                'nom_reg' => htmlentities($fields['nom_region'], ENT_QUOTES),
                'geo_lat' => $fields['geo_point_2d'][0],
                'geo_long' => $fields['geo_point_2d'][1],
                'geo_shape' => json_encode($shape),
                'statut' => htmlentities($fields['statut'], ENT_QUOTES),
            ]);

            $i++;
        }

        return $i;
    }

    public function getCityName($id_city) : ?string {
        $sql = new \DbQuery();

        $sql->select('c.`nom_comm`');
        $sql->from('joi_city', 'c');
        $sql->where('c.`id_city` = '. $id_city);

        $result = \Db::getInstance()->getRow($sql);

        return $result['nom_comm'];
    }

    public function getFeatureValuePostalCode($postal_code) : ?\FeatureValue {

        $featureManager = new FeatureManager();

        $city_name = $this->getCityNameByPostalCode($postal_code);

        $id_feature_value = $featureManager->getFeatureValueIdByName($city_name);

        try {
            return new \FeatureValue($id_feature_value);

        } catch (\PrestaShopDatabaseException $e) {

            return null;

        } catch (\PrestaShopException $e) {

            return null;
        }
    }

    public function getCityNameByPostalCode(int $postal_code) : string {
        $sql = new \DbQuery();

        $sql->select('c.`nom_comm`');
        $sql->from('joi_city', 'c');
        $sql->where('c.`postal_code` = '. (int) $postal_code);

        $result = \Db::getInstance()->getRow($sql);

        return $result['nom_comm'];
    }

    public function getCitiesByArea(float $range, float $long, float $lat) {

        $sql = new \DbQuery();

        $sql->from('joi_city', 'c');

        $sql->select('c.`nom_comm`, c.`geo_shape`, c.`postal_code`, c.`geo_lat`, c.`geo_long`, c.`nom_dept`, c.`nom_reg`');
        $sql->where('
            (c.`geo_lat` BETWEEN '. $lat * ( 1 - $range ) .' AND '. $lat * ( 1 + $range ) .')
        AND (c.`geo_long` BETWEEN '. $long * ( 1 - $range ) .' AND '. $long * ( 1 + $range ) .')
        ');
        $sql->orderBy('c.`nom_comm`');

        return \Db::getInstance()->executeS($sql);
    }

    public function locateCityByPostalCode(int $postalCode): ?array {

        $sql = new \DbQuery();

        $sql->from('joi_city', 'c');

        $sql->select('c.`nom_comm`, c.`geo_shape`, c.`postal_code`, c.`geo_lat`, c.`geo_long`, c.`nom_dept`, c.`nom_reg`');
        $sql->where('c.`postal_code` = '. $postalCode);

        return \Db::getInstance()->getRow($sql) ?: null;

    }

    public function locateCityByGPS(array $coords): ?array {

        $cities = $this->getCitiesByArea(.07, $coords[0], $coords[1]);

        foreach ($cities as $city) {

            if ($this->pointInPolygon(json_decode($city['geo_shape']), $coords, $city['nom_comm'])) {

                return $city;
            }
        }
        return null;
    }

    public function pointInPolygon(?array $polygon, array $point, string $cityName) {

        //A point is in a polygon if a line from the point to infinity crosses the polygon an odd number of times
        $odd = false;
        //For each edge (In this case for each point of the polygon and the previous one)
        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $i++) {
            //If a line from the point into infinity crosses this edge

            if ((($polygon[$i][1] > $point[1]) !== ($polygon[$j][1] > $point[1]))
                // One point needs to be above, one below our y coordinate
                // ...and the edge doesn't cross our Y coordinate before our x coordinate (but between our x coordinate
                // and infinity)
                && ($point[0] < (($polygon[$j][0] - $polygon[$i][0]) * ($point[1] - $polygon[$i][1]) /
                        ($polygon[$j][1] - $polygon[$i][1]) + $polygon[$i][0]))) {
                // Invert odd
                $odd = !$odd;
            }
            $j = $i;
        }
            //If the number of crossings was odd, the point is in the polygon
            return $odd;
    }

    public function getCityIdByName($name): ?int {

        $sql = new \DbQuery();

        $sql->select('c.`id_city`');
        $sql->from('joi_city', 'c');
        $sql->where('c.`nom_comm` = "'. htmlentities(strtoupper($name)) .'"');

        $result = \Db::getInstance()->getRow($sql);

        return $result ? $result['id_city'] : null;
    }

    public function saveCity(array $city, ?int $id_customer): bool {

        if ($id_customer) {

            return \Db::getInstance()->update(
                'customer',
                ['geo_code' => $city['postal_code']],
                'id_customer ='. $id_customer,
                1
            );
        }

        return true;
    }

    /* AVORTED
    public function toggleActivity($id_city, $state, $name) : ?bool {

        $featureManager = new FeatureManager();

        if ($state) {

            $id_feature_value = $featureManager->hasValueId($id_city);

            if (!$id_feature_value) {

                $id_feature_value = $featureManager->createValue($name, $id_city);
            }

        } else {
            $id_feature_value = null;
        }

        $db = \Db::getInstance();

        $result = $db->update('joi_city', [
            'id_feature_value' => $id_feature_value
        ], 'id_city = '. $id_city, 1);

        return $result;

    }
    */

    public function linkFeatureToCity($id_city, $id_feature_value) {

        $db = \Db::getInstance();

        $result = $db->update('joi_city', [
            'id_feature_value' => $id_feature_value
        ], 'id_city = '. $id_city, 1);

        return $result;
    }
}
