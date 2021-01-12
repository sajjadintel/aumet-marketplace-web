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

    // formats money to a whole number or with 2 decimals
    public static function formatMoney($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
        if (is_numeric($number)) { // a number
            if (!$number) { // zero
                $money = ($cents == 2 ? '0.00' : '0'); // output zero
            } else { // value
                if (floor($number) == $number) { // whole number
                    $money = number_format($number, ($cents == 2 ? 2 : 0)); // format
                } else { // cents
                    $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
                } // integer or decimal
            } // value
            return $money;
        } // numeric
    } // formatMoney
}