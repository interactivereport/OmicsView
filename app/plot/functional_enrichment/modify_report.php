<?php



foreach (array('Up', 'Down') as $direction) {

	for ($index = 1; $index <= 2870; $index++) {


		$COMPARISON_INDEX = $index;

		$dir = $BXAF_CONFIG['GO_OUTPUT_HUMAN'] . '/comp_' . $COMPARISON_INDEX . '/comp_' . $COMPARISON_INDEX . '_GO_Analysis_' . $direction;

		$content = file_get_contents($dir . '/geneOntology.html');

		$pos_start = strpos($content, '<TD>Common Genes</TD></TR>');
		$pos_end = strpos($content, '</BODY></HTML>');



		$table_content = substr($content, $pos_start + 31, $pos_end - $pos_start - 46);
			$table_content_array = explode("</TR>\n<TR>", $table_content );

			// Get detail info
			$table_content_array_detail = array();
			foreach ($table_content_array as $key => $value){
				$temp = explode("</TD>", $value);
				foreach($temp as $k => $v){
					if(trim($v) == "") unset($temp[$k]);
					$temp[$k] = strip_tags(str_replace("/n", "", $v));
				}
				$table_content_array_detail[] = $temp;

			}

			$table_header = '<th>P-value</th><th>ln(P)</th>';
			$table_header .= '<th style="word-wrap: break-word; width:100px !important;">Term</th>';
			$table_header .= '<th>GO Tree</th><th style="width:100px">GO ID</th>';
			$table_header .= '<th># of Genes in Term</th>';
			$table_header .= '<th># of Target Genes in Term</th>';
			$table_header .= '<th># of Total Genes</th><th># of Target Genes</th>';
			$table_header .= '<th style="min-width:500px;">Common Genes</th>';


			$htmlCode = "
				<!DOCTYPE html>
				<html lang='en'>
				<head>
				<meta charset='UTF-8'>
				<title>Functional Enrichment</title>
				<link href='../../../../library/bootstrap/dist/css/bootstrap.min.css.php' rel='stylesheet' type='text/css'>
				<link href='../../../../library/font-awesome/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
				<link href='../../../../library/animate.css.php' rel='stylesheet' type='text/css'>
				<link href='../../../../library/tootik.min.css' rel='stylesheet' type='text/css'>";
			$htmlCode .= '
				<script src="../../../../library/jquery.min.1.12.4.js"></script>
				<script src="../../../../library/bootstrap/dist/js/bootstrap.min.js.php"></script>
				<script src="../../../../library/bootbox.min.js.php"></script>';
			$htmlCode .= "

				</head>
				<body>";

			$htmlCode .= '
				<div class="container-fluid">
					<h3 class="font-raleway m-t-1">
						Gene Ontology Enrichment Results &nbsp;&nbsp
						<a href="../../../../search_comparison/single_comparison.php?type=comparison&id=' . $COMPARISON_INDEX . '" style="font-size:1rem;"><i class="fa fa-angle-double-right"></i> Back to comparison detail</a>
					</h3>
					<hr>
					<h5 class="font-raleway m-b-sm">Text file version of complete results (i.e. open with Excel):</h5>
					<p><a href="javascript:void(0);" id="show_file_list"><i class="fa fa-angle-double-right"></i> Download text version of complete results for each category</a></p>
					<ul class="font-raleway gray" style="display:none;" id="file_list">
						<li><a href="biological_process.txt">biological process</a>: Functional groupings of proteins (<a href="http://www.geneontology.org">Gene Ontology</a>)</li>
						<li><a href="molecular_function.txt">molecular function</a>: Mechanistic actions of proteins (<a href="http://www.geneontology.org">Gene Ontology</a>)</li>
						<li><a href="cellular_component.txt">cellular component</a>: Protein localization (<a href="http://www.geneontology.org">Gene Ontology</a>)</li>
						<li><a href="chromosome.txt">chromosome location</a>: Genes with similar chromosome localization (<a href="http://www.ncbi.nlm.nih.gov/gene">NCBI Gene</a>)</li>
						<li><a href="kegg.txt">KEGG pathways</a>: Groups of proteins in the same pathways (<a href="http://www.genome.jp/kegg/pathway.html">KEGG</a>)</li>
						<li><a href="interactions.txt">protein interactions</a>: "Proteins interacting with a common protein (BIND, EcoCyc, HPRD)" (<a href="http://www.ncbi.nlm.nih.gov/gene">NCBI Gene</a>)</li>
						<li><a href="interpro.txt">interpro domains</a>: Proteins with similar domains and features (<a href="http://www.ebi.ac.uk/interpro/">Interpro</a>)</li>
						<li><a href="pfam.txt">pfam domains</a>: Proteins with similar domains and features (<a href="http://www.sanger.ac.uk/Software/Pfam/">Pfam</a>)</li>
						<li><a href="smart.txt">smart domains</a>: Proteins with similar domains and features (<a href="http://smart.embl-heidelberg.de/">SMART</a>)</li>
						<li><a href="gene3d.txt">gene3d domains</a>: Proteins with similar domains and features (<a href="http://cathwww.biochem.ucl.ac.uk:8080/Gene3D">Gene3D</a>)</li>
						<li><a href="prosite.txt">prosite domains</a>: Proteins with similar domains and features (<a href="http://ca.expasy.org/prosite/">Prosite</a>)</li>
						<li><a href="prints.txt">prints domains</a>: Proteins with similar domains and features (<a href="http://www.bioinf.manchester.ac.uk/dbbrowser/PRINTS/">PRINTS</a>)</li>
						<li><a href="msigdb.txt">MSigDB lists</a>: "Genes sets for pathways, factor/miRNA target predictions, expression patterns, etc." (<a href="http://www.broadinstitute.org/gsea/msigdb/index.jsp">MSigDB</a>)</li>
						<li><a href="biocyc.txt">BIOCYC pathways</a>: Groups of proteins in the same pathways (<a href="http://biocyc.org/">BIOCYC</a>)</li>
						<li><a href="cosmic.txt">COSMIC cancer mutations</a>: Genes mutated in similar cancers (<a href="http://cancer.sanger.ac.uk/cancergenome/projects/cosmic/">COSMIC</a>)</li>
						<li><a href="gwas.txt">GWAS genes</a>: Genes mutated in similar diseases (<a href="http://www.genome.gov/26525384">GWAS Catalog</a>)</li>
						<li><a href="lipidmaps.txt">Lipid Maps pathways</a>: Groups of proteins in the same lipid pathways (<a href="http://www.ncbi.nlm.nih.gov/biosystems/">Lipid Maps/Biosystems</a>)</li>
						<li><a href="pathwayInteractionDB.txt">Pathway Interaction DB</a>: Groups of proteins in the same pathways (<a href="http://pid.nci.nih.gov/">Pathway Interaction Database</a>)</li>
						<li><a href="reactome.txt">REACTOME pathways</a>: Groups of proteins in the same pathways (<a href="http://www.reactome.org/PathwayBrowser/">REACTOME</a>)</li>
						<li><a href="smpdb.txt">SMPDB pathways</a>: Groups of proteins in the same pathways (<a href="http://www.smpdb.ca/">SMPDB</a>)</li>
						<li><a href="wikipathways.txt">WikiPathways</a>: Groups of proteins in the same pathways (<a href="http://www.wikipathways.org/index.php/Special:BrowsePathwaysPage">Wikipathways</a>)</li>
					</ul>
					<br />
					<h3 class="font-raleway m-t-1">Enriched Categories</h3>
					<hr>
					<table class="table table-bordered" id="dataTable" style="max-width: 100%;">
						<thead style="max-width: 100%;">
							<tr class="table-info" style="max-width: 100%;">' . $table_header . '</tr>
						</thead>
						<tbody style="max-width: 100%;">';

							foreach($table_content_array_detail as $key => $value){
								$htmlCode .= '<tr>';
								for($i=0; $i<10; $i++){
									if($i == 4 && $value[3] == 'MSigDB lists'){
										$htmlCode .= '<td>&nbsp;</td>';
									} else if($i == 2) {
										$htmlCode .= '<td>' . str_replace('_', ' ', $value[$i]) . '</td>';
									} else if($i == 9) {
										$htmlCode .= '<td>' . str_replace(',', ', ', $value[$i]) . '</td>';
									}  else {
										$htmlCode .= '<td>' . $value[$i] . '</td>';
									}
								}
								$htmlCode .= '</tr>';
							}
				$htmlCode .= '
						</tbody>
					</table>
				</div>';

				$htmlCode .= "
				<script>
					$(document).ready(function(){
						//$('#dataTable').DataTable();
						$(document).on('click', '#show_file_list', function(){
							$('#file_list').toggle(500);
						});
					});
				</script>";

			$htmlCode .= '</body></html>';

			file_put_contents($dir . '/report.html', $htmlCode);

			echo 'File ' . $COMPARISON_INDEX . ' generated: ' . $dir . '/report.html' . '<br />';

	}

}

?>
