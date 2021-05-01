<?php

function genesets_api_get_header_code($GENESET_SPECIES = '', $MY_SECRET_ID = '', $GENESET_GENES_ID = ''){
	
	global $BXAF_CONFIG;
	
	
	if ($BXAF_CONFIG['GENESETS_API']['genesets.php'] == '') return false;
	
	
	if ($GENESET_SPECIES == ''){
		$GENESET_SPECIES = $BXAF_CONFIG['APP_SPECIES'];	
	}
	
	if ($MY_SECRET_ID == ''){
		$MY_SECRET_ID = session_id();	
	}
	
	$results = '';
	$results .= "<script>
					var GENESET_ACTION_URL 			= '{$BXAF_CONFIG['GENESETS_API']['genesets.php']}'; 
					var MY_SECRET_ID 				= '{$MY_SECRET_ID}'; 
					//var GENESET_SPECIES 			= '{$GENESET_SPECIES}';
					var GENESET_GENES_OUTPUT_ID 	= '{$GENESET_GENES_ID}';
					var GENESET_INIT_ENABLE			= true;
					var GENESET_DEFAULT_TABLE		= '';
					var GENESET_DEFAULT_SPECIES		= '{$GENESET_SPECIES}';
					var GENESET_DEFAULT_MIN			= 10;
					var GENESET_DEFAULT_MAX			= 500;
				 </script>
				 
				 <link  href='{$BXAF_CONFIG['GENESETS_API']['genesets.css']}' rel='stylesheet'>
				 <script src='{$BXAF_CONFIG['GENESETS_API']['genesets.js']}'></script>
				 <style>
					.geneset_input_section_species{
						display: none;
					}
				</style>
				";
				
	return $results;
	
	
}


function genesets_api_get_body_code(){
	
	global $BXAF_CONFIG, $APP_MESSAGE;
	
	if ($BXAF_CONFIG['GENESETS_API']['genesets.php'] == '') return false;
	
	$results = '';
	$results .= "<div id='div_geneset1' class='div_geneset'>";
		$results .= "<input style='width: 30rem;' id='Geneset_Name1' name='Geneset_Name1' type='hidden' class='form-control form-control-sm geneset_name' />";
		$results .= "<a href='javascript: void(0);' class='btn_browse_geneset'>" . "<i class='fa-fw fas fa-search' aria-hidden='true'></i>" . " {$APP_MESSAGE['Search by Geneset']}</a>";
		$results .= "<div id='Dropdown1' class='geneset_dropdown dropdown-menu'></div>";
		$results .= "<input class='geneset_id' type='hidden' id='Geneset_ID1' name='Geneset_ID1' value=''/>";
	$results .= "</div>";	
	
	return $results;
}

/*
function genesets_api_get_genes_by_genelist($species, $table, $name){
	
	global $BXAF_CONFIG, $APP_MESSAGE;
	
	if ($BXAF_CONFIG['GENESETS_API']['genesets.php'] == '') return false;
	
	if ($species == ''){
		$species = $BXAF_CONFIG['APP_SPECIES'];	
	}
	
	return false;
}
*/

?>