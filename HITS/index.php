<?php

$matrix=array(
    array(0,1,0,0,0,0,0),
    array(0,0,1,1,1,0,0),
    array(0,0,0,0,1,0,0),
    array(0,0,0,0,1,0,0),
    array(0,0,0,0,0,1,0),
    array(0,0,0,0,0,0,0),
    array(0,0,0,1,0,0,0)
);
$test_m=array(
    array(0,1,1,1,0),
    array(1,0,0,1,0),
    array(0,0,0,0,1),
    array(0,1,1,0,0),
    array(0,0,0,0,0)
);
$test_v=array(1,1,1,1,1);
$test_h=array(1,1,1,1,1);
function matrix_vector_multiplication($vector,$matrix){
    $ans=array();
    $temp=0;

    $row_length = count($matrix);
    if($row_length!=count($vector)){throw new Exception('Number of columns and elements in vector do not match');}
    for ($k=0;$k<count($matrix);$k++){
        for ($l=0;$l<$row_length;$l++){
            $temp=$temp + $matrix[$k][$l]*$vector[$l];
        }

        array_push( $ans,$temp);
        $temp=0;
    }

    return $ans;
}

function matrixtransp($m){
    $r=count($m);
    $c=count($m[0]);
    $mt=array();
    for($i=0;$i< $r;$i++){
        for($j=0;$j<$c;$j++){
            $mt[$j][$i]=$m[$i][$j];
        }
    }
    return($mt);
}
function hits($matrix){
    $h=array(1,1,1,1,1,1,1);
    $a=array(1,1,1,1,1,1,1);

    $t_matrix=matrixtransp($matrix);
    for ($l=0;$l<6;$l++){
        $a=matrix_vector_multiplication($h,$t_matrix);
        $h=matrix_vector_multiplication($a,$matrix);
        $max=max($a);
        for ($t=0;$t<count($a);$t++)
        {
            $a[$t]=$a[$t]/$max;
        }
        for ($e=0;$e<count($a);$e++)
        {
            $h[$e]=$h[$e]/$max;
        }
    }
    echo 'Authority';
    var_dump($a);
    echo '<hr>';
    echo 'Hubbness';
    var_dump($h);
}

hits($matrix);