<?php

//require 'conn.php';
$myv=array(
    array(0,1/5,1,1,1,1,1,1),
    array(1/3,0,0,0,0,0,0,0),
    array(1/3,0,0,0,0,0,0,0),
    array(1/3,0,0,0,0,0,0,0),
    array(0,1/5,0,0,0,0,0,0),
    array(0,1/5,0,0,0,0,0,0),
    array(0,1/5,0,0,0,0,0,0),
    array(0,1/5,0,0,0,0,0,0)
);

$tvect=array(1/8,1/8,1/8,1/8,1/8,1/8,1/8,1/8);

$var=0;
$d=0.85;
$ans=array();
$fpr_vect=array_fill(0,8,1);
$counter=array_fill(0,8,0);
for ($e=0;$e<count($counter);$e++){
    for ($n=0;$n<count($myv[0]);$n++){
        if($myv[$n][$e]!=0){
            $counter[$e]++;
        }
    }
}

for ($t=0;$t<50;$t++){

    for ($m=0;$m<8;$m++){
        for ($n=0;$n<count($myv[0]);$n++){
            if($myv[$n][$m]!=0&&$m!=$n){
                $var=$var+($fpr_vect[$n]/$counter[$n]);
            }
        }
        $fpr_vect[$m]=(1-$d)+($d*$var)/$counter[$m]/*array_sum($counter)*/;
        $var=0;
    }
}
var_dump($fpr_vect);


