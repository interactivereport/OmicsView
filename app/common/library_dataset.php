<?php

function has_public_data(){
	
	global $BXAF_CONFIG;
	
	if (isset($BXAF_CONFIG['HAS_PUBLIC_DATA'])){
		return boolval($BXAF_CONFIG['HAS_PUBLIC_DATA']);
	} else {
		return true;	
	}
}

function get_gene_type(){
	global $BXAF_CONFIG;
	
	return 'Gene';
	
}

function has_public_comparison_data(){
	
	global $BXAF_CONFIG;
	
	if (isset($BXAF_CONFIG['HAS_PUBLIC_DATA'])){
		return boolval($BXAF_CONFIG['HAS_PUBLIC_DATA']);
	} else {
		return true;	
	}
}

function is_internal_column_flexible($table = NULL){
	
	return false;
		
}

function support_table_format_data($table = NULL){

	global $BXAF_CONFIG;
	
	$table = trim($table);
	
	if ($table == '') return false;
	
	$supportedTables = array('GeneLevelExpression');
	
	if (in_array($table, $supportedTables)){
		return true;
	}
	
	return false;
		
}


function has_flexible_column(){

	return false;
}

?>