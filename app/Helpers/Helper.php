<?php
namespace App\Helpers;
use App\Consts;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class Helper
{
    public static function GetPer($value1, $value2){
        if($value1 == null && $value2 == null){
            return 0;
        }
        return round($value1 /($value2 + $value1) * 100, 4);
    }

	public static function limit_text($text, $limit) {
		if (str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr($text, 0, $pos[$limit]) . '...';
		}
		return $text;
	}

	public static function limit_string ($string, $limit){
	    $str = $string;
        if(strlen($string) > $limit) {
           $str =  substr($string,0,40).'...';
        }
        return $str;
	}


	public static function getBackgound(){
        $bg = Cache::get('background');
        if(isset($bg) && !empty($bg) ){
            return Cache::get('background');
        } elseif (isset(Session::get('username')->background) && !empty(Session::get('username')->background)){
            return Session::get('username')->background;
        } else {
            return "solid-bg-3";
        }
    }

    public static function getLateClass($start_time, $created){
        $time = Carbon::parse($created)->format('H:i:s');
        return ($start_time > $time) == true ? "earlier" : "" ;
    }
    public static function getTimeLate($start_time, $created){
        $time = Carbon::parse($created)->format('H:i:s');
        $prefix = ($start_time < $time) == true ? "-" : "+" ;
        return $prefix.Carbon::parse($time)->diffInMinutes($start_time);
    }

    public static function formatDate($created){
        return Carbon::parse($created)->format("Y-m- H:i:s");
    }

    public static function convertMinuteToTime($minute){
        return intdiv($minute, 60).':'. ($minute % 60);
    }

    public static function convertMinuteToDay($minute){
        return intdiv($minute, 60).'.'. ($minute % 60);
    }

}
