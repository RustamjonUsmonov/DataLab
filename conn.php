<?php

$servername = "";
$username = "";
$password = "";
$dbname="";
// Create connection

$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully"."<hr>";


$arr = array(
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0),
    array(0, 0, 0, 0, 0, 0, 0, 0));

for ($m=0;$m<8;$m++){
    $sql="select id1,id2 from links where id1='".($m+1)."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){

            for($q=0;$q<8;$q++) {
                $sql2="select count(id1) as count from links where id1='".($m+1)."'";
                $result2 = $conn->query($sql2);
                $row2 = $result2->fetch_assoc();
                $id1 = $row['id1'];
                $id2 = $row['id2'];

                $count = $row2['count'];
                $arr[$id1-1][$id2-1] =1/$count;
            }
        }
    }else {
        echo "0 results";
    }

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
$kod=matrixtransp($arr);


