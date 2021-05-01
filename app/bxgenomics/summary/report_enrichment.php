<?php

include_once(__DIR__ . "/config.php");

$go_analysis_files = array(
    array("biocyc.txt", "BIOCYC pathways", "Groups of proteins in the same pathways", "http://biocyc.org/", "BIOCYC"),
    array("biological_process.txt", "biological process", "Functional groupings of proteins", "http://www.geneontology.org", "Gene Ontology"),
    array("cellular_component.txt", "cellular component", "Protein localization", "http://www.geneontology.org", "Gene Ontology"),
    array("chromosome.txt", "chromosome location", "Genes with similar chromosome localization", "http://www.ncbi.nlm.nih.gov/gene", "NCBI Gene"),
    array("cosmic.txt", "COSMIC cancer mutations", "Genes mutated in similar cancers", "http://cancer.sanger.ac.uk/cancergenome/projects/cosmic/", "COSMIC"),
    array("gene3d.txt", "gene3d domains", "Proteins with similar domains and features", "http://cathwww.biochem.ucl.ac.uk:8080/Gene3D", "Gene3D"),
    array("gwas.txt", "GWAS genes", "Genes mutated in similar diseases", "http://www.genome.gov/26525384", "GWAS Catalog"),
    array("interactions.txt", "protein interactions", "Proteins interacting with a common protein (BIND, EcoCyc, HPRD", "http://www.ncbi.nlm.nih.gov/gene", "NCBI Gene"),
    array("interpro.txt", "interpro domains", "Proteins with similar domains and features", "http://www.ebi.ac.uk/interpro/", "Interpro"),
    array("kegg.txt", "KEGG pathways", "Groups of proteins in the same pathways", "http://www.genome.jp/kegg/pathway.html", "KEGG"),
    array("lipidmaps.txt", "Lipid Maps pathways", "Groups of proteins in the same lipid pathways", "http://www.ncbi.nlm.nih.gov/biosystems/", "Lipid Maps/Biosystems"),
    array("molecular_function.txt", "molecular function", "Mechanistic actions of proteins", "http://www.geneontology.org", "Gene Ontology"),
    array("msigdb.txt", "MSigDB lists", "Genes sets for pathways, factor/miRNA target predictions, expression patterns, etc.", "http://www.broadinstitute.org/gsea/msigdb/index.jsp", "MSigDB"),
    array("pathwayInteractionDB.txt", "Pathway Interaction DB", "Groups of proteins in the same pathways", "http://pid.nci.nih.gov/", "Pathway Interaction Database"),
    array("pfam.txt", "pfam domains", "Proteins with similar domains and features", "http://www.sanger.ac.uk/Software/Pfam/", "Pfam"),
    array("prints.txt", "prints domains", "Proteins with similar domains and features", "http://www.bioinf.manchester.ac.uk/dbbrowser/PRINTS/", "PRINTS"),
    array("prosite.txt", "prosite domains", "Proteins with similar domains and features", "http://ca.expasy.org/prosite/", "Prosite"),
    array("reactome.txt", "REACTOME pathways", "Groups of proteins in the same pathways", "http://www.reactome.org/PathwayBrowser/", "REACTOME"),
    array("smart.txt", "smart domains", "Proteins with similar domains and features", "http://smart.embl-heidelberg.de/", "SMART"),
    array("smpdb.txt", "SMPDB pathways", "Groups of proteins in the same pathways", "http://www.smpdb.ca/", "SMPDB"),
    array("wikipathways.txt", "WikiPathways", "Groups of proteins in the same pathways", "http://www.wikipathways.org/index.php/Special:BrowsePathwaysPage", "Wikipathways"),
	array("custom_gene_set.txt", "Custom Gene Set", "Custom Gene Set", "", "Custom Gene Set"),
);
$combined_file_name = "combined_all.txt";
$combined_sorted_file_name = "combined_all_sorted.txt";


if(isset($_GET['action']) && $_GET['action'] == "get_gene_list") {

    $comparison_dir = base64_decode($_GET['dir']);

    if(file_exists($comparison_dir . $combined_sorted_file_name)){
        $file = new SplFileObject($comparison_dir . $combined_sorted_file_name);
    	$file->seek(intval($_GET['row_number']));
    	$row = explode("\t", $file->current() );

        if(array_key_exists(11, $row)) echo str_replace(',', ', ', $row[11]);
        else echo "No gene names found. $comparison_dir . $combined_sorted_file_name";
    }
    else echo "Can not find file $comparison_dir . $combined_sorted_file_name.";

	exit();
}

$COMPARISON_INDEX = intval($_GET['id']);
$direction = (array_key_exists('direction', $_GET) && $_GET['direction'] == 'Down') ? 'Down' : 'Up';

$comparison_dir = "";
$comparison_url = "";

$comparison_dir = internal_data_get_comparison_directory($COMPARISON_INDEX) . "comp_{$COMPARISON_INDEX}_GO_Analysis_{$direction}/";
$comparison_url = internal_data_get_comparison_URL($COMPARISON_INDEX) . "comp_{$COMPARISON_INDEX}_GO_Analysis_{$direction}/";


if(! file_exists($comparison_dir . $combined_sorted_file_name)){

    // combine all files and sort
    $handle_out = fopen($comparison_dir . $combined_file_name, "w");
    fwrite($handle_out, "GO Tree	TermID	Term	Enrichment	logP	Genes in Term	Target Genes in Term	Fraction of Targets in Term	Total Target Genes	Total Genes	Entrez Gene IDs	Gene Symbols	Raw_Pvalue\n");

    foreach($go_analysis_files as $files){

        if(file_exists($comparison_dir . $files[0]) ){

            $handle_in = fopen($comparison_dir . $files[0], "r");

            if ($handle_in) {
                // Skip header row
                fgets($handle_in);

                while (($buffer = fgets($handle_in)) !== false) {
                    fwrite($handle_out, $files[4] . "\t" . $buffer);
                }
                fclose($handle_in);
            }
        }

    }
    fclose($handle_out);

    $command = "{$BXAF_CONFIG['TAIL_BIN']} -n +2 {$comparison_dir}{$combined_file_name} | {$BXAF_CONFIG['SORT_BIN']} -k4 -g -t\$'\\t' > {$comparison_dir}{$combined_sorted_file_name}";
    shell_exec($command);
}

if(! file_exists($comparison_dir . $combined_sorted_file_name)){
	header("Location: ../../../app/plot/search_comparison/single_comparison.php?type=comparison&id=$COMPARISON_INDEX");
}

$header = explode("\t", "GO Tree	TermID	Term	Enrichment	logP	Genes in Term	Target Genes in Term	Fraction of Targets in Term	Total Target Genes	Total Genes");
$contents = array();

$handle_in = fopen($comparison_dir . $combined_sorted_file_name, "r");


if ($handle_in) {
    $n = 0;
    while (($row = fgetcsv($handle_in, 0, "\t")) !== false) {
        $contents[$n] = array_slice($row, 0, 10);
        $n++;
        if($n >= 5000) break;
    }
    fclose($handle_in);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

    <link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.js'></script>

	<script type="text/javascript">
		$(document).ready(function(){

            var table = $('#myDataTable').DataTable({ 'pageLength': 10, 'lengthMenu': [[10, 100, 500, 1000], [10, 100, 500, 1000]], "order": [[3, 'asc']], dom: 'Blfrtip', buttons: [ 'colvis','csv','excel','pdf','print'] });

			$('.toggle-vis').on( 'click', function (e) {
		        var column = table.column( $(this).val() );
		        column.visible( ! column.visible() );
		    } );
		});

        $(document).on('click', '.content_detail',function(){
			var row_number = $(this).attr('row_number');

			$.ajax({
				url: '<?php echo $_SERVER['PHP_SELF']; ?>?action=get_gene_list&row_number=' + row_number + '&dir=<?php echo base64_encode($comparison_dir); ?>',
				success: function(responseText, statusText){
					bootbox.alert({
						message: responseText,
						callback: function(){}
					});
				}
			})
		})

	</script>

    <style>
        #myDataTable thead tr th{
            white-space: nowrap!important;
        }
    </style>

</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">



				<div class="container-fluid">
					<h1 class="w-100 my-4">Gene Ontology Enrichment Results</h1>

                    <?php

                        echo "<div class='w-100 my-3 text-warning'>Text file version of complete results (i.e. open with Excel) <a class='mx-5' href='Javascript: void(0);' onClick='if(\$(\"#all_file_list\").hasClass(\"hidden\")) \$(\"#all_file_list\").removeClass(\"hidden\"); else \$(\"#all_file_list\").addClass(\"hidden\"); '><i class='fas fa-arrow-right'></i> Show/Hide</a></div>";
                        echo "<ul id='all_file_list' class='my-3 hidden'>";
                        foreach($go_analysis_files as $files){

							if (file_exists($comparison_dir . $files[0])){
								if ($files[3] != ''){
		                            echo "<li><a href='" . $comparison_url . $files[0]. "' target='_blank'>" . $files[1]. "</a> " . $files[2]. " (<a href='" . $files[3]. "' target='_blank'>" . $files[4]. "</a>)</li>";
								} else {
									echo "<li><a href='" . $comparison_url . $files[0]. "' target='_blank'>" . $files[1]. "</a></li>";
								}
							}

                        }
                        echo "</ul>";

                        echo "<hr class='w-100' />";

                        echo "<h3 class='w-100 my-4'>Enriched Categories</h3>";


                        echo '<div class="my-3"><strong>Toggle columns:</strong>';
                            foreach($header as $i=>$col) echo '<input type="checkbox" checked class="toggle-vis mx-2" value="' . $i . '">' . $col;
                        echo '</div>';

                        echo "<table id='myDataTable' class='table table-hover'>";
                        echo "<thead><tr>";
                            foreach($header as $col) echo "<th>$col</th>";
                            echo "<th>Actions</th>";
                        echo "</tr></thead>";

                        echo "<tbody>";
                            foreach($contents as $n=>$row){
                                echo "<tr>";
                                foreach($row as $col) echo "<td>$col</td>";
                                echo "<td><a title='Show Genes' href='Javascript: void(0);' row_number='" . $n . "' class='content_detail'><i class='fas fa-list'></i>Show Genes</a></td>";
                                echo "</tr>";
                            }
                        echo "</tbody>";
                        echo "</table>";

                    ?>



				</div>



            </div>

		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>

		</div>

	</div>

</body>
</html>