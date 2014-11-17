<?php
    class fvHuman
    {
        /**
        * Трансформирует число в строку
        * @param float $inn
        * @param bool $stripkop
        * @return string
        */
        function NumberToString( $inn, $stripkop=false ) 
        {
            $nol = 'ноль';
            $str[100]= array('','сто','двісті','триста','чотириста','п\'ятсот','шістсот', 'сімсот', 'вісімсот','дев\'ятсот');
            $str[11] = array('','десять','одиннадцять','дванадцять','тринадцять', 'чотирнадцять','п\'ятнадцять','шістнадцять','сімнадцять', 'вісімнадцять','дев\'ятнадцять','двадцять');
            $str[10] = array('','десять','двадцять','тридцять','сорок','п\'ятдесят', 'шістдесят','сімдесят','вісімдесят','дев\'яносто');
            $sex = array(
                array('','один','два','три','чотыре','п\'ять','шість','сімь', 'вісемь','девять'),// m
                array('','одна','дві','три','четыре','пять','шість','сімь', 'вісім','дев\'ять') // f
            );
            $forms = array(
            array('копійка', 'копійки', 'копійок', 1), // 10^-2
            array('гривня', 'гривні', 'гривень',  0), // 10^ 0
            array('тысяча', 'тысячі', 'тысяч', 1), // 10^ 3
            array('мильйон', 'мильйона', 'мильйонів',  0), // 10^ 6
            array('мильярд', 'мильярда', 'мильярдів',  0), // 10^ 9
            array('триллион', 'триллиона', 'триллионов',  0), // 10^12
            );
            $out = $tmp = array();
            // Поехали!
            $tmp = explode('.', str_replace(',','.', $inn));
            $rub = number_format($tmp[ 0], 0,'','-');
            if ($rub== 0) $out[] = $nol;
            // нормализация копеек
            $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
            $segments = explode('-', $rub);
            $offset = sizeof($segments);
            if ((int)$rub== 0) 
            { // если 0 рублей
                $o[] = $nol;
                $o[] = self::morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
            }
            else {
                foreach ($segments as $k=>$lev) {
                    $sexi= (int) $forms[$offset][3]; // определяем род
                    $ri = (int) $lev; // текущий сегмент
                    if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                        $offset--;
                        continue;
                    }
                    // нормализация
                    $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
                    // получаем циферки для анализа
                    $r1 = (int)substr($ri, 0,1); //первая цифра
                    $r2 = (int)substr($ri,1,1); //вторая
                    $r3 = (int)substr($ri,2,1); //третья
                    $r22= (int)$r2.$r3; //вторая и третья
                    // разгребаем порядки
                    if ($ri>99) $o[] = $str[100][$r1]; // Сотни
                    if ($r22>20) {// >20
                        $o[] = $str[10][$r2];
                        $o[] = $sex[ $sexi ][$r3];
                    }
                    else { // <=20
                        if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                        elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
                    }
                    // Рубли
                    $o[] = self::morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
                    $offset--;
                }
            }
            // Копейки
            if (!$stripkop) {
                $o[] = $kop;
                $o[] = self::morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
            }
            return preg_replace("/\s{2,}/",' ',implode(' ',$o));
        }

        /**
        * Склоняем словоформу
        */
        function morph($n, $f1, $f2, $f5) {
            $n = abs($n) % 100;
            $n1= $n % 10;
            if ($n>10 && $n<20) return $f5;
            if ($n1>1 && $n1<5) return $f2;
            if ($n1==1) return $f1;
            return $f5;
        } 
    }
?>
