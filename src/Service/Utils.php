<?php

namespace JOI\Service;

class Utils {
    static public function pagination(int $p, int $total, int $range = 3) : array {

        $pages = [];

        for ($i = 0; $i <= ($range - 1); $i++) {


            $pages[$i] = $i;

            if ($total > $range - $i ) $pages[$total - $i] = $total - $i;
        }

        if ($p > $range && $p < $total - $range) $pages[$p] = $p;
        if ($p - 1 > $range && $p - 1 < $total - $range) $pages[$p - 1] = $p - 1;
        if ($p + 1 > $range && $p + 1 < $total - $range) $pages[$p + 1] = $p + 1;

        sort($pages);

        foreach ($pages as $key => $page) {
            if (isset($pages[$key - 1]) && is_int($pages[$key - 1]) && ($page - 1) > $pages[$key - 1]) {
                array_splice($pages, $key, 0, ['...']);
            }
        }

        // TODO : debug that sh*t

        // TODO : report post request on filter

        return $pages;
    }
}