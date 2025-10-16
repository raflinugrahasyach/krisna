<?php
// =========$conn (string koneksi ke database lokal)=============
$servername='localhost';
$username='root';
$password='';
$dbname = "krisna_new";
$conn=mysqli_connect($servername,$username,$password,"$dbname");
if(!$conn){
   die('Could not Connect My Sql:' .mysql_error());
}


// ========$conn_spri (string koneksi ke spri)================
$servername='10.5.16.17';
$username='balikpapanView';
$password='V!3wbalikpapan';
$dbname = "spri_local";
$conn_spri=mysqli_connect($servername,$username,$password,"$dbname",3306);

if(!$conn_spri){
   die('Could not Connect My Sql:' .mysql_error());
}

// ========$conn_arsip (string koneksi ke arsip)================
$servername='localhost';
$username='root';
$password='';
$dbname = "arsip";
$conn_arsip=mysqli_connect($servername,$username,$password,"$dbname");
if(!$conn_arsip){
   die('Could not Connect My Sql:' .mysql_error());
}

?>
