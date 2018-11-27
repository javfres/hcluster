<?php

namespace Javfres\HCluster;

class Utils {


    public static function title($str, $size=50){
        error_log(trim(self::strtitle($str,"\n",$size)));
    }


    public static function strtitle($str, $breakline="\n", $size=50){

        $res = '';

        $res .= str_repeat("-",$size) . $breakline;
        $str = str_pad($str,$size-4," ",STR_PAD_BOTH);
        $res .= "- $str -" . $breakline;
        $res .= str_repeat("-",$size) . $breakline;

        return $res;
    }


    public static function groupcolor($group = null){

        if($group === null){
            return "\033[0m";
        }

        $colors = [
            "\033[0;34m", // blue
            "\033[0;32m", // green
            "\033[0;36m", // cyan
            "\033[0;31m", // red
            "\033[0;35m", // purple
            "\033[0;33m", // brown
            "\033[1;33m", // yellow
        ];

        return $colors[ $group % count($colors) ];

    }


}
