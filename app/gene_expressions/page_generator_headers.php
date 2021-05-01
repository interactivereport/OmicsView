<?php

//24.9 - Fullying Working
$canvasXpress = "24.9";
//$canvasXpress = "28.6";

echo "<head>";

	$PAGE['Meta']['Refresh'] = intval($PAGE['Meta']['Refresh']);
	if ($PAGE['Meta']['Refresh'] > 0){
		echo "<meta http-equiv='refresh' content='{$PAGE['Meta']['Refresh']}'>\n";
	}


	if (!$PAGE['Barebone']){
		echo "<script type='text/javascript' src='./js/jquery/1.11.3/jquery.min.js'></script>\n";
		echo "<script type='text/javascript' src='./js/jquery-migrate/jquery-migrate-1.2.1.min.js'></script>\n";
		
		echo "<link   href='./js/fontawesome/css/all.min.css' rel='stylesheet' type='text/css'>\n";
		echo "<script src='./js/fontawesome/js/all.min.js'></script>\n";
	}
    
    if ($PAGE['Plugins']['selectPicker'] == 1){
		echo "<script type='text/javascript' src='./js/popper/1.14.3/popper.min.js'></script>\n";
	}
    
    
	if (!$PAGE['Barebone']){

		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['jquery'] 			= false;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['bootstrap'] 		= true;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['font-awesome'] 		= false;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['tether'] 			= true;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['bootbox'] 			= true;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['jquery-md5']		= false;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['jquery-form']		= true;
		$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['jquery-tabledit']	= false;

		include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']);
	}
    
    if (!isset($PAGE['Plugins']['dataTables']) || ($PAGE['Plugins']['dataTables'] == 1)){
    	echo "<link rel='stylesheet' type='text/css' href='./js/dataTables/1.10.23/jquery.dataTables.min.css'>\n";
    	echo "<link rel='stylesheet' type='text/css' href='./js/dataTables.buttons/1.5.6/buttons.dataTables.min.css'>\n";
    	echo "<link rel='stylesheet' type='text/css' href='./js/dataTables.select/1.3.0/select.dataTables.min.css'>\n";
    }
    
    
    if ($PAGE['Plugins']['canvasxpress'] == 1){
		echo "<link type='text/css' rel='stylesheet' href='./js/canvasxpress/canvasxpress-{$canvasXpress}/canvasXpress.css'/>\n";
    }
		
        
	if ($PAGE['Plugins']['selectPicker'] == 1){
		echo "<link rel='stylesheet' href='./js/bootstrap-select/1.13.11/bootstrap-select.min.css'/>\n";
    }


	if ($PAGE['Plugins']['dc'] == 1){
	    echo "<link type='text/css' rel='stylesheet' href='./js/dc/2.2.2/dc.min.css'>\n";
    }


	if (!$PAGE['Barebone']){
	  	echo "<link type='text/css' rel='stylesheet' href='./css/style.css'>\n";
	}
    
    if ($PAGE['Plugins']['canvasxpress']){ 
		echo "<script type='text/javascript' src='./js/canvasxpress/canvasxpress-{$canvasXpress}/canvasXpress_dc.js'></script>\n";
	}

    if (!$PAGE['Barebone']){
		if (!isset($PAGE['Plugins']['stupidTable']) || ($PAGE['Plugins']['stupidTable'] == 1)){
			echo "<script type='text/javascript' src='./js/stupid-table/stupidtable.min.js'></script>\n";
		}
	}
    
    
	if (!isset($PAGE['Plugins']['dataTables']) || ($PAGE['Plugins']['dataTables'] == 1)){
		echo "
		<script type='text/javascript' src='./js/dataTables/1.10.23/jquery.dataTables.min.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.buttons/1.5.6/dataTables.buttons.min.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.buttons/1.5.6/buttons.flash.min.js'></script>\n
		<script type='text/javascript' src='./js/jszip/2.5.0/jszip.min.js'></script>\n
		<script type='text/javascript' src='./js/pdfmake/0.1.54/pdfmake.min.js'></script>\n
		<script type='text/javascript' src='./js/pdfmake/0.1.54/vfs_fonts.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.buttons/1.5.6/buttons.html5.min.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.buttons/1.5.6/buttons.print.min.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.buttons/1.5.6/buttons.colVis.min.js'></script>\n
		<script type='text/javascript' src='./js/dataTables.select/1.3.0/dataTables.select.min.js'></script>\n";
	}
	
    
    if ($PAGE['Plugins']['selectPicker'] == 1){
		echo "<script type='text/javascript' src='./js/bootstrap-select/1.13.11/bootstrap-select.min.js'></script>\n";
    }
    
    if ($PAGE['Plugins']['Sortable'] == 1){
		echo "<script type='text/javascript' src='./js/bootstrap-html5sortable/jquery.sortable.min.js'></script>\n";
    }
    
    if ($PAGE['Plugins']['tinymce'] == 1){
		echo "<script type='text/javascript' src='./js/tinymce/tinymce-4.7.0/tinymce.min.js'></script>\n";
	}

	if ($PAGE['Plugins']['dc'] == 1){
		echo "<script type='text/javascript' src='./js/d3/d3-3.5.17.min.js'></script>\n";
		echo "<script type='text/javascript' src='./js/crossfilter/crossfilter-1.4.7.js'></script>\n";
		echo "<script type='text/javascript' src='./js/dc/2.2.2/dc.min.js'></script>\n";
	}
    
    if ($PAGE['Plugins']['plotly'] == 1){
		/*        echo "<script type='text/javascript' src='http://cdn.plot.ly/plotly-latest.min.js'></script>\n"; */
        echo "<script type='text/javascript' src='./js/plotly/plotly-1.58.4/plotly.min.js'></script>\n";
	}
	
	if ($PAGE['Plugins']['pdfObject'] == 1){
		echo "<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js'></script>\n";
    }


	if (!$PAGE['Barebone']){
		echo "
		<!--[if lt IE 9]>
			<script src='//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js'></script>
			<script src='//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js'></script>
		<![endif]-->";
	}

echo "</head>";

?>