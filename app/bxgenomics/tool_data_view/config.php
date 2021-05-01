<?php

include_once(dirname(__DIR__) . '/config/config.php');


$gene_annot_file = $BXAF_CONFIG['TABIX_INDEX']['GeneAnnotation.gz'];

$tabix_sample_file = $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Sample'];
$tabix_gene_file   = $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM'];

$tensor_URL = $BXAF_CONFIG['BXAF_ROOT_URL'] . "embedding-projector-standalone/?config=";


$BXAF_CONFIG['SPECIES'] = ucfirst($BXAF_CONFIG['APP_SPECIES']);




$projects_info_file = "{$BXAF_CONFIG['WORK_DIR']}bxgenomics/tool_data_view/projects.info";
$project_dir = "{$BXAF_CONFIG['WORK_DIR']}bxgenomics/tool_data_view/projects/";
$project_url = "{$BXAF_CONFIG['BXAF_ROOT_URL']}{$BXAF_CONFIG['WORK_URL']}bxgenomics/tool_data_view/projects/";

if(! file_exists($project_dir) ) mkdir($project_dir, 0775, true);

$projects_info = array();
if(file_exists($projects_info_file)){
    $content = file_get_contents($projects_info_file);
    if($content != '') $projects_info = unserialize($content);
}


$temp_dir = $BXAF_CONFIG['BXGENOMICS_CACHE_DIR'] . 'tool_data_view/';

$moved_folders = 0;
if(is_dir($temp_dir) && is_readable($temp_dir)){

	$d = dir($temp_dir);
	while (false !== ($fn = $d->read())) {

		if( $fn != '.' && $fn != '..' &&
            $fn != 'cache' && $fn != '0' && $fn != 'bookmarks' &&
            (! preg_match("/^[\.\_]/", $fn) ) &&
            is_dir("{$temp_dir}{$fn}") &&
            ! file_exists("{$project_dir}{$fn}") && ! array_key_exists($fn, $projects_info)
        ){

            $info = array();

            $info['Name'] = $fn;
            $info['Date'] = date ("Y-m-d H:i:s", filemtime("{$temp_dir}{$fn}") );

            $sample_info = array();
			$sample_info_file = "{$temp_dir}{$fn}/sample_info.csv";
			if(file_exists($sample_info_file)){
                if (($handle = fopen($sample_info_file, "r")) !== FALSE) {
                    $head = fgetcsv($handle);
                    $head_flip = array_flip($head);

                    while (($data = fgetcsv($handle)) !== FALSE) {
                        $sample_info[ $data[ $head_flip['SampleIndex'] ] ] = $data[ $head_flip['SampleID'] ];
                    }
                    fclose($handle);
                }
			}
            $info['Samples'] = $sample_info;

            $info['Owner'] = $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
            $info['Permission'] = 'Private';

            $info['DIR'] = "{$project_dir}{$fn}/";
            $info['URL'] = "{$project_url}{$fn}/";

            if(rename("{$temp_dir}{$fn}", "{$project_dir}{$fn}")){
                $projects_info[ $fn ] = $info;
                $moved_folders++;
            }

		}

	}
	$d->close();
}
if($moved_folders > 0){
    file_put_contents($projects_info_file, serialize($projects_info));
}

?>