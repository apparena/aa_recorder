<?php
/**
 * All export functionality of data
 * @author Sebastian Buckpesch (s.buckpesch@iconsultants.eu)
 * @version 0.01
 * @copyright  Copyright (c) 2011 iConsultants UG (http://www.iconsultants.eu)
 * @license	http://www.gnu.org/licenses/gpl-3.0.html
 */
class iCon_Export {

	public function arrayToCsv($arrData, $arrTitle, $filename = 'export.csv')
	{
	    $csv_terminated = "\n";
	    $csv_separator = ";";
	    $csv_enclosed = '"';
	    $csv_escaped = "\\";

	   	$fields_cnt = count($arrData[0]);
	   	$schema_insert = '';

      foreach($arrTitle as $title)
	    {
	        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
	            stripslashes($title)) . $csv_enclosed;
	        $schema_insert .= $l;
	        $schema_insert .= $csv_separator;
	    } // end for

	    $out = trim(substr($schema_insert, 0, -1));
	    $out .= $csv_terminated;

	    // Format the data
	    foreach ($arrData as $row) {
	    	$schema_insert = '';
	    	$j = 1;
	    	foreach ($row as $field) {
	    		if ($field == '0' || $field != ''){
	                if ($csv_enclosed == ''){
	                    $schema_insert .= $field;
	                } else {
	                    $schema_insert .= $csv_enclosed . 
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $field) . $csv_enclosed;
	                }
	            } else {
	                $schema_insert .= '';
	            }

	            if ($j < $fields_cnt) {
	                $schema_insert .= $csv_separator;
	                $j++;
	            }
	    	}

	        $out .= $schema_insert;
	        $out .= $csv_terminated;
	    } // end while

	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Length: " . strlen($out));
	    header("Content-type: text/x-csv;charset=utf8");
	    header("Content-Disposition: attachment; filename=$filename");
	    echo $out;
		return true;
	}

}

?>
