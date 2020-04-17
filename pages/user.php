<?php
if(!defined("INDEX")) header('location: ../index.php');

$show = isset($_GET['show']) ? $_GET['show'] : "";
$link = "?module=user";
echo '<div class="block-header">';
echo '	<h2>';
if(userroles('role') == 'admin') {
	echo '		<a href="'.$link.'&show=form" class="btn btn-primary btn-sm pull-right top-button">';
	echo '			<i class="glyphicon glyphicon-plus-sign"></i> Tambah';
	echo '		</a>';
}
echo '		DATA PENGGUNA';
echo '		<small>Periode '.tgl_indonesia($date).'</small>';
echo '	</h2>';
echo '</div>';

switch($show){
	//Menampilkan data
	default:
		if(userroles('role')=="admin"){
			display_message();

			buka_section_body('Tabel Pengguna');
			buka_tabel(array("Nama Lengkap", "Username", "Password", "Level"));
			$no = 1;
			$query = $mysqli->query("SELECT pegawai.nama, lite_roles.role, pegawai.nik, AES_DECRYPT(user.password,'windi') as password FROM pegawai, lite_roles, user WHERE pegawai.nik = lite_roles.username AND lite_roles.username = AES_DECRYPT(user.id_user,'nur') ORDER BY pegawai.nik");
			if (!empty($query) && $query->num_rows > 0) {
				while($data = $query->fetch_array()){
					if($data['role']=="admin") isi_tabel($no, array($data['nama'], $data['nik'], $data['password'], $data['role']), $link, $data['nik'], true, false);
					else isi_tabel($no, array($data['nama'], $data['nik'], $data['password'], $data['role']), $link, $data['nik'], true, true);
					$no++;
				}
			}
			tutup_tabel();
			tutup_section_body();
		}else{
			$id_user = $_SESSION['username'];
			$query 	= $mysqli->query("SELECT pegawai.nama, lite_roles.role, pegawai.nik, AES_DECRYPT(user.password,'windi') as password FROM pegawai, lite_roles, user WHERE pegawai.nik = lite_roles.username AND lite_roles.username = AES_DECRYPT(user.id_user,'nur') AND lite_roles.username='$id_user'");
			$data	= $query->fetch_array();
			$aksi 	= "Edit";

			//echo'<h3 class="page-header"><b>'.$aksi.' User</b> </h3>';

			buka_section_body($aksi.' Modul');
			buka_form($link, $id_user, strtolower($aksi));
				buat_textbox("NIP", "nik", $data['nik']);
				buat_textbox("Nama Lengkap", "nama", $data['nama']);
			tutup_form($link);
			tutup_section_body();
		}
	break;

	//Menampilkan form input dan edit data
	case "form":
		if(isset($_GET['id'])){
			$query 	= $mysqli->query("SELECT * FROM lite_roles WHERE username='$_GET[id]'");
			$data	= $query->fetch_array();
			$aksi 	= "Edit";
		}else{
			$data = array(
				"username"=>"",
				"role"=>"",
				"cap"=>"",
				"module"=>""
			);
			$aksi 	= "Tambah";
		}

		buka_section_body($aksi." Pengguna");
		buka_form($link, $data['username'], strtolower($aksi));
			//buat_textbox("Username", "username", $data['username']);
			//buat_textbox("Roles", "role", $data['role']);
			?>
			<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="username">Username</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
									<div class="form-line">
										<select name="username" id="username" class="form-control show-tick"  data-live-search="true" data-size="4">
											 <?php
											 $query = $mysqli->query("(SELECT kd_dokter AS username, nm_dokter AS nama FROM dokter) UNION (SELECT nik AS username, nama AS nama FROM pegawai) UNION (SELECT nip AS username, nama AS nama FROM petugas)");
											 while($user = $query->fetch_array()){
													 $select = $user['username']==$data['username'] ? 'selected' : '';
													 echo'<option value='.$user['username'].' '.$select.'>'.$user['nama'].'</option>';
											 }
											 ?>
										</select>
									</div>
							</div>
					</div>
			</div>
			<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
							<label for="role">Roles</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
									<div class="form-line">
										<select name="role" id="roles" class="form-control show-tick" data-width="100%">
											 <?php
											 $role = array("admin","manajemen","medis","paramedis","apoteker","rekammedis");
											 foreach($role as $value) {
												 $select = $value==$data['role'] ? 'selected' : '';
												 echo '<option value="'.$value.'" '.$select.'>'.$value.'</option>';
											 }
											 ?>
										</select>
									</div>
							</div>
					</div>
			</div>
			<?php
			buat_textbox("Cap", "cap", $data['cap']);
			buat_textbox("Module", "module", $data['module']);
		tutup_form($link);
		tutup_section_body();

	break;

	//Menyisipkan atau mengedit data di database
	case "action":
		if($_POST['aksi'] == "tambah"){
			$mysqli->query("INSERT INTO lite_roles SET
				username 	= '$_POST[username]',
				role 	= '$_POST[role]',
				cap 	= '$_POST[cap]',
				module 	= '$_POST[module]'
			");
			set_message('Data pengguna berhasil ditambah.');
		}elseif($_POST['aksi'] == "edit"){
			$mysqli->query("UPDATE lite_roles SET
				role 	= '$_POST[role]',
				cap 	= '$_POST[cap]',
				module 	= '$_POST[module]'
			WHERE username='$_POST[id]'");
			set_message('Data pengguna berhasil diubah.');
		}
		header('location:'.$link);
	break;

	//Menghapus data di database
	case "delete":
		$mysqli->query("DELETE FROM lite_roles WHERE username='$_GET[id]'");
		set_message('Data pengguna berhasil dihapus.');
		header('location:'.$link);
	break;
}

function addCSS() {
	echo '<link href="assets/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />';
	dataTablesCSS();
}

function addJS() {
	echo '<script src="assets/plugins/bootstrap-select/js/bootstrap-select.js"></script>';
	dataTablesJS();
	dataTablesDisplay();
}

?>