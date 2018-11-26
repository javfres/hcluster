<?php

namespace Javfres\HCluster;

class JH {


    public static function log($something, $full=false){

        if($something instanceof \Exception){
            $e = $something;
            $m = $e->getMessage();
            $f = $e->getFile();
            $l = $e->getLine();
            error_log("$m $f:$l");
            return;
        }

        error_log(self::better_print_r($something,0,$full));

    }


    public static function better_print_r($var, $level=0, $full=false){

        $spaces = str_repeat( " " , 4 * $level);
        $out = null;

        if($var === false) $out = 'false';
        if($var === true) $out = 'true';
        if(is_null($var)) $out = 'null';
        if(is_numeric($var)) $out = "$var";
        if(is_string($var)){

            if(!$full && strlen($var) > 80){
                $s = substr($var,0,80-2);
                $out = "'$s...";
            } else {
                $out = "'$var'";
            }
        }


        if(!is_null($out)) return "$out";

        if(is_array($var)){
            $out = "Array (\n";
            $out .= self::better_print_r_array_items($var, $level, $full);
            $out .= $spaces . ")";
            return $out;
        }

        if(is_object($var) && $var instanceof Model){
            $out = "Model: " .get_class($var) . " (\n";
            $out .= self::better_print_r_array_items($var->toArray(), $level, $full);
            $out .= $spaces . ")";
            return $out;
        }

        if(is_object($var)){
            $out = get_class($var) . " (\n";
            $out .= self::better_print_r_array_items($var, $level, $full);
            $out .= $spaces . ")";
            return $out;
        }

        return "-- Not supported --";
    }

    private static function better_print_r_array_items($array, $level=0, $full){

        $spaces = str_repeat( " " , 4 * $level);
        $out = '';

        foreach ($array as $k => $v){
            $out .= "$spaces    [$k] => " . self::better_print_r($v,$level+1,$full) . "\n";
        }

        return $out;

    }


    /**
     * This method does nothing in portal but throws an exception in local and dev.
     * @param string $msg optional message in the exception
     * @param bool $throw Throw the exception or not
     * @throws \Exception
     */
    public static function deprecated($msg = 'This is deprecated', $throw=true){
        if(App::environment('portal')) return;
        error_log("-------------- Deprecated --------------\n$msg\n");

        $user = get_current_user();
        JH::log($user);

        if($throw) throw new \Exception($msg);
    }




    public static function log_memory(){

        $b = memory_get_usage(true);
        $mb = $b / (1e6);

        $b_max = memory_get_peak_usage(true);
        $mb_max = $b_max / (1e6);

        self::log("Memory usage: $mb Mb, Max memory used: $mb_max Mb");

    }



    public static function binary_search($a, $value, $key=null, $compare=null) {

        if(is_null($compare)){
            if(is_null($key)) {
                $compare = function ($a, $b) {
                    return ($a < $b) ? -1 : (($a > $b) ? 1 : 0);
                };
            } else {
                $compare = function ($a, $b) use ($key) {
                    return ($a[$key] < $b) ? -1 : (($a[$key] > $b) ? 1 : 0);
                };
            }
        }

        $lo = 0;
        $hi = sizeof($a) - 1;

        while ($lo <= $hi) {
            $mid = (int)(($hi - $lo) / 2) + $lo;
            $cmp = call_user_func($compare, $a[$mid], $value);

            if ($cmp < 0) {
                $lo = $mid + 1;
            } elseif ($cmp > 0) {
                $hi = $mid - 1;
            } else {
                return $mid;
            }
        }
        return -1;
    }



    static $first_timer = null;
    static $last_timer = null;

    public static function logtimer($something){

        $current = microtime(true);

        if(is_null(self::$first_timer)){
            self::$first_timer = $current;
            $diff_b = 0;
            $diff = 0;
        } else {
            $diff_b = $current - self::$first_timer;
            $diff = $current - self::$last_timer;
        }

        self::$last_timer = $current;

        // Format elapsed
        $diff_b = date("i:s" , $diff_b);
        $diff = sprintf('%02.3f', $diff);


        self::log("[$diff_b][$diff s] " . print_r($something,true));
    }


    /**
     * Returns a logger object that saves the log to a file
     * @param $file string The file path to store the log
     * @param bool $add_date bool Add the date to the file path
     * @return JHLogger
     */
    public static function filelog($file, $add_date=false){
        return new JHLogger($file, $add_date);
    }


}
