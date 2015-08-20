<?php
/*
 * our awesome php utils
 */
include "misc_io.php";

/*
 * constants
 */
define("METHOD","method");
define("FILENAME","filename");
define("DEBUGGING","debugging");
define("LOGGING","logging");
define("SKIPLINES","skiplines");
define("FIELDCOUNT","fieldcount");
define("LINENUMBERS","linenumbers");
define("PGUSER","pguser");
define("PGPASSWORD","pgpassword");
define("PGTABLE","pgtable");
/*
 * argument defaults
 * much like pg2gviz
 * in other words, defaults get used unless over-ridden by command line args
 * the difference is they all get stuffed into an options array, makes for much cleaner code
 */
$options[FILENAME]="dirty_data_2.txt";
$options[METHOD]=1;
$options[DEBUGGING]=true;
$options[LOGGING]=true;
$options[SKIPLINES]=0;
$options[FIELDCOUNT]=0;
$options[LINENUMBERS]=false;
$options[PGUSER]="pguser";
$options[PGPASSWORD]="pgpassword";
$options[PGTABLE]="pgtable";
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

/*
 * the functions that are the working componenets of the engine for this simple DMI
 */
function csv2pg($options=array()) {
	/*
	 * get command line args here, replacing default values
	 * much like pg2gviz
	 * in other words, defaults get used unless over-ridden by command line args
	 * the difference is they all get stuffed into an options array, makes for much cleaner code
	 */
	/*
	 * get the debugging arg
	 */
	if (array_key_exists(DEBUGGING,$options)) {
		$debugging = $options[DEBUGGING];
	} else {
		$debugging = false;
	}
	if ($debugging) echo "debugging default: $debugging \n";
	$debugging_arg = getargs ("debug",$debugging);
	if ($debugging) echo "debugging_arg: $debugging_arg \n";
	if (strlen(trim($debugging_arg))) {
		$debugging = strtobool($debugging_arg);
		$options[DEBUGGING] = $debugging;
	}
	if ($debugging) echo "debugging final: $debugging \n";
	/*
	 * get the logging arg
	 */
	if (array_key_exists(LOGGING,$options)) {
		$logging = $options[LOGGING];
	} else {
		$logging = false;
	}
	if ($debugging) echo "logging default: $logging \n";
	$logging_arg = getargs ("logging",$logging);
	if ($debugging) echo "logging_arg: $logging_arg \n";
	if (strlen(trim($logging_arg))) {
		$logging = strtobool($logging_arg);
		$options[LOGGING] = $logging;
	}
	if ($debugging) echo "logging final: $logging \n";
	/*
	 * get the filename arg
	 * this is required, so bail if it is not set from either the default above or the cli arg 
	 */
	if (array_key_exists(FILENAME,$options)) {
		$file_name = $options[FILENAME];
	} else {
		// we can NOT set a default for this so the arg better have something!
		$file_name = "";
	}
	if ($debugging) echo "file_name default: $file_name \n";
	$file_name_arg = getargs ("filename",$file_name);
	if ($debugging) echo "file_name_arg: $file_name_arg \n";
	if (strlen(trim($file_name_arg))) {
		$file_name = trim($file_name_arg);
		$options[FILENAME] = $file_name;
		if ($debugging) echo "file_name final: $file_name \n";
	} else {
		if (strlen(trim($file_name))) {
			if ($debugging) echo "file_name final: $file_name \n";
		} else {
			// we can NOT proceed without a file name!!
			if ($logging) {
				echo "Error: csv2pg: Missing file name. \n";
			}
			if ($debugging) {
				echo "Options array: csv2pg:\n";
				print_r($options);
			}
			return false;
		}
	}
	/*
	 * get the method arg
	 */
	if (array_key_exists(METHOD,$options)) {
		$method = $options[METHOD];
	} else {
		// we can set a default for this
		$method = 1;
	}
	if ($debugging) echo "method default: $method \n";
	$method_arg = getargs ("method",$method);
	if ($debugging) echo "method_arg: $method_arg \n";
	if (strlen(trim($method_arg))) {
		$method = intval($method_arg);
		$options[METHOD] = $method;
	}
	if ($debugging) echo "logging final: $logging \n";
	/*
	 * get the skiplines arg
	 */
	if (array_key_exists(SKIPLINES,$options)) {
		$skiplines = $options[SKIPLINES];
	} else {
		// we can set a default for this
		$skiplines = 0;
	}
	if ($debugging) echo "skiplines default: $skiplines \n";
	$skiplines_arg = getargs ("skiplines",$skiplines);
	if ($debugging) echo "skiplines_arg: $skiplines_arg \n";
	if (strlen(trim($skiplines_arg))) {
		$skiplines = intval($skiplines_arg);
		$options[SKIPLINES] = $skiplines;
	}
	if ($debugging) echo "skiplines final: $skiplines \n";
	/*
	 * get the fieldcount arg
	 */
	if (array_key_exists(FIELDCOUNT,$options)) {
		$fieldcount = $options[FIELDCOUNT];
	} else {
		// we can set a default for this, 0 means unknown
		$fieldcount = 0;
	}
	if ($debugging) echo "fieldcount default: $fieldcount \n";
	$fieldcount_arg = getargs ("fieldcount",$fieldcount);
	if ($debugging) echo "fieldcount_arg: $fieldcount_arg \n";
	if (strlen(trim($fieldcount_arg))) {
		$fieldcount = intval($fieldcount_arg);
		$options[FIELDCOUNT] = $fieldcount;
	}
	if ($debugging) echo "fieldcount final: $fieldcount \n";
	/*
	 * get the linenumbers arg
	 */
	if (array_key_exists(LINENUMBERS,$options)) {
		$linenumbers = $options[LINENUMBERS];
	} else {
		$linenumbers = false;
	}
	if ($debugging) echo "linenumbers default: $linenumbers \n";
	$linenumbers_arg = getargs ("linenumbers",$linenumbers);
	if ($debugging) echo "linenumbers_arg: $linenumbers_arg \n";
	if (strlen(trim($linenumbers_arg))) {
		$linenumbers = strtobool($linenumbers_arg);
		$options[LINENUMBERS] = $linenumbers;
	}
	if ($debugging) echo "linenumbers final: $linenumbers \n";
	/*
	 * get the pguser arg
	 */
	if (array_key_exists(PGUSER,$options)) {
		$pguser = $options[PGUSER];
	} else {
		$pguser = "";
	}
	if ($debugging) echo "pguser default: $pguser \n";
	$pguser_arg = getargs ("pguser",$pguser);
	if ($debugging) echo "pguser_arg: $pguser_arg \n";
	if (strlen(trim($pguser_arg))) {
		$pguser = trim($pguser_arg);
		$options[PGUSER] = $pguser;
		if ($debugging) echo "pguser final: $pguser \n";
	} else {
		if ($debugging) echo "pguser final: $pguser \n";
	}
	/*
	 * get the pgpassword arg
	 */
	if (array_key_exists(PGPASSWORD,$options)) {
		$pgpassword = $options[PGPASSWORD];
	} else {
		$pgpassword = "";
	}
	if ($debugging) echo "pgpassword default: $pgpassword \n";
	$pgpassword_arg = getargs ("pgpassword",$pgpassword);
	if ($debugging) echo "pgpassword_arg: $pgpassword_arg \n";
	if (strlen(trim($pgpassword_arg))) {
		$pgpassword = trim($pgpassword_arg);
		$options[PGPASSWORD] = $pgpassword;
		if ($debugging) echo "pgpassword final: $pgpassword \n";
	} else {
		if ($debugging) echo "pgpassword final: $pgpassword \n";
	}
	/*
	 * get the pgtable arg
	 * this is required, so bail if it is not set from either the default above or the cli arg
	 */
	if (array_key_exists(PGTABLE,$options)) {
		$pgtable = $options[PGTABLE];
	} else {
		// we can NOT set a default for this so the arg better have something!
		$pgtable = "";
	}
	if ($debugging) echo "pgtable default: $pgtable \n";
	$pgtable_arg = getargs ("pgtable",$pgtable);
	if ($debugging) echo "pgtable_arg: $pgtable_arg \n";
	if (strlen(trim($pgtable_arg))) {
		$pgtable = trim($pgtable_arg);
		$options[PGTABLE] = $pgtable;
		if ($debugging) echo "pgtable final: $pgtable \n";
	} else {
		if (strlen(trim($pgtable))) {
			if ($debugging) echo "pgtable final: $pgtable \n";
		} else {
			// we can NOT proceed without a pgtable!!
			if ($logging) {
				echo "Error: csv2pg: Missing pgtable. \n";
			}
			if ($debugging) {
				echo "Options array: csv2pg:\n";
				print_r($options);
			}
			return false;
		}
	}
	/*
	 * get the pgdb arg
	 * this is required, so bail if it is not set from either the default above or the cli arg
	 */
	if (array_key_exists(PGDB,$options)) {
		$pgdb = $options[PGDB];
	} else {
		// we can NOT set a default for this so the arg better have something!
		$pgdb = "";
	}
	if ($debugging) echo "pgdb default: $pgdb \n";
	$pgdb_arg = getargs ("pgdb",$pgdb);
	if ($debugging) echo "pgdb_arg: $pgdb_arg \n";
	if (strlen(trim($pgdb_arg))) {
		$pgdb = trim($pgdb_arg);
		$options[PGDB] = $pgdb;
		if ($debugging) echo "pgdb final: $pgdb \n";
	} else {
		if (strlen(trim($pgdb))) {
			if ($debugging) echo "pgdb final: $pgdb \n";
		} else {
			// we can NOT proceed without a pgdb!!
			if ($logging) {
				echo "Error: csv2pg: Missing pgdb. \n";
			}
			if ($debugging) {
				echo "Options array: csv2pg:\n";
				print_r($options);
			}
			return false;
		}
	}
	/*
	 * get the pghost arg
	 * this is required, so bail if it is not set from either the default above or the cli arg
	 */
	if (array_key_exists(PGHOST,$options)) {
		$pghost = $options[PGHOST];
	} else {
		// we can set a default for this
		$pghost = "localhost";
	}
	if ($debugging) echo "pghost default: $pghost \n";
	$pghost_arg = getargs ("pghost",$pghost);
	if ($debugging) echo "pghost_arg: $pghost_arg \n";
	if (strlen(trim($pghost_arg))) {
		$pghost = trim($pghost_arg);
		$options[PGHOST] = $pghost;
		if ($debugging) echo "pghost final: $pghost \n";
	} else {
		if ($debugging) echo "pghost final: $pghost \n";
	}
	/*
	 * get the pgport arg
	 * this is required, so bail if it is not set from either the default above or the cli arg
	 */
	if (array_key_exists(PGPORT,$options)) {
		$pgport = $options[PGPORT];
	} else {
		// we can set a default for this
		$pgport = 5432;
	}
	if ($debugging) echo "pgport default: $pgport \n";
	$pgport_arg = getargs ("pgport",$pgport);
	if ($debugging) echo "pgport_arg: $pgport_arg \n";
	if (strlen(trim($pgport_arg))) {
		$pgport = intval($pgport_arg);
		$options[PGPORT] = $pgport;
		if ($debugging) echo "pgport final: $pgport \n";
	} else {
		if ($debugging) echo "pgport final: $pgport \n";
	}
	
	/*
	 * now start the file processing
	 * convert file into array of file records
	 */
	if ($file_records = file($file_name)) {
		if ($debugging) {
			echo "File record array: csv2pg: \n";
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
			$pgconnectionstring = "dbname=$pgdb host=$pghost port=$pgport";
			if (strlen($pguser)) {
				$pgconnectionstring .= " dbuser=$dbuser";
			}
			if (strlen($pgpassword)) {
				$pgconnectionstring .= " pgpassword=$pgpassword";
			}
			$pgconnection = pg_connect($pgconnectionstring);
			if (!$pgconnection) {
				print pg_last_error($pgconnection);
				return false;
			}
			$results = pg_query($pgsql_conn, "SELECT * LIMIT 1 FROM $pgtable");
				
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
