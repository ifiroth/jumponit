<?php

namespace JOI\Service;

class Debug {
    public static $id = 0;

    private static function getMargin($margin = '200px'): string {
        if (self::$id == 0) {
            $marginTop = '200px';
            self::$id++;
        } else {
            $marginTop = '0';
        }

        return 'margin-left: '. $margin .'; margin-top: '. $marginTop;
    }

    public static function dump($var, $margin = '100px'): void {

        echo '<pre style="'. self::getMargin($margin) .'">';
        var_dump($var);
        echo '</pre>';
    }

    public static function say(string $var): void {
        echo '<p style="'. self::getMargin() .'">'. $var .'</p>';
    }
}