<?php

// Convert IDs to "Name (ID: )" for one or multiple rows
if (!function_exists('category_ids_to_nameids')) {
    function category_ids_to_nameids($ids, $category = "Comaprison") {

        $id_names = category_text_to_idnames($ids, 'id', $category);

        if(is_array($id_names) && count($id_names) > 0){
            $result = array();
            foreach($id_names as $id=>$name) {
                $result[] = "$name (ID: $id)";
            }
            return implode("\n", $result);
        }
        else return '';
    }
}
// Same as category_ids_to_nameids()
if (!function_exists('category_id_to_nameid')) {
    function category_id_to_nameid($id, $category="Comaprison") {

        return category_ids_to_nameids($id, $category);
    }
}


// Convert List of ID to List of "Name" ($return_type == 'names') Array
// Convert List of ID to List of "Name (ID: )" ($return_type == 'name(id)') Array
if (!function_exists('category_id_list_to_nameid')) {
    function category_id_list_to_nameid($ids, $category="Comaprison", $return_type = 'names') {

        $id_names = category_list_to_idnames($ids, 'id', $category);

        if($return_type == 'name(id)'){
            $result = array();
            foreach($id_names as $id=>$name) {
              $result[$id] = "$name (ID: $id)";
            }
            return $result;
        }
        else {
            return $id_names;
        }

    }
}


// Convert "Name (ID: xxx)" or "Name|||othertext" to ID for one row, validated with database records
if (!function_exists('category_nameid_to_id')) {
    function category_nameid_to_id($nameid, $category='Gene') {
      global $BXAF_MODULE_CONN, $BXAF_CONFIG;

        $info = array();
        if(strpos($nameid, ' (ID:') !== false) $info = explode(' (ID:', $nameid);
        else if(strpos($nameid, '|||') !== false) $info = explode('|||', $nameid);

        $id = 0;
        if (is_array($info) && count($info) == 2) {
            $id = intval( preg_replace("/[^\d]/", '', $info[1]) );
        }

        $categories = array(
            'gene'      =>$BXAF_CONFIG['TBL_BXGENOMICS_GENES'],
            'sample'    =>$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'],
            'project'   => $BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS'],
            'comparison'=>$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']
        );
        $categories2 = array(
            'gene'      =>$BXAF_CONFIG['TBL_BXGENOMICS_GENES'],
            'sample'    => 'App_User_Data_Samples',
            'project'   => 'App_User_Data_Projects',
            'comparison'=> 'App_User_Data_Comparisons'
        );

        $category = strtolower(trim(strval($category)));
        if(! array_key_exists($category, $categories)) $category = 'comparison';

        if($id > 0){
            $sql = "SELECT `ID` FROM ?n WHERE `ID` = ?s ";
            $id2 = $BXAF_MODULE_CONN -> get_one($sql, $categories[$category], $id);
            if($id2 <= 0){
                $sql = "SELECT `ID` FROM ?n WHERE `ID` = ?s ";
                $id2 = $BXAF_MODULE_CONN -> get_one($sql, $categories2[$category], $id);
            }
            return $id2;
        }
        else {
            $fld = 'Name';
            if($category == 'name') $fld = 'GeneName';
            $sql = "SELECT `ID` FROM ?n WHERE ?n = ?s ";
            $id2 = $BXAF_MODULE_CONN -> get_one($sql, $categories[$category], $fld, $nameid);
            if($id2 <= 0){
                $sql = "SELECT `ID` FROM ?n WHERE ?n = ?s ";
                $id2 = $BXAF_MODULE_CONN -> get_one($sql, $categories2[$category], $fld, $nameid);
            }
            return $id2;
        }
    }
}



// Convert "Name (ID: xxx)" or "Name|||othertext" to Name for one row, no database record validation !
if (!function_exists('category_nameid_to_name')) {
    function category_nameid_to_name($nameid) {
        if(strpos($nameid, ' (ID:') !== false) return substr($nameid, 0, strpos($nameid, ' (ID:'));
        else if(strpos($nameid, '|||') !== false) return substr($nameid, 0, strpos($nameid, '|||'));
        else return trim($nameid);
    }
}


// Convert "Name (ID: xxx)" or "Name|||othertext" to ID=>Name Array for multiple rows
if (!function_exists('category_nameid_list_to_name')) {
    function category_nameid_list_to_name($list, $category='Gene') {

        if (! is_array($list) || count($list) <= 0) return array();
        $new_list = array();
        foreach ($list as $nameid) {
            if(trim($nameid) != '') $new_list[] = category_nameid_to_name($nameid);
        }
        return category_list_to_idnames($new_list, 'name', $category);
    }
}


// Convert "Name (ID: xxx)" or "Name|||othertext" to Name=>ID Array for multiple rows
if (!function_exists('category_nameid_list_to_id')) {
    function category_nameid_list_to_id($list, $category='Gene') {
        return array_flip( category_nameid_list_to_name($list, $category) );
    }
}




// Convert List of IDs or Names to List of ID=>Name Array
if (!function_exists('category_list_to_idnames')) {
    function category_list_to_idnames($list, $value_type = 'name', $category = 'gene') {

        global $BXAF_MODULE_CONN, $BXAF_CONFIG;

        if (! is_array($list) || count($list) <= 0) return array();

        $value_type = strtolower(trim(strval($value_type)));
        $category = strtolower(trim(strval($category)));

        $categories = array(
            'gene'      =>$BXAF_CONFIG['TBL_BXGENOMICS_GENES'],
            'sample'    =>$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'],
            'project'   => $BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS'],
            'comparison'=>$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']
        );
        if(! array_key_exists($category, $categories)) return array();


        $categories2 = array(
            'gene'      =>$BXAF_CONFIG['TBL_BXGENOMICS_GENES'],
            'sample'    => 'App_User_Data_Samples',
            'project'   => 'App_User_Data_Projects',
            'comparison'=> 'App_User_Data_Comparisons'
        );

        $table_field_index = array(
            'gene'      => 'GeneIndex',
            'sample'    => 'SampleIndex',
            'project'   => 'ProjectIndex',
            'comparison'=> 'ComparisonIndex'
        );
        $table_field_name = array(
            'gene'      => 'GeneName',
            'sample'    => 'SampleID',
            'project'   => 'ProjectID',
            'comparison'=> 'ComparisonID'
        );

        $value_type = ($value_type == 'id') ? $table_field_index[$category] : $table_field_name[$category];

        $sql = "SELECT ?n, ?n FROM ?n WHERE ?n IN (?a)";
        $result = $BXAF_MODULE_CONN -> get_assoc($table_field_index[$category], $sql, $table_field_index[$category], $table_field_name[$category], $categories[$category], $value_type, $list);

        if (! is_array($result) || count($result) <= 0) {
            $sql = "SELECT ?n, ?n FROM ?n WHERE ?n IN (?a)";
            $result = $BXAF_MODULE_CONN -> get_assoc($table_field_index[$category], $sql, $table_field_index[$category], $table_field_name[$category], $categories2[$category], $value_type, $list);
        }
        return $result;
    }
}

// Convert Text (IDs or Names, split by commas or spaces) to List of ID=>Name Array
if (!function_exists('category_text_to_idnames')) {
    function category_text_to_idnames($text, $value_type = 'name', $category = 'gene') {

        global $BXAF_MODULE_CONN, $BXAF_CONFIG;

        $text = trim(strval($text));
        if($text == '') return array();

        // split the phrase by any number of commas or space characters, which include " ", \r, \t, \n and \f
        $list = preg_split("/[\s,]+/", $text, NULL, PREG_SPLIT_NO_EMPTY);

        return category_list_to_idnames($list, $value_type, $category);
    }
}





if (!function_exists('bxaf_save_to_cache')) {
    function bxaf_save_to_cache($array, $name = '', $notes = '') {

        global $BXAF_MODULE_CONN;

        $list = array('Input' => $array, 'Name' => $name, 'Notes' => $notes );

        $key = sha1(serialize($list));
        $cache_id = $BXAF_MODULE_CONN->get_one("SELECT `ID` FROM ?n WHERE `Key` = ?s", 'App_Cache', $key);

        if($cache_id > 0) return $key;

        $value = bzcompress(json_encode($list));

        $info = array(
            'Key' => $key,
            'Value' => $value,
            'Category' => 'URL',
            'Json_Decode_Assoc' => 1
        );

        $cache_id = $BXAF_MODULE_CONN->insert('App_Cache', $info);

        if($cache_id > 0) return $key;
        else return '';

    }
}

if (!function_exists('bxaf_get_from_cache')) {
    function bxaf_get_from_cache($key, $output = 'Input') {

        global $BXAF_MODULE_CONN;

        $info = $BXAF_MODULE_CONN->get_row("SELECT * FROM ?n WHERE `Key` = ?s", 'App_Cache', $key);
        if(! is_array($info) || count($info) <= 0 || ! isset($info['Value'])) return '';

        $value = $info['Value'];
        $value = bzdecompress($value);

        if ($info['Json_Decode_Assoc'] == 1){
            $value = json_decode($value, true);
        }
        elseif ($info['Json_Decode_Assoc'] == 0){
            $value = json_decode($value);
        }
        elseif ($info['Json_Decode_Assoc'] == 2){
            $value = unserialize($value);
        }

        if($output == 'Input' && is_array($value) && array_key_exists('Input', $value)) return $value['Input'];
        else return $value;

    }
}




// This function improves the function tabix_search_records_with_index_all() by filtering results
if(! function_exists('tabix_search_bxgenomics')){
    function tabix_search_bxgenomics($primaryIndex, $secondaryIndex, $table){
    	$outputFormat = 'GetArrayAssoc';

    	$results = array();
    	if($table == 'ComparisonData'){
    		$tabix_results  = tabix_search_records_with_index_all($primaryIndex, $secondaryIndex, $table, $outputFormat);

    		foreach($tabix_results as $i=>$row){
    		    if(is_numeric($row['ComparisonIndex']) && is_numeric($row['GeneIndex']) && (is_numeric($row['Log2FoldChange']) || is_numeric($row['PValue']) || is_numeric($row['AdjustedPValue'])) ){
    		        $results[] = array(
    		            'ComparisonIndex'=>$row['ComparisonIndex'],
    		            'GeneIndex'=>$row['GeneIndex'],
    		            'Log2FoldChange'=>is_numeric($row['Log2FoldChange']) ? $row['Log2FoldChange'] : "",
    		            'PValue'=>is_numeric($row['PValue']) ? $row['PValue'] : "",
    		            'AdjustedPValue'=>is_numeric($row['AdjustedPValue']) ? $row['AdjustedPValue'] : ""
    		        );
    		    }
    		}
    	}
    	else if($table == 'GeneLevelExpression' || $table == 'GeneFPKM'){
    		$tabix_results  = tabix_search_records_with_index_all($primaryIndex, $secondaryIndex, $table , $outputFormat);

    		foreach($tabix_results as $i=>$row){
    		    if(is_numeric($row['SampleIndex']) && is_numeric($row['GeneIndex']) && (is_numeric($row['Value']) || is_numeric($row['FPKM'])) ){
    		        $results[] = array(
    		            'SampleIndex'=>$row['SampleIndex'],
    		            'GeneIndex'=>$row['GeneIndex'],
    		            'Value'=>is_numeric($row['Value']) ? $row['Value'] : $row['FPKM']
    		        );
    		    }
    		}
    	}
    	return $results;
    }
}



// This function improves the function tabix_search_records_with_index_all() by filtering results
if(! function_exists('get_short_url')){
    function get_short_url($long_url){
        return file_get_contents( 'http://x2y.me/api.php?url=' . urlencode($long_url) );
    }
}


?>