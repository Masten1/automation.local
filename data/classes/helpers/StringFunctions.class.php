<?php
/**
 * User: apple
 * Date: 06.06.12
 * Time: 15:01
 */
class StringFunctions {

    static function simplifyText( $text ) {
        if( ! self::checkUtf8($text) )
            $text = mb_convert_encoding($text, 'utf-8', 'cp-1251');

        if (function_exists('tidy_parse_string')) {
            $tidy = tidy_parse_string($text, array(), "utf8");
            $tidy->cleanRepair();
            $text = (string)$tidy;
        }

        $patterns = array(
            '/<script[^>]*?>.*?<\/script[^>]*?>/isu',
            '/<style[^>]*?>.*?<\/style[^>]*?>/isu',
            '/</iu',
            '/>/iu',
            '/\R/iu',
            '/[+&*:-]+/iu'
        );
        $replaces = array(
            ' ',
            ' ',
            ' <',
            '> ',
            ' ',
            ' ',
        );
        $text = preg_replace($patterns, $replaces, $text);
        $text = preg_replace("/\s+/iu", " ", strip_tags($text));
        $text = preg_replace("/\s+(\pP)/iu", "\\1", $text);
        $text = preg_replace("/[^\s\pP\pL\d]/iu", "",$text);

        return trim($text);
    }

    static function findWords( $body, Array $words ) {
        $body   = self::simplifyText( $body );
        $result = array();

        foreach( $words as $word ) {
            $word  = self::simplifyText(trim( $word ));
            $pword = preg_quote( $word, "/" );
            if( !empty( $word ) && preg_match( "/(^|\P{L}){$pword}(\P{L}|$)/iu", $body ) > 0 ) {
                $result[] = $word;
            }
        }

        return $result;
    }

    static function checkUtf8( $str ) {
        $len = strlen( $str );
        for( $i = 0 ; $i < $len ; $i++ ) {
            $c = ord( $str[$i] );
            if( $c > 128 ) {
                if( ( $c > 247 ) ) return false; elseif( $c > 239 ) $bytes = 4; elseif( $c > 223 ) $bytes = 3; elseif( $c > 191 ) $bytes = 2; else return false;
                if( ( $i + $bytes ) > $len ) return false;
                while( $bytes > 1 ) {
                    $i++;
                    $b = ord( $str[$i] );
                    if( $b < 128 || $b > 191 ) return false;
                    $bytes--;
                }
            }
        }

        return true;
    }

    static function humanReadableSeconds( $seconds, $depth = 1, $useReductions = true ){

        $depth--;

        /*** get the weeks ***/
        $weeks = intval(intval($seconds) / 3600 / 24 / 7);
        if( $weeks > 4 )
            return "more than month";

        if( $weeks > 0 ){
            $diff = $seconds - $weeks*3600*24*7;
            if( $useReductions )
                $string = $weeks . "w";
            else
                $string = $weeks . " week" . ($weeks > 1 ? 's' : '');

            if( intval($diff / 3600 / 24) > 0 && $depth )
                $string .= ( $useReductions ? " " : ", " ) . self::humanReadableSeconds( $diff, $depth );

            return $string;
        }

        /*** get the days ***/
        $days = intval(intval($seconds) / 3600 / 24);
        if( $days > 0 ) {
            $diff = $seconds - $days*3600*24;
            if( $useReductions )
                $string = $days . "d";
            else
                $string = $days . " day" . ($days > 1 ? 's' : '');

            if( intval($diff / 3600) > 0 && $depth )
                $string .= ( $useReductions ? " " : ", " ) . self::humanReadableSeconds( $diff, $depth );

            return $string;
        }

        /*** get the hours ***/
        $hours = intval(intval($seconds) / 3600);
        if( $hours > 0 ) {
            $diff = $seconds - $hours*3600;
            if( $useReductions )
                $string = $hours . "h";
            else
                $string = "$hours hour" . ($hours > 1 ? 's' : '');

            if( intval($diff / 60) > 0 && $depth )
                $string .= ( $useReductions ? " " : ", " ) . self::humanReadableSeconds( $diff, $depth );

            return $string;
        }

        /*** get the minutes ***/
        $minutes = bcmod((intval($seconds) / 60), 60);
        if( $minutes > 0 ){
            $diff = $seconds - $minutes*60;
            if( $useReductions )
                $string = $minutes . "m";
            else
                $string = "$minutes minute" . ($minutes > 1 ? 's' : '');

            if( intval($diff) > 0 && $depth )
                $string .= ( $useReductions ? " " : ", " ) . self::humanReadableSeconds( $diff, $depth );

            return $string;
        }

        /*** get the seconds ***/
        $seconds = bcmod(intval($seconds), 60);
        if( $useReductions )
            return $seconds . "s";
        else
            return "$seconds second" . ($seconds > 1 ? 's' : '');
    }

    static function parseException( Exception $e ){
        if( $e instanceof LogicException )
            return "<br/><div class='logic exception'><h1>{$e->getMessage()}</h1></div>";

        if( !FV_DEBUG_MODE || !defined('FV_DEBUG_MODE') )
            return "<br/><div class='exception'><h1>An error occurred</h1><p>{$e->getMessage()}</p></div>";

        $trace = "";

        foreach( $e->getTrace() as $i => $line ){
            foreach( $line['args'] as &$arg ){
                if( is_null($arg) )
                    $arg = "null";
                if( is_string($arg) )
                    $arg = "\"{$arg}\"";
                if( is_array($arg) )
                    $arg = '<a title="' . str_replace( '"', '&quote;', print_r($arg, true) ) . '">Array</a>';
                if( is_object($arg) )
                    $arg = '<a title="' . str_replace( '"', '&quote;', print_r($arg, true) ) . '">'.get_class($arg).'</a>';
            }
            $args = implode(", ", $line['args']);

            if( !empty($line['class']) )
                $function = "{$line['class']}{$line['type']}{$line['function']}({$args})";
            else
                $function = "{$line['function']}({$args})";

            $line['file'] = str_replace( str_replace( "data/", "", FV_ROOT ), "/", $line['file'] );
            $trace .= "<tr><td>{$i}</td><td>{$function}</td><td>{$line['file']}</td><td>{$line['line']}</td></tr>";
        }
        $trace = "<table><thead><tr><th></th><th>Function</th><th>File</th></tr><tr></tr></thead><tbody>{$trace}</tbody></table>";


        return "<br/><div class='exception'><h1>An error occurred</h1><p>{$e->getMessage()} (". get_class($e) .")</p> <input type='checkbox' class='ez-hide' id='trace'> <div>{$trace}</div> <a><label for='trace'>show/hide technical information</label></a></div>";
    }

    static function getHostByUrl( $Address ) {
        $parseUrl = parse_url(trim($Address));
        return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
    }
}
