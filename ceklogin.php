<?php

//$pass=($_POST[password]);
include_once 'app/config.php';
date_default_timezone_set("Asia/SIngapore");
$tanggal=date('Y-m-d H:i:s');
$pass=md5($_POST['password']);
//$con=mysqli_connect("localhost","root","","krisna");
$login=mysqli_query($conn,"SELECT * FROM user WHERE nip='$_POST[nip]' AND password='$pass' AND status='Aktif'");
$ketemu=mysqli_num_rows($login);
$r=mysqli_fetch_array($login);

// Apabila username dan password ditemukan
if ($ketemu > 0){
	session_start();
  
	$_SESSION['nip']=$r['nip'];
	$_SESSION['passuser']=$r['password'];
	
	//INPUT LOG LOGIN
	$sql = "INSERT INTO log	(tanggal,
							 nip,
							 aktifitas)
					VALUES	('$tanggal',
							 '$_SESSION[nip]',
							 'Berhasil Login')";
	
	if (mysqli_query($conn, $sql)) {
		header('location:view.php?module=home');
		}
}
else{
  echo"<div align='center'><br><br><br><h2>Username/Password Salah, atau Status User Anda Tidak Aktif <br> Silakan Coba Lagi</h2><br>
		<input type=button value=Kembali style='height:35px; width:100px' onclick=self.history.back()>";
}
?>
