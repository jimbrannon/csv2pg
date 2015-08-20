<?php
/*
 * constants
 */
define("METHOD","method");
define("FILENAME","filename");
define("DEBUGGING","debugging");
define("LOGGING","logging");
/*
 * argument defaults
 * much like pg2gviz
 * in other words, defaults get used unless over-ridden by command line args
 * the difference is they all get stuffed into an options array, makes for much cleaner code
 */
$options[FILENAME]="dirty_data_1.txt";
$options[METHOD]=1;
$options[DEBUGGING]=true;
$options[LOGGING]=true;
/*
 * the code 
 */
if (array_key_exists(LOGGING,$options)) {
	$logging = $options[LOGGING];
} else {
	$logging = false;
}
if (csv2pg($options)) {
	if ($logging) {
		echo "It worked.\n";
	}
} else {
	if ($logging) {
		echo "It failed.\n";
	}
}

function csv2pg($options=array()) {
	/*
	 * get command line args here, replacing default values
	 * much like pg2gviz
	 * in other words, defaults get used unless over-ridden by command line args
	 * the difference is they all get stuffed into an options array, makes for much cleaner code
	 */
	
	/*
	 * now start the file processing
	 */
	if (array_key_exists(LOGGING,$options)) {
		$logging = $options[LOGGING];
	} else {
		$logging = false;
	}
	if (array_key_exists(DEBUGGING,$options)) {
		$debugging = $options[DEBUGGING];
	} else {
		$debugging = false;
	}
	/*
	 * convert file into array of file records
	 */
	if (array_key_exists(FILENAME,$options)) {
		$file_name = $options[FILENAME];
	} else {
		if ($logging) {
			echo "Error: csv2pg: Missing file name.\n";
		}
		if ($debugging) {
			echo "Options array: csv2pg:\n";
			print_r($options);
		}
		return false;
	}
	if ($file_records = file($file_name)) {
		if ($debugging) {
			echo "File record array: csv2pg:\n";
			print_r($file_records);
		}
		/*
		 * convert array of file records into an array of file field arrays
		 */
		if ($file_fields = csv2array($file_records,$options)) {
			if ($debugging) {
				print_r($file_fields);
			}
			/*
			 * append field values to pg table
			 */
			return true;
		} else {
			if ($logging) {
				echo "Error: csv2pg: could not convert file record array into file field arrays\n";
			}
			return false;
		}
	} else {
		if ($logging) {
			echo "Error: csv2pg: could not read records from file: ".$file_name."\n";
		}
		return false;
	}
}

function csv2array($file_records,$options=array()) {
	if (array_key_exists(LOGGING,$options)) {
		$logging = $options[LOGGING];
	} else {
		$logging = false;
	}
	if (array_key_exists(DEBUGGING,$options)) {
		$debugging = $options[DEBUGGING];
	} else {
		$debugging = false;
	}
	if (array_key_exists(METHOD,$options)) {
		$method = $options[METHOD];
	} else {
		if ($logging) {
			echo "Error: csv2array: missing file processing method\n";
		}
		if ($debugging) {
			echo "Options array: csv2array:\n";
			print_r($options);
		}
		return false;
	}
	switch($method) {
		case 1:
			return array_map('str_getcsv', $file_records);
		case 0:
		default:
			if ($logging) {
				echo "Error: csv2array: invalid file processing method specified: ".$method."\n";
			}
			return false;
	}
}
