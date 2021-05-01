<?php
include_once(__DIR__ . "/config/config.php");

ignore_user_abort(true);
set_time_limit(0);


foreach($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES'] as $name=>$file){
	if(! file_exists($file)){
		echo "Missing file: $file <BR>";
	}
}

// Check tables
$sql = "SHOW TABLES";
$all_tables = $BXAF_MODULE_CONN->get_col($sql);

$table_exists = array();
foreach($BXAF_CONFIG['BXGENOMICS_DB_TABLES'] as $key=>$table){
	if($table == ''){
		unset($BXAF_CONFIG['BXGENOMICS_DB_TABLES'][$key]);
		continue;
	}
	if(in_array($table, $all_tables)) $table_exists[$key] = 1;
	else {
		$table_exists[$key] = 0;
		echo "Missing table: $table<BR>";
	}
}




if($table_exists['TBL_BXGENOMICS_GENES'] == 0){
	echo "<BR><strong>Please add this table to continue: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES'];
	exit();
}
if($table_exists['TBL_BXGENOMICS_GENE_LOOKUP'] == 0 ){
	echo "<BR><strong>Please add this table to continue: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENE_LOOKUP'];
	exit();
}





if($table_exists['TBL_BXGENOMICS_GENES_INDEX'] == 0 ){

	$sql = "CREATE TABLE IF NOT EXISTS `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES_INDEX'] . "` (
			`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`Species` varchar(255) NOT NULL DEFAULT '',
			`Name` varchar(255) NOT NULL DEFAULT '',
			`GeneIndex` int(11) unsigned NOT NULL DEFAULT '0',
			`GeneName` varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (`ID`),
			KEY `Name` (`Name`),
			KEY `GeneIndex` (`GeneIndex`),
			KEY `GeneName` (`GeneName`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
	";
	$BXAF_MODULE_CONN->execute($sql);


	$sql = "INSERT INTO `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES_INDEX'] . "` (`GeneIndex`, `Name`) SELECT `GeneIndex`, `ID` FROM `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENE_LOOKUP'] . "`";
	$BXAF_MODULE_CONN->execute($sql);



	$sql = "SELECT `GeneIndex`, `GeneName` FROM `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES'] . "`";
	$geneindex_genenames = $BXAF_MODULE_CONN->get_assoc('GeneIndex', $sql);

	$sql = "UPDATE `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES_INDEX'] . "` SET `Species` = '" . $BXAF_CONFIG['SPECIES'] . "', `GeneName` = ?s WHERE `GeneIndex` = ?i";
	foreach($geneindex_genenames as $geneindex=>$genename){
		$BXAF_MODULE_CONN->execute($sql, $genename, $geneindex);
	}

}




if($table_exists['TBL_WIKIPATHWAYS_INFO'] == 0 ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/command_build_tbl_wikipathways_info.php' to build table: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_WIKIPATHWAYS_INFO'];
	exit();
}

$sql = "DELETE FROM `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_WIKIPATHWAYS_INFO'] . "` WHERE `Species` != '" . $BXAF_CONFIG['SPECIES'] . "'";
$BXAF_MODULE_CONN->execute($sql);

$sql = "SELECT COUNT(*) FROM `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_WIKIPATHWAYS_INFO'] . "` WHERE `Gene_Name` != ''";
$count = $BXAF_MODULE_CONN->get_one($sql);

if($count == 0){

	$sql = "SELECT `ID`, `DB_ID` FROM `tbl_wikipathways_info` WHERE `Type` IN ('Protein', 'GeneProduct', 'Rna') AND `DB_Name` IN ('Entrez Gene', 'Uniprot-TrEMBL', 'Ensembl')";
	$results = $BXAF_MODULE_CONN->get_assoc('ID', $sql );

	$sql2 = "UPDATE `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_WIKIPATHWAYS_INFO'] . "` SET `Gene_Index` = ?i, `Gene_Name` = ?s WHERE `ID` = ?i";

	foreach($results as $id=>$DB_ID){

	    $sql = "SELECT `GeneIndex`, `GeneName` FROM `" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_BXGENOMICS_GENES_INDEX'] . "` WHERE `Name` = ?s";
	    $row = $BXAF_MODULE_CONN->get_row( $sql, $DB_ID);

	    if(is_array($row) && count($row) > 0){
			$BXAF_MODULE_CONN->execute($sql2, $row['GeneIndex'], $row['GeneName'], $id);
	    }
	}

}




if($table_exists['TBL_GO_GENE_LIST'] == 0 ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/command_build_tbl_go_gene_list.php' to build table: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_GO_GENE_LIST'];
	exit();
}


if($table_exists['TBL_PAGE_GENESETS'] == 0 ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/command_build_tbl_page_genesets.php' to build table: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_PAGE_GENESETS'];
	exit();
}

if($table_exists['TBL_COMPARISON_GO_ENRICHMENT'] == 0 ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/build_tbl_comparison_go_enrichment.php' to build table: </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_COMPARISON_GO_ENRICHMENT'];
	exit();
}


if($table_exists['TBL_COMPARISON_GO_ENRICHMENT_10_2'] == 0 || $table_exists['TBL_COMPARISON_GO_ENRICHMENT_10_6'] == 0 || $table_exists['TBL_COMPARISON_GO_ENRICHMENT_10_10'] == 0 ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/command_tbl_comparison_go_enrichment_fdr.php' to build tables </strong>" . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_COMPARISON_GO_ENRICHMENT_10_2'] . ", " . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_COMPARISON_GO_ENRICHMENT_10_6'] . ", " . $BXAF_CONFIG['BXGENOMICS_DB_TABLES']['TBL_COMPARISON_GO_ENRICHMENT_10_10'] . "";

	echo "<BR><strong>Also run command '.../app/bxgenomics/config/command_build_tbl_comparison_go_enrichment_internal.php' to build tables </strong>";

	exit();
}



if(! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['comparison_page_all_msigdb.txt']) ){
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/command_build_tabix_comparison_page_all_msigdb.php' to create file: </strong>" .  $BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['comparison_page_all_msigdb.txt'];
	exit();
}


if(	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_001_neg.txt']) ||
	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_001_pos.txt']) ||
	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_005_neg.txt']) ||
	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_005_pos.txt']) ||
	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_025_neg.txt']) ||
	! file_exists($BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['list_025_pos.txt'])
	)
{
	echo "<BR><strong>Please run command '.../app/bxgenomics/config/build_page_out.php' to create files: </strong>" .  $BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES']['comparison_page_all_msigdb.txt'];
	exit();
}


/*
CREATE TABLE `tbl_comparison_go_enrichment` (
  `ID` int(11) NOT NULL,
  `Comparison_Index` int(11) NOT NULL DEFAULT '0',
  `Comparison_Name` varchar(255) NOT NULL DEFAULT '',
  `Direction` varchar(15) NOT NULL DEFAULT '',
  `Enrichment` varchar(15) NOT NULL DEFAULT '',
  `logP` varchar(15) NOT NULL DEFAULT '',
  `Term` varchar(255) NOT NULL DEFAULT '',
  `GO_Tree` varchar(255) NOT NULL DEFAULT '',
  `TermID` varchar(255) NOT NULL DEFAULT '',
  `Genes_in_Term` varchar(15) NOT NULL DEFAULT '',
  `Target_Genes_in_Term` varchar(15) NOT NULL DEFAULT '',
  `Total_Genes` varchar(15) NOT NULL DEFAULT '',
  `Total_Target_Genes` varchar(15) NOT NULL DEFAULT '',
  `Gene_Symbols` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_comparison_go_enrichment_10_2` (
  `ID` int(11) NOT NULL,
  `Comparison_Index` int(11) NOT NULL DEFAULT '0',
  `Comparison_Name` varchar(255) NOT NULL DEFAULT '',
  `Direction` varchar(15) NOT NULL DEFAULT '',
  `GO_Tree` varchar(255) NOT NULL DEFAULT '',
  `Terms` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_comparison_go_enrichment_10_6` (
  `ID` int(11) NOT NULL,
  `Comparison_Index` int(11) NOT NULL DEFAULT '0',
  `Comparison_Name` varchar(255) NOT NULL DEFAULT '',
  `Direction` varchar(15) NOT NULL DEFAULT '',
  `GO_Tree` varchar(255) NOT NULL DEFAULT '',
  `Terms` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_comparison_go_enrichment_10_10` (
  `ID` int(11) NOT NULL,
  `Comparison_Index` int(11) NOT NULL DEFAULT '0',
  `Comparison_Name` varchar(255) NOT NULL DEFAULT '',
  `Direction` varchar(15) NOT NULL DEFAULT '',
  `GO_Tree` varchar(255) NOT NULL DEFAULT '',
  `Terms` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_go_gene_list` (
  `ID` int(11) NOT NULL,
  `Species` varchar(255) NOT NULL DEFAULT '',
  `File` varchar(255) NOT NULL DEFAULT '',
  `Category` varchar(255) NOT NULL DEFAULT '',
  `Code` varchar(255) NOT NULL DEFAULT '',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `Gene_Counts` int(11) NOT NULL DEFAULT '0',
  `Gene_Names` longtext NOT NULL,
  `Gene_IDs` longtext NOT NULL,
  `bxafStatus` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbl_page_genesets` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `URL` varchar(255) NOT NULL DEFAULT '',
  `Gene_Counts` int(11) NOT NULL DEFAULT '0',
  `Gene_IDs` longtext NOT NULL,
  `Gene_Names` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `TBL_BXGENOMICS_GENES_INDEX`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Species` (`Species`),
  ADD KEY `Name` (`Name`),
  ADD KEY `_Genes_ID` (`GeneIndex`);

ALTER TABLE `tbl_comparison_go_enrichment`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Comparison_Index` (`Comparison_Index`),
  ADD KEY `Comparison_Name` (`Comparison_Name`),
  ADD KEY `GO_Tree` (`GO_Tree`),
  ADD KEY `Term` (`Term`);

ALTER TABLE `tbl_comparison_go_enrichment_10_2`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Comparison_ID` (`Comparison_Index`),
  ADD KEY `Comparison_Name` (`Comparison_Name`),
  ADD KEY `GO_Tree` (`GO_Tree`);

ALTER TABLE `tbl_comparison_go_enrichment_10_6`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Comparison_Index` (`Comparison_Index`),
  ADD KEY `Comparison_Name` (`Comparison_Name`),
  ADD KEY `GO_Tree` (`GO_Tree`);

ALTER TABLE `tbl_comparison_go_enrichment_10_10`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Comparison_Index` (`Comparison_Index`),
  ADD KEY `Comparison_Name` (`Comparison_Name`),
  ADD KEY `GO_Tree` (`GO_Tree`);

ALTER TABLE `tbl_go_gene_list`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Category` (`Category`),
  ADD KEY `Code` (`Code`),
  ADD KEY `Name` (`Name`);

ALTER TABLE `tbl_page_genesets`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`) USING BTREE;


ALTER TABLE `TBL_BXGENOMICS_GENES_INDEX`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4108476;

ALTER TABLE `tbl_comparison_go_enrichment`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50746487;

ALTER TABLE `tbl_comparison_go_enrichment_10_2`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51845;

ALTER TABLE `tbl_comparison_go_enrichment_10_6`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19510;

ALTER TABLE `tbl_comparison_go_enrichment_10_10`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11226;

ALTER TABLE `tbl_go_gene_list`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174299;

ALTER TABLE `tbl_page_genesets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23971;

*/

// echo "$sql<pre>" . print_r($all_tables, true) . "</pre>";

// echo "<BR><BR>Finished.";

?>