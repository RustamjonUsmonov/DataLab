<?php
/*
class Bloom
{

    public $set;

    public $hashes;

    public $error_chance;

    public $set_size;

    public $hash_count;

    public $entries_count;

    public $entries_max;

    public $counter;

    public $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $map = array(
        'entries_max' => array(
            'type' => 'integer',
            'min' => 0
        ),
        'error_chance' => array(
            'type' => 'double',
            'min' => 0,
            'max' => 1
        ),
        'set_size' => array(
            'type' => 'integer',
            'min' => 100
        ),
        'hash_count' => array(
            'type' => 'integer',
            'min' => 1
        ),
        'counter' => array(
            'type' => 'boolean'
        ),
        'hash' => array(
            'strtolower' => array(
                'type' => 'boolean'
            )
        )
    );


    public function __construct($setup = null)
    {

        $params = array(
            'entries_max' => 100,
            'error_chance' => 0.001,
            'counter' => false,
            'hash' => array(
                'strtolower' => true
            )
        );


        $params = Map::apply($this->map, $params, $setup);

        foreach($params as $key => $value)
            $this->$key = $value;


        if(!$this->set_size)
            $this->set_size = -round( ( $this->entries_max * log($this->error_chance) ) / pow(log(2), 2) );

        if(!$this->hash_count)
            $this->hash_count = round($this->set_size * log(2) / $this->entries_max);

        for($i = 0; $i < $this->hash_count; $i++)
            $this->hashes[] = new Hash($params['hash'], $this->hashes);

        $this->set = str_repeat('0', $this->set_size);

        return $this;
    }


    public function __sleep() {
        foreach($this as $key => $attr)
            $result[] = $key;
        if($this->entries_count == 0)
            unset($result['set']);
        return $result;
    }

    public function __wakeup() {
        if($this->entries_count == 0)
            $this->set = str_repeat('0', $this->set_size);
    }

    public function set($mixed) {

        if( is_array($mixed) )
            foreach($mixed as $arg)
                $this->set($arg);

        else
            for($i=0; $i < $this->hash_count; $i++) {
                if($this->counter === false)
                    $this->set[ $this->hashes[$i]->crc($mixed, $this->set_size) ] = 1;
                else
                    $this->counter( $this->hashes[$i]->crc($mixed, $this->set_size), 1 );

                $this->entries_count++;
            }

        return $this;
    }


    public function delete($mixed) {
        if($this->counter === false)
            return false;

        if( is_array($mixed) ) {
            foreach($mixed as $key => $arg)
                $result[$key] = $this->delete($arg);

            return $result;
        }

        else
            if($this->has($mixed)) {
                for($i=0; $i < $this->hash_count; $i++) {
                    $this->counter($this->hashes[$i]->crc($mixed, $this->set_size), -1);

                    $this->entries_count--;
                }
                return true;
            }
            else
                return false;
    }


    public function counter($position, $add = 0, $get = false) {

        if($get === true)
            return $this->set[$position];
        else {
            $in_a = strpos($this->alphabet, $this->set[$position]);
            $this->set[$position] = ($this->alphabet[$in_a + $add] != null) ? $this->alphabet[$in_a + $add] : $this->set[$position];
        }
    }


    public function has($mixed, $boolean = true) {

        if( is_array($mixed) ) {
            foreach($mixed as $key => $arg)
                $result[$key] = $this->has($arg, $boolean);

            return $result;
        }	else {
            $c = 0;
            for($i=0; $i < $this->hash_count; $i++) {
                if($this->counter === false)
                    $value = $this->set[ $this->hashes[$i]->crc($mixed, $this->set_size) ];
                else
                    $value = $this->counter($this->hashes[$i]->crc($mixed, $this->set_size), 0, true);

                if($boolean && !$value)
                    return false;
                elseif($boolean === false)
                    $c += ($value) ? 1 : 0;
            }

            return ($boolean === true) ? true : $c/$this->hash_count;
        }
    }
}


class Map {

    static public function apply($map, $initial, $setup) {
        self::circl($map, $setup);
        return array_merge($initial, (array) $setup);
    }

    static private function circl($map, $rabbit) {
        foreach($map as $k => $element) {
            if (is_array($element) && (!array_key_exists('type', $element) || !$element['type']) && array_key_exists($k, $rabbit)) {
                unset($rabbit[$k]);
                self::circl($element, $rabbit[$k]);
            } else if (array_key_exists($k, $rabbit)) {
                self::check($element, $rabbit[$k]);
                unset($rabbit[$k]);
            }
        }

        if($rabbit)
            throw new Exception('Unexpected array arguments. '.json_encode( $rabbit ));
    }


    static private function check($map, $rabbit) {

        if (array_key_exists('null', $map) && $map['null'] === false && !$rabbit)
            throw new Exception('Must be not NULL');


        if(!$rabbit)
            return true;

        if (array_key_exists('type', $map) && $map['type'] !== gettype($rabbit) && $map['type'])
            throw new Exception('Wrong type '.gettype($rabbit).'! Must be '.$map['type']);


        if (array_key_exists('min', $map) && $map['min'] > $rabbit && $map['min'] !== null)
            throw new Exception('Interval overflow by '.$rabbit.'! Must be '.$map['min']);


        if (array_key_exists('min', $map) && $map['min'] > $rabbit && $map['min'] !== null)
            throw new Exception('Interval overflow by '.$rabbit.'! Must be '.$map['max']);
    }
}


class Hash {

    public $seed;

    public $params;

    private $map = array(
        'strtolower' => array(
            'type' => 'boolean'
        )
    );

    public function __construct($setup = null, $hashes = null) {

        $params = array(
            'strtolower' => true
        );


        $params = Map::apply($this->map, $params, $setup);
        $this->params = $params;

        $seeds = array();
        if($hashes)
            foreach($hashes as $hash)
                $seeds = array_merge( (array) $seeds, (array) $hash->seed );
        do {
            $hash = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        } while( in_array($hash, $seeds) );
        $this->seed[] = $hash;
    }

    public function crc($string, $size) {
        $string = strval($string);

        if($this->params['strtolower'] === true)
            $string = mb_strtolower($string, 'UTF-8');

        return abs( crc32( md5($this->seed[0] . $string) ) ) % $size;
    }
}



$parameters = array(
    'entries_max' => 2 //создаем Объект для выборки из 2х элементов, с дефолтной вероятностью ошибки 0.1%
);
$bloom = new Bloom($parameters);

//добавляем элемент, можно добавить массив элементов
$bloom->set('создаем Объект для выборки из 2х элементов, с дефолтной вероятностью ошибки');
//проверяем наличие элемента
echo $bloom->has('dfdg'); //true

//удаление объекта, только если Bloom был инициирован с параметром counter
$bloom->delete('dfdg');*/
$txt='some string';

function adler_hash($txt)
{
    $txt = iconv('UTF-8', 'UTF-16LE', $txt);
    $adler = bin2hex(mhash(MHASH_ADLER32, $txt));

    return strtoupper($adler);
}
function crc_hash($txt)
{
    $txt = iconv('UTF-8', 'UTF-16LE', $txt);
    $crc = bin2hex(mhash(MHASH_CRC32, $txt));

    return strtoupper($crc);
}
echo adler_hash('ClearTextPasswd');
echo "\n";
echo crc_hash('ClearTextPasswd');
echo "\n";
echo '<br/>';
$value = unpack('H*', adler_hash('return'));
echo base_convert(($value[1]*2)+2%32, 16, 2);echo "<br/>";
echo '<br/>';
$vvalue = unpack('H*', crc_hash('return'));
echo base_convert(($vvalue[1]*2)+2%32, 16, 2);echo "<br/>";
