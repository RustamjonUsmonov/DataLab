<?php


$matrix_a=array(
    array(0,1/2,1,0),
    array(1/3,0,0,1/2),
    array(1/3,0,0,1/2),
    array(1/3,1/2,0,0)
);
 $vect=array(1/4,1/4,1/4,1/4);

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

function matrix_multiplication($m1,$m2){
    $r=count($m1);
    $c=count($m2[0]);
    $p=count($m2);
    if(count($m1[0])!=$p){throw new Exception('Incompatible matrixes');}
    $m3=array();
    for ($i=0;$i< $r;$i++){
        for($j=0;$j<$c;$j++){
            $m3[$i][$j]=0;
            for($k=0;$k<$p;$k++){
                $m3[$i][$j]+=$m1[$i][$k]*$m2[$k][$j];
            }
        }
    }

    return($m3);
}
function matrix_vector_multiplication($vector,$matrix){
    $ans=array();
    $temp=0;

    $row_length = count($matrix[0]);
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

function comb($m,$times,$vec){
    $t=$m;
    $row_length = count($m);
    $value=array();
    for ($g=0;$g<$times-1;$g++){
        $t=matrix_multiplication($t,$m);
    }
    $value=$t;
    return matrix_vector_multiplication($vec,$value);
}
$com=comb($myv,50,$tvect);

for ($i=0;$i<count($com);$i++) {
    echo $com[$i]." " ;
    echo "<br/>";
}//final answer representation
//$res=matrix_multiplication($matrix_a,$matrix_a);
/*for ($i=0;$i<count($res[0]);$i++) {

    for ($j=0;$j<count($res[0]);$j++) {
        echo $res[$i][$j]." " ;
    }
    echo "<br/>";
}*///representation of matrix multiplication answer
