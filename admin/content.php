<?php

include_once '../app/config.php';
//$conn=mysqli_connect("localhost","root","","pandawa");
// Check connection
/*if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}*/

//HALAMAN HOME (DASHBOARD)
if ($_GET['module']=='home'){
	echo "<h1>DASHBOARD</h1>
			<h2>JUMLAH PASPOR YANG DIARSIPKAN 7 HARI TERAKHIR</h2>";
	
	
	echo"<iframe src='app/chart1.php' height='400px' width='700px'></iframe>
			<br><br><hr color=#265180><br>
	 
			<h2>JUMLAH PASPOR YANG DISERAHKAN DALAM 7 HARI TERAKHIR</h2>
			<iframe src='app/chart2.php' height='400px' width='700px'></iframe>";
	 
	}


// HALAMAN MANAJEMEN USER
elseif ($_GET['module']=='manajemenuser'){
	echo "<h3>MANAJEMEN USER</h3>
			<div align='center'><a href=?module=tambahuser><button>TAMBAH USER</button></a></div>";
	
	echo"<table border='1'>
    <thead bgcolor='yellow'>
      <tr align='center'>
	    <th width='50'>NO</span></th>
        <th width='200'>NIP</span></th>
		<th width='300'>NAMA</span></th>
		<th width='300'>SEKSI</span></th>
		<th width='100'>AKTIF</span></th>
      </tr>
    </thead>
    <tbody>";
	$halaman = 10;
	$page = isset($_GET["halaman"])?(int)$_GET["halaman"] : 1;
	$mulai = ($page>1)?($page * $halaman) - $halaman : 0;
	$sebelum        = $page - 1;
	$setelah        = $page + 1;
	$result = mysqli_query($conn,"SELECT * FROM user");
	$total = mysqli_num_rows($result);
	$pages = ceil($total/$halaman);
	$tampilMas = mysqli_query($conn,"SELECT * FROM user
									order by nip LIMIT $mulai, $halaman");
	$no = $mulai+1;
                while($mas = mysqli_fetch_array($tampilMas)){
	echo "<tr align='center'>
			  <td>$no</td>
			  <td><a href=?module=detiluser&nip=$mas[nip]>$mas[nip]</a></td>
			  <td>$mas[nama]</td>
			  <td>$mas[seksi]</td>
			  <td>$mas[status]</td>
		  </tr>";

		$no++;
				}
	echo"</table><br>";
	
	//paging
	 echo"<div style='font-weight:bold;' align='center'>";
		$previous = $page - 1;
		$next = $page + 1;	
           /* for ($i=1; $i<=$pages ; $i++){
            echo"<a href=?module=lihatdatapaspor&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
            }*/
		if($page-1 < 5){
			for ($i=1; $i<=$page-1 ; $i++){
				echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
			}
		} else {
			echo"<a href=?module=manajemenuser&halaman=1; style='text-decoration:none'>   << First |</a>";
			if($pages-$page < 6){
				for ($i=$pages-9; $i<=$page-1 ; $i++){
					echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
					}
			}else {
				for ($i=$page-4; $i<=$page-1 ; $i++){
					echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			}
		}
		
		echo"<b> $i</b>";
		
		if($pages-$page < 6){
			for ($i=$page+1; $i<=$pages ; $i++){
				echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
			}
		} else {
			if($page-1 < 5){
				for ($i=$page+1; $i<=10 ; $i++){
					echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			} else {
				for ($i=$page+1; $i<=$page+5 ; $i++){
					echo"<a href=?module=manajemenuser&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			}
			echo"<a href=?module=manajemenuser&halaman=$pages; style='text-decoration:none'>   | Last >></a>";
		}
    echo"<br></div><br>";
}


// HALAMAN TAMBAH USER
elseif ($_GET['module']=='tambahuser'){
	echo "<h3>TAMBAH USER</h3>
			 <form method=POST enctype='multipart/form-data' action=input.php?module=user&act=input>
				<p>
				  <table>
					<tr><td width='100'><b>NIP</b></td><td>:</td><td><input type='text' name='nip' size='60' required></td></tr>
					<tr><td><b>NAMA</b></td><td>:</td><td><input type='text' name='nama' size='60' required></td></tr>
					<tr><td><b>SEKSI</b></td><td>:</td><td><select name=seksi id=seksi>
						<option value=LANTASKIM>LANTASKIM</option>
						<option value=INTALTUSKIM disabled>INTALTUSKIM</option>
						<option value=INTELDAKIM disabled>INTELDAKIM</option>
						<option value=TIKIM disabled>TIKIM</option>
					</select></td></tr>
					<tr><td><b>PASSWORD</b></td><td>:</td><td><input type='password' name='password' size='60' required></td></tr>
					<tr><td colspan=3 align='right'><input type='submit' name='save' value='Simpan' style='height:50px; width:100px'></td></tr>
				  </table>
				</p>
			  </form>";
}

//HALAMAN EDIT USER
elseif ($_GET['module']=='detiluser'){
	$edit   = mysqli_query($conn,"SELECT * FROM user WHERE nip = '$_GET[nip]'");
	$e    = mysqli_fetch_array($edit);
	echo"<h3>DETIL USER</h3>
			<form method=POST enctype='multipart/form-data' action=input.php?module=user&act=edit>
			<input type='hidden' name='nip' value='$e[nip]'>
				<p>
				  <table>
					<tr><td width='100'><b>NIP</b></td><td>:</td><td><input type='text' size='60' value='$e[nip]' disabled></td></tr>
					<tr><td><b>NAMA</b></td><td>:</td><td><input type='text' name='nama' size='60' value='$e[nama]' required></td></tr>
					<tr><td><b>SEKSI</b></td><td>:</td><td><select name=seksi id=seksi>
							<option value='LANTASKIM'";if($e['seksi']=='LANTASKIM'){echo"selected='selected'";}echo">LANTASKIM</option>
							<option value='INTALTUSKIM'";if($e['seksi']=='INTALTUSKIM'){echo"selected='selected'";}echo" disabled>INTALTUSKIM</option>
							<option value='INTELDAKIM'";if($e['seksi']=='INTELDAKIM'){echo"selected='selected'";}echo" disabled>INTELDAKIM</option>
							<option value='TIKIM'";if($e['seksi']=='TIKIM'){echo"selected='selected'";}echo" disabled>TIKIM</option>
						</select></td></tr>
					<tr><td><b>PASSWORD*</b></td><td>:</td><td><input type='password' name='password' size='60'></td></tr>
					<tr><td colspan=3 align='right'><i>*)kosongkan password jika tidak dilakukan penggantian</i></td></tr>
					<tr><td><b>STATUS</b></td><td>:</td><td>
							<select name='status'>
								<option value='Aktif' ";if($e['status']=='Aktif'){echo"selected='selected'";}echo">Aktif</option>
								<option value='Tidak Aktif' ";if($e['status']=='Tidak Aktif'){echo"selected='selected'";}echo">Tidak Aktif</option>
							 </select>
						</td></tr>
					<tr><td colspan=3 align='right'><input type='submit' name='save' value='Simpan' style='height:50px; width:100px'></td></tr>
				  </table>
				</p>
			  </form>";
}


// HALAMAN MANAJEMEN RAK
elseif ($_GET['module']=='manajemenrak'){
	echo "<h3>MANAJEMEN RAK</h3>
			<div align='center'><a href=?module=tambahrak><button>TAMBAH RAK</button></a></div>";
	
	echo"<table border='1'>
    <thead bgcolor='yellow'>
      <tr align='center'>
	    <th width='50'>NO</span></th>
        <th width='200'>NOMOR RAK</span></th>
		<th width='200'>LOKASI RAK</span></th>
		<th width='300'>KUOTA</span></th>
		<th width='300'>DOKIM TERSIMPAN</span></th>
		<th width='50'>AKSI</span></th>
      </tr>
    </thead>
    <tbody>";
	$halaman = 10;
	$page = isset($_GET["halaman"])?(int)$_GET["halaman"] : 1;
	$mulai = ($page>1)?($page * $halaman) - $halaman : 0;
	$sebelum        = $page - 1;
	$setelah        = $page + 1;
	$result = mysqli_query($conn,"SELECT * FROM rak");
	$total = mysqli_num_rows($result);
	$pages = ceil($total/$halaman);
	$tampilMas = mysqli_query($conn,"SELECT * FROM rak
									order by nomor_rak LIMIT $mulai, $halaman");
	$no = $mulai+1;
                while($mas = mysqli_fetch_array($tampilMas)){
	echo "<tr align='center'>
			  <td>$no</td>
			  <td><a href=?module=detilrak&nomor_rak=$mas[nomor_rak]>$mas[nomor_rak]</a></td>
			  <td>$mas[lokasi]</td>
			  <td>$mas[kuota]</td>";
				$terpakai = mysqli_query($conn, "SELECT COUNT(nomor_rak) as terpakai FROM arsip_paspor WHERE nomor_rak='$mas[nomor_rak]' AND status='Aktif'");
				$t = mysqli_fetch_array($terpakai);
				echo"<td>$t[terpakai]</td>";
		
		if($t['terpakai']>0){
			echo"<td><a href=input.php?module=rak&act=hapus&nomor_rak=$mas[nomor_rak]><button class=buttonhapuskecil disabled>Hapus</button></a></td>
		  </tr>";
		}else{
			echo"<td><a href=input.php?module=rak&act=hapus&nomor_rak=$mas[nomor_rak]><button class=buttonhapuskecil>Hapus</button></a></td>
		  </tr>";
		}
		$no++;
				}
	echo"</table><br>";
	
	//paging
	 echo"<div style='font-weight:bold;' align='center'>";
		$previous = $page - 1;
		$next = $page + 1;	
           /* for ($i=1; $i<=$pages ; $i++){
            echo"<a href=?module=lihatdatapaspor&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
            }*/
		if($page-1 < 5){
			for ($i=1; $i<=$page-1 ; $i++){
				echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
			}
		} else {
			echo"<a href=?module=manajemenrak&halaman=1; style='text-decoration:none'>   << First |</a>";
			if($pages-$page < 6){
				for ($i=$pages-9; $i<=$page-1 ; $i++){
					echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
					}
			}else {
				for ($i=$page-4; $i<=$page-1 ; $i++){
					echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			}
		}
		
		echo"<b> $i</b>";
		
		if($pages-$page < 6){
			for ($i=$page+1; $i<=$pages ; $i++){
				echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
			}
		} else {
			if($page-1 < 5){
				for ($i=$page+1; $i<=10 ; $i++){
					echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			} else {
				for ($i=$page+1; $i<=$page+5 ; $i++){
					echo"<a href=?module=manajemenrak&halaman=$i; style='text-decoration:none'>   <u>$i</u></a>";
				}
			}
			echo"<a href=?module=manajemenrak&halaman=$pages; style='text-decoration:none'>   | Last >></a>";
		}
    echo"<br></div><br>";
}


// HALAMAN TAMBAH RAK
elseif ($_GET['module']=='tambahrak'){
	echo "<h3>TAMBAH RAK</h3>
			 <form method=POST enctype='multipart/form-data' action=input.php?module=rak&act=input>
				<p>
				  <table>
					<tr><td width='100'><b>NOMOR RAK</b></td><td>:</td><td><input type='text' name='nomor_rak' size='60' required></td></tr>
					<tr><td><b>LOKASI</b></td><td>:</td><td><select name=lokasi id=lokasi>
																<option value=LANTASKIM>LANTASKIM</option>
																<option value=INTALTUSKIM disabled>INTALTUSKIM</option>
																<option value=INTELDAKIM disabled>INTELDAKIM</option>
																<option value=TIKIM disabled>TIKIM</option>
															</select></td></tr>
					<tr><td><b>KUOTA</b></td><td>:</td><td><input type='text' name='kuota' size='60' required></td></tr>
					<tr><td colspan=3 align='right'><input type='submit' name='save' value='Simpan' style='height:50px; width:100px'></td></tr>
				  </table>
				</p>
			  </form>";
}

//EDIT RAK
elseif ($_GET['module']=='detilrak'){
	$edit   = mysqli_query($conn,"SELECT * FROM rak WHERE nomor_rak = '$_GET[nomor_rak]'");
	$e    = mysqli_fetch_array($edit);
	echo"<h3>DETIL RAK</h3>
			<form method=POST enctype='multipart/form-data' action=input.php?module=rak&act=edit>
			<input type='hidden' name='nomor_rak' value='$e[nomor_rak]'>
				<p>
				  <table>
					<tr><td width='100'><b>NOMOR RAK</b></td><td>:</td><td><input type='text' size='60' value='$e[nomor_rak]' disabled></td></tr>
					<tr><td><b>LOKASI</b></td><td>:</td><td><select name=lokasi id=lokasi>
							<option value='LANTASKIM'";if($e['lokasi']=='LANTASKIM'){echo"selected='selected'";}echo">LANTASKIM</option>
							<option value='INTALTUSKIM'";if($e['lokasi']=='INTALTUSKIM'){echo"selected='selected'";}echo" disabled>INTALTUSKIM</option>
							<option value='INTELDAKIM'";if($e['lokasi']=='INTELDAKIM'){echo"selected='selected'";}echo" disabled>INTELDAKIM</option>
							<option value='TIKIM'";if($e['lokasi']=='TIKIM'){echo"selected='selected'";}echo" disabled>TIKIM</option>
						</select></td></tr>
					<tr><td><b>KUOTA</b></td><td>:</td><td><input type='text' name='kuota' size='60' value='$e[kuota]' required></td></tr>
					<tr><td colspan=3 align='right'><input type='submit' name='save' value='Simpan' style='height:50px; width:100px'></td></tr>
				  </table>
				</p>
			  </form>";
}


//HALAMAN EDIT PASSWORD ADMIN
elseif ($_GET['module']=='passwordadmin'){
	$edit   = mysqli_query($conn,"SELECT * FROM admin WHERE username = '$_SESSION[username]'");
	$e    = mysqli_fetch_array($edit);
	echo"<h3>GANTI PASSWORD ADMINISTRATOR</h3>
			<form method=POST enctype='multipart/form-data' action=input.php?module=admin&act=edit>
			<input type='hidden' name='username' value='$e[username]'>
				<p>
				  <table>
					<tr><td width='100'><b>USERNAME</b></td><td>:</td><td><input type='text' size='60' value='$e[username]' disabled></td></tr>
					<tr><td><b>PASSWORD*</b></td><td>:</td><td><input type='password' name='password' size='60' required></td></tr>
					<tr><td colspan=3 align='right'><input type='submit' name='save' value='Simpan' style='height:50px; width:100px'></td></tr>
				  </table>
				</p>
			  </form>";
}

//HALAMAN LOG ACTIVITY
elseif ($_GET['module']=='log'){
	echo"<h3>LOG ACTIVITY</h3>
	<form method=POST action='?module=detillog'>
	<div align='center'><br>
	<table>
	  <tr><td>Pilih User &nbsp;</td><td>: &nbsp;</td><td><select name='user' id='user'>
										   <option value='0'>Semua User</option>";
										   $query = mysqli_query($conn,"SELECT * FROM user");
										   while($u=mysqli_fetch_array($query)){
											   echo"<option value='$u[nip]'>$u[nip] - $u[nama]</option>";
										   }
										   echo"</select></td></tr>
	  <tr><td>Tanggal &nbsp;</td><td>: &nbsp;</td><td><input type='datetime-local' name='tanggal_mulai' required> s/d <input type='datetime-local' name='tanggal_akhir' required></td></tr>
	  <tr align='right'><td colspan='3'><input type=submit value=Cari></td></tr>
	</table>
	</form></div>";
}

//HALAMAN LOG ACTIVITY
elseif ($_GET['module']=='detillog'){
	echo"<h3>LOG ACTIVITY</h3>";
	
	//JIKA MELIHAT LOG SELURUH USER
	if($_POST['user']=='0'){
		echo"<div align='left'>
			 NIP : Seluruh User<br>
			 Tanggal : $_POST[tanggal_mulai] s/d $_POST[tanggal_akhir]
			 </div>
			 
			 <table border='1'>
			  <tr><th>No</th><th>Tanggal</th><th>NIP</th><th>Nama</th><th>Aktifitas</th></tr>";
		
		$query=mysqli_query($conn,"SELECT * FROM log JOIN user ON log.nip=user.nip WHERE log.tanggal>='$_POST[tanggal_mulai]' AND log.tanggal<='$_POST[tanggal_akhir]' ORDER BY log.tanggal");
		$no=1;
		while($row=mysqli_fetch_array($query)){
			echo"<tr><td align='center'>$no</td>
				     <td align='center'>$row[tanggal]</td>
					 <td align='center'>$row[nip]</td>
					 <td align='center'>$row[nama]</td>
					 <td>$row[aktifitas]</td></tr>";
			$no++;
		}
		echo"</table>";
	
	//JIKA MELIHAT LOG USER TERPILIH
	}else{
		$user = mysqli_query($conn,"SELECT * FROM user WHERE nip = '$_POST[user]'");
		$u = mysqli_fetch_array($user);
		echo"<div align='left'>
			 NIP : $_POST[user] - $u[nama]<br>
			 Tanggal : $_POST[tanggal_mulai] s/d $_POST[tanggal_akhir]
			 </div>
			 
			 <table border='1'>
			  <tr><th>No</th><th>Tanggal</th><th>NIP</th><th>Nama</th><th>Aktifitas</th></tr>";
		
		$query=mysqli_query($conn,"SELECT * FROM log JOIN user ON log.nip=user.nip WHERE log.nip='$_POST[user]' AND log.tanggal>='$_POST[tanggal_mulai]' AND log.tanggal<='$_POST[tanggal_akhir]' ORDER BY log.tanggal");
		$no=1;
		while($row=mysqli_fetch_array($query)){
			echo"<tr><td align='center'>$no</td>
				     <td align='center'>$row[tanggal]</td>
					 <td align='center'>$row[nip]</td>
					 <td align='center'>$row[nama]</td>
					 <td>$row[aktifitas]</td></tr>";
			$no++;
		}
		echo"</table>";
	}
}

// HALAMAN MANAJEMEN PESAN WA
elseif ($_GET['module']=='manajemenwa'){
	$pesan   = mysqli_query($conn,"SELECT * FROM wa WHERE id_wa='1'");
	$wa    = mysqli_fetch_array($pesan);
	echo"<h3>MANAJEMEN PESAN</h3>
			<form method=POST enctype='multipart/form-data' action=input.php?module=pesan&act=edit>
			<input type='hidden' name='id_wa' value='$wa[id_wa]'>	
				<p>
				  <table>
					<tr><td width='100'><b>TOKEN</b></td><td>:</td><td><input type='text' size='60' name='token' value='$wa[token]' required></td></tr>
					<tr><td valign='top'><b>ISI PESAN REGULER</b></td><td valign='top'>:</td><td><textarea name='pesan' rows='4' cols='50' required>$wa[pesan]</textarea><br></td></tr>
					<tr><td valign='top'><b>ISI PESAN PERCEPATAN</b></td><td valign='top'>:</td><td><textarea name='pesan_percepatan' rows='4' cols='50' required>$wa[pesan_percepatan]</textarea><br></td></tr>
					<tr><td valign='top'><b>ISI PESAN PERINGATAN</b></td><td valign='top'>:</td><td><textarea name='pesan_peringatan' rows='4' cols='50' required>$wa[pesan_peringatan]</textarea></td></tr>
					<tr><td valign='top'><b>PANDUAN KODE PESAN</b></td><td valign='top'>:</td><td>
													<ul>
													  <li>{name} : Nama Pemohon</li>
													  <li>{var1} : Nomor Permohonan</li>
													  <li>{var2} : Catatan Petugas</li>
													  <li>{var3} : Nomor Paspor</li>
													  <li>{var4} : Tanggal Input ke Krisna</li>
													  <li>{var5} : Lama Paspor Belum Diambil (hari)</li>
													</ul></td></tr>
					<tr><td colspan=3 align='right'><br><input type='submit' name='save' value='Simpan' style='height:30px; width:70px'></td></tr>
				  </table>
				</p>
			  </form>";
}

//jika modul tidak ditemukan
else{
  echo "<p align=center><b>MODUL BELUM ADA</b></p>";
}
?>