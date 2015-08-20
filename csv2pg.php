<?php

function csv2pg($file,$options=array()) {
	
}

function csv2array($file,$skipline=0,$method=0) {
	switch($method) {
		case 0:
		default:
			return array_map('str_getcsv', file('data.csv'));
	}
}
