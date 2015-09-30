<?php
/*
 * our awesome php utils
 */
include "misc_io.php";
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
	$debugging_arg = getargs ("debugging",$debugging);
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
			if ($logging) echo "Error: csv2pg: Missing file name. \n";
			if ($debugging) print_r($options);
			return false;
		}
	}
	/*
	 * get the delimiter arg
	 */
	if (array_key_exists(DELIMITER,$options)) {
		$delimiter = $options[DELIMITER];
	} else {
		$delimiter = ",";
	}
	if ($debugging) echo "delimiter default: $delimiter \n";
	$delimiter_arg = getargs ("delimiter",$delimiter);
	if ($debugging) echo "delimiter_arg: $delimiter_arg \n";
	$fixed_width=0;
	if (strlen(trim($delimiter_arg))) {
		switch($delimiter_arg) {
			case "tab":
				$delimiter = "\t";
				break;
			case "space":
				$delimiter = " ";
				break;
			case "fixed_".substr($delimiter_arg,6): // emulate fixed_*
				$fixed_width=intval(substr($delimiter_arg,6));
				if ($logging) echo "switched to fixed width fields of size: $fixed_width \n";
				break;
			case "comma":
			default:
				$delimiter = ",";
				break;
		}
		$options[DELIMITER] = $delimiter;
	}
	if ($debugging) echo "delimiter final: $delimiter \n";
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
			if ($logging) echo "Error: csv2pg: Missing pgtable. \n";
			if ($debugging) print_r($options);
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
			if ($logging) echo "Error: csv2pg: Missing pgdb. \n";
			if ($debugging) print_r($options);
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
		if ($debugging) print_r($file_records);
		/*
		 * convert array of file records into an array of file field arrays
		 */
		if($fixed_width){
			// read the records with fixed width fields of size $fixed_width
			$file_fields = fixedwidth2array($file_records,$fixed_width,$options);
		} else {
			//else assume it is a delimited record
			$file_fields = csv2array($file_records,$options);
		}
		if ($file_fields) {
			if ($debugging) print_r($file_fields);
			/*
			 * append field values to pg table
			 */
			$pgconnectionstring = "dbname=$pgdb host=$pghost port=$pgport";
			if (strlen($pguser)) {
				$pgconnectionstring .= " user=$pguser";
			}
			if (strlen($pgpassword)) {
				$pgconnectionstring .= " password=$pgpassword";
			}
			$pgconnection = pg_connect($pgconnectionstring);
			if (!$pgconnection) {
				if ($logging) echo "Error: could not make database connection: ".pg_last_error($pgconnection);
				return false;
			}
			$results = pg_query($pgconnection, "SELECT * FROM $pgtable LIMIT 1");
			$pgtable_fieldcount = pg_num_fields($results);
			$file_recordcount = count($file_records);
			if ($logging) echo "\$pgtable_fieldcount: $pgtable_fieldcount\n";
			if ($logging) echo "\$fieldcount (expected): $fieldcount\n";
			if ($logging) echo "\$file_recordcount: $file_recordcount\n";
			if ($linenumbers) if ($logging) echo "Warning: using first field in table $pgtable as a line number field \n";
			$arraytocopy = array();
			//loop over the file records
			for($recordnumber=0;$recordnumber<$file_recordcount;$recordnumber++) {
				if ($recordnumber>=$skiplines) {
					$file_fieldcount = count($file_fields[$recordnumber]);
					if ($debugging) echo "\$recordnumber: ".($recordnumber+1)."  \$file_fieldcount: $file_fieldcount\n";
					/*
					 * if $fieldcount >=0 then copy the record fields to database table fields, 1 to 1
					 * but if $fieldcount <0 then it means to create relational records in the target table for every field. like: row#, column#, value
					 */
					if ($fieldcount>=0) {
						if ($fieldcount && ($file_fieldcount<>$fieldcount)) {
							if ($logging) echo "Warning: record ".($recordnumber+1)." in file $file_name has $file_fieldcount fields, expected $fieldcount \n";
						}
						if ($file_fieldcount>$pgtable_fieldcount) {
							if ($logging) echo "Warning: record ".($recordnumber+1)." in file $file_name has $file_fieldcount fields, pg table $pgtable only has $pgtable_fieldcount \n";
						} 
						/*
						 * create the tab delimited string to use for the "row"
						 */
						$row = "";
						$fieldcounttocopy = $pgtable_fieldcount;
						$fieldcountcopied = 0;
						/*
						 * use the first field in the output table for file line numbers
						 */
						if ($linenumbers) {
							$row .= $recordnumber+1;
							$fieldcounttocopy = $pgtable_fieldcount-1;
							$fieldcountcopied=1;
						}
						/*
						 * now fill out the tab delimited string to paste in each row
						 */
						for ($fieldnumber=0;$fieldnumber<$fieldcounttocopy;$fieldnumber++) {
							if ($fieldcountcopied) $row .= "\t";
							if ($fieldnumber<$file_fieldcount) {
								$row .= $file_fields[$recordnumber][$fieldnumber];
							} else {
								$row .= "\\NULL";
							}
							$fieldcountcopied++;
						}
						$row .= "\n";
						$arraytocopy[] = $row;
					} else {
						/*
						 * make sure the target table is the right size, should be three fields
						 */
						if ($pgtable_fieldcount<>3) {
							if ($logging) echo "Error, relational table must have three fields.  The pg table $pgtable has $pgtable_fieldcount \n";
							return false;
						} 
						/*
						 * create the tab delimited string to use for the "row"
						 */
						$fieldcounttocopy = $file_fieldcount;
						$fieldcountcopied = 0;
						/*
						 * loop over all the fields, creating a record out of each
						 */
						for ($fieldnumber=0;$fieldnumber<$fieldcounttocopy;$fieldnumber++) {
							/*
							 * now fill out the tab delimited string to paste in each row
							 */
							$zero_base=false;
							if ($zero_base) {
								$r=($recordnumber-$skiplines);
								$c=$fieldnumber;
							} else {
								$r=($recordnumber-$skiplines)+1;
								$c=$fieldnumber+1;
							}
							$val=$file_fields[$recordnumber][$fieldnumber];
							if ($logging) echo "r $r \n";
							if ($logging) echo "c $c \n";
							if ($logging) echo "val $val \n";
							if ($logging) echo "$r\t$c\t$val\n";
							$arraytocopy[] = "$r\t$c\t$val\n";
							$fieldcountcopied++;
						}
					}
				} else {
					if ($logging) print "Warning: skipping line ".($recordnumber+1)." of file $file_name \n";
				}
			}
			return pg_copy_from($pgconnection,$pgtable,$arraytocopy,"\t","\\NULL");
		} else {
			if ($logging) echo "Error: csv2pg: could not convert file record array into file field arrays\n";
			return false;
		}
	} else {
		if ($logging) echo "Error: csv2pg: could not read from file: $file_name \n";
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
	if (array_key_exists(DELIMITER,$options)) {
		$delimiter = $options[DELIMITER];
	} else {
		$delimiter = ",";
	}
	$enclosure='"';
	$escape='\\';
	if (array_key_exists(METHOD,$options)) {
		$method = $options[METHOD];
	} else {
		if ($logging) echo "Error: csv2array: missing file processing method\n";
		if ($debugging) print_r($options);
		return false;
	}
	// there are better more rigorous methods out there, but this was a quick and inexpensive project...
	//   put the better ones here when the need arises
	switch($method) {
		case 1:
			$csvArray = array();
			foreach ($file_records as $file_record)
				$csvArray[] = str_getcsv($file_record,$delimiter,$enclosure,$escape);
			return $csvArray;
		case 0:
		default:
			if ($logging)echo "Error: csv2array: invalid file processing method specified: ".$method."\n";
			return false;
	}
}
function fixedwidth2array($file_records,$fixed_width,$options=array()) {
	if (!$fixed_width) {
		return false;
	}
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
	$recordArray = array();
	foreach ($file_records as $file_record) {
		$fieldArray = array();
		$recordLength = strlen($file_record);
		for ($i = 0; $i < $recordLength; $i=$i+$fixed_width) {
			$fieldArray[] = substr($file_record,$i,$fixed_width);
		}
		$recordArray[] = $fieldArray;
	}
	return $recordArray;
}
