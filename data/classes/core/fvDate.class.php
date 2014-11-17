<?php
    class fvDate
    {
        static $uaMonths = array(
        1   =>  "Січня",
                "Лютого",
                "Березня",
                "Квітня",
                "Травня",
                "Липня",
                "Червня",
                "Серпня",
                "Вересня",
                "Жовтня",
                "Листопада",
                "Грудня" );
                
        static $ruMonths = array(
        1   =>  "января",
                "февраля",
                "марта",
                "апреля",
                "мая",
                "июня",
                "июля",
                "августа",
                "сентября",
                "октября",
                "ноября",
                "декабря" );
        
        /**
        * возвращает имя месяца
        *    
        * @param int $m
        * @param string $lang
        * @return mixed
        */
        static function getMonthName( $m, $lang = "ua" )
        {
            $arrName = ($lang."Months");
            $months = self::$$arrName;
            
            return $months[ intval( $m ) ];
        }
                
        static public function getReadableDate( $date, $lang = "ua", $showTime = false )
        {
            $stamp = strtotime( $date );
            $date = date( "d", $stamp ) . " " . self::getMonthName( date( "m", $stamp ), $lang ) . " " . date( "Y", $stamp ) . self::getYearAcronym( $lang );
            
            $date .= ( $showTime ) ? date( " H:i:s", $stamp ) : "";
            
            return $date;
        }
        
        static public function getYearAcronym( $lang = "ua" )
        {
            switch( $lang )
            {
                case "ru":
                    $toReturn = " г.";
                    break;
                case "ua":
                default:
                    $toReturn = " p.";
                    break;
            }
            
            return $toReturn;
        }

    }
?>
