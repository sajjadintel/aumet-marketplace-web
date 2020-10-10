<?php 

class Helper {

    public static function idListFromArray($array){

        $ids = '';
        foreach ($array as $key => $value){
            if ($ids != ''){
                $ids .= ', ';
            }
            $ids .= $key;
        }
        return $ids;
    }
}