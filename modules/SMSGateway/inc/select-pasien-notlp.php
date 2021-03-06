<?php

/***
* e-Dokter from version 0.1 Beta
* Last modified: 02 Pebruari 2018
* Author : drg. Faisol Basoro
* Email : drg.faisol@basoro.org
*
* File : includes/select-obat.php
* Description : Get databarang data from json encode by select2
* Licence under GPL
***/

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
   header("HTTP/1.0 403 Forbidden");
   exit;
}

ob_start();
session_start();

include ('../../../config.php');
include ('../../../init.php');

$q = $_GET['q'];

$sql = query("SELECT no_tlp AS id, nm_pasien AS text, no_rkm_medis AS no_rkm_medis FROM pasien WHERE (no_tlp LIKE '%".$q."%' OR nm_pasien LIKE '%".$q."%' OR no_rkm_medis LIKE '%".$q."%')");
$json = [];

while($row = fetch_assoc($sql)){
     $json[] = ['id'=>$row['id'], 'text'=>$row['text'], 'no_rkm_medis'=>$row['no_rkm_medis']];
}
echo json_encode($json);


?>
