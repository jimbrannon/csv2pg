<?php
$file_name="dirty_data_1.txt";
print_r(csv2pg($file_name));

function csv2pg($file_name,$options=array()) {
	return csv2array(file($file_name),$options);
}

function csv2array($file_records,$options=array()) {
	$method=0;
	switch($method) {
		case 0:
		default:
			return array_map('str_getcsv', $file_records);
	}
}
