<?php
/*
 * this file is a project specific wrapper php ptrogram that can be modified
 */

/*
 * the awesome csv2php program - do not modify the following file!!
 */
include "csv2pg.php";

/*
 * constants - do not modify
 */
define("METHOD","method");
define("FILENAME","filename");
define("DELIMITER","delimiter");
define("DEBUGGING","debugging");
define("LOGGING","logging");
define("SKIPLINES","skiplines");
define("FIELDCOUNT","fieldcount");
define("LINENUMBERS","linenumbers");
define("MODFLOWWELLFILE","modflowwellfile");
define("PGUSER","pguser");
define("PGPASSWORD","pgpassword");
define("PGTABLE","pgtable");
define("PGHOST","pghost");
define("PGDB","pgdb");
define("PGPORT","pgport");

/*
 * argument defaults
 *   these are OK to modify, as long as the values are valid
 * much like pg2gviz
 * in other words, defaults get used unless over-ridden by command line args
 * the difference is they all get stuffed into an options array, makes for much cleaner code
 */
$options[FILENAME]="dirty_data_1.txt";
$options[DELIMITER]=","; //typical: comma ",";  tab "\t"; space " ", "fixedwidth_x" means read it as fixed width fields of size x
$options[METHOD]=1;
$options[DEBUGGING]=false;
$options[LOGGING]=true;
$options[SKIPLINES]=0;
$options[FIELDCOUNT]=3; // a field count of <0 means convert it into a relational table with the fields row#, column#, value
$options[LINENUMBERS]=false;
$options[MODFLOWWELLFILE]=0;
$options[PGUSER]="";
$options[PGPASSWORD]="";
$options[PGTABLE]="";
$options[PGDB]="";
$options[PGHOST]="localhost";
$options[PGPORT]=5432;

/*
 * the code - do not modify 
 */
if (array_key_exists(LOGGING,$options)) {
	$logging = $options[LOGGING];
} else {
	$logging = false;
}
if ($logging) echo "Beginning csv2pg at ".date(DATE_RFC2822)."\n";
// need this for command line args on the CLI to work with the getargs() function
foreach ($argv as $arg) {
	$exploded_array=explode("=",$arg);
	if(count($exploded_array)==2)
		$_GET[$exploded_array[0]]=$exploded_array[1];
	else
		$_GET[]=$exploded_array[0];
}
// call the csv2pg
if (csv2pg($options)) {
	if ($logging) echo "csv2pg ended successfully at ".date(DATE_RFC2822)."\n";
} else {
	if ($logging) echo "csv2pg ended unsuccessfully at ".date(DATE_RFC2822)."\n";
}
