<?php


function addMetaAnalysis($input){
	
	global $APP_CONFIG;
	
	unset($dataArray);
	
	$dataArray['Date']	 			= date('Y-m-d');
	$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
	$dataArray['User_ID'] 			= $APP_CONFIG['User_Info']['ID'];
	
	$dataArray['Name'] 				= trim($input['Name']);
	$dataArray['Analysis_Type'] 	= trim($input['Analysis_Type']);
	
	$dataArray['Status'] 			= trim($input['Status']);
	$dataArray['Parameters'] 		= json_encode($input['Parameters']);
		
	
	$SQL_TABLE = $APP_CONFIG['Table']['App_Meta_Analysis'];
	$SQL = getInsertSQLQuery($SQL_TABLE, $dataArray);
	
	$sql_exe_results = execSQL($SQL);
	
	if (!$sql_exe_results){
		$results['Error'] 	= 1;
		$results['Message'] = "Database error. Please contact us for details.";
	} else {
		$results['Error'] = 0;
		$results['ID']		= getLastInsertID();
	}
	
	return $results;
	
}

function getAllMetaAnalyses(){

	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_Meta_Analysis'];
	
	$userID			= intval($_SESSION['User_Info']['ID']);
	
	if (general_is_admin_user()){
		$SQL = "SELECT `ID`, `Date`, `User_ID`, `Name`, `Status` FROM `{$SQL_TABLE}` ORDER BY `ID` DESC";
	} else {
		$SQL = "SELECT `ID`, `Date`, `User_ID`, `Name`, `Status` FROM `{$SQL_TABLE}` WHERE (`User_ID` = {$userID}) ORDER BY `ID` DESC";
	}
	$results = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 0, 0);
	
	foreach($results as $currentID => $currentRecord){
		$results[$currentID] = processMetaAnalysis($currentRecord);
		$results[$currentID]['ID'] = $currentID;
	}
	
	return $results;
	
}

function processMetaAnalysis($dataArray){
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	
	if (!$dataArray['Processed']){
		$dataArray['Processed'] = 1;
		
		$dataArray['isOwner'] 	= 0;
		$dataArray['canUpdate']	= 0;
		
		if ($dataArray['User_ID'] == $user_id){
			$dataArray['isOwner'] 	= 1;
			$dataArray['canUpdate']	= 1;
		} elseif (general_is_admin_user()){
			$dataArray['canUpdate']	= 1;
		}
		
		$dataArray['User'] = general_get_user_info($dataArray['User_ID']);
		
		
		if (isset($dataArray['Parameters'])){
			$dataArray['Parameters'] = json_decode($dataArray['Parameters'], true);
		}
	}
	
	return $dataArray;
	
}


function getMetaAnalysis2($ID = 0){

	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_Meta_Analysis'];
	$ID 		= intval($ID);
	
	if ($ID <= 0) return false;
	
	$userID		= intval($_SESSION['User_Info']['ID']);
	
	if (general_is_admin_user()){
		$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE (`ID` = {$ID})";		
	} else {
		$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE (`User_ID` = {$userID}) AND (`ID` = {$ID})";	
	}
	
	$dataArray = getSQL($SQL, 'GetRow', $SQL_TABLE, 0, 0);
	
	if ($dataArray['ID'] > 0){
		$dataArray = processMetaAnalysis($dataArray);
	}
	
	return $dataArray;
	
}

function completeMetaAnalysisStatus2($ID = 0){

	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_Meta_Analysis'];
	$ID 		= intval($ID);
	
	if ($ID <= 0) return false;
	
	$SQL = "UPDATE `{$SQL_TABLE}` SET `Status` = 'Finished' WHERE `ID` = {$ID}";
	execSQL($SQL);
	
	return $dataArray;
	
}


function prepareMetaAnalysisData2($comparisonInput, $dataSource = array(), $internalProjectIndexes = array(), $otherOptions = array()){
	
	global $APP_CONFIG, $BXAF_CONFIG;

	$geneIndexes 			= array_clean($geneIndexes);	
	$comparisonIndexes 		= array_clean($comparisonIndexes);
	$genePlotColumns 		= array_clean($genePlotColumns);
	
	$version  = '2018-08-02 12:27';
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($comparisonInput) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode($otherOptions) . '::' . 
										$version);
										
	$dirKey		= str_replace('::', '_', $cacheKey);										
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}

	if (true){
		$path			 = "{$BXAF_CONFIG['WORK_DIR']}META_ANALYSIS2/{$BXAF_CONFIG['APP_PROFILE']}/{$dirKey}/";
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		$results['Summary']['Path'] = $path;
		
		$results['Input'] = $comparisonInput;
	}
	
	//Comparison_List.csv
	$allSampleIDs = '';
	if (true){
		$inputFilePath = "{$path}Comparison_List.csv";
		
		$results['Summary']['Comparison_List.csv'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		
		$dataArray   = array();
		$dataArray['Header'] = array("Comp_Number", "Comp_Name", "Case.SampleIDs", "Control.SampleIDs");
		fputcsv($fp, $dataArray['Header']);
	
		
		foreach($comparisonInput as $i => $currentComparison){
			
			$dataArray['Body'][$i]['Comp_Number'] 		= $i;
			$dataArray['Body'][$i]['Comp_Name'] 		= $currentComparison['Name'];
			$dataArray['Body'][$i]['Case.SampleIDs'] 	= implode(';', $currentComparison['Case']);
			$dataArray['Body'][$i]['Control.SampleIDs'] = implode(';', $currentComparison['Control']);
			
			foreach($currentComparison['Case'] as $tempKeyX => $tempValue){
				$allSampleIDs[] = $tempValue;
			}
			
			foreach($currentComparison['Control'] as $tempKeyX => $tempValue){
				$allSampleIDs[] = $tempValue;
			}
			
			fputcsv($fp, $dataArray['Body'][$i]);
		}
		
		fclose($fp);
		
		unset($dataArray);
	}
	
	//Tabix
	if (true){
		$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($allSampleIDs, '', $internalProjectIndexes);
		
		$sampleIDs['Microarray'] 	= $getSampleIDsExistenceInfo['byPlatformType']['Microarray'];
		$sampleIDs['RNA-Seq'] 		= $getSampleIDsExistenceInfo['byPlatformType']['RNA-Seq'];
		
		if (isset($getSampleIDsExistenceInfo['byPlatformType']['Microarray'])){
			$sampleData['Microarray'] 	= tabix_search_records_with_index_all('', 
												array_keys($getSampleIDsExistenceInfo['byPlatformType']['Microarray']), 
												'GeneLevelExpression', 
												'GetArrayAssoc');
		}
		
		
		if (isset($getSampleIDsExistenceInfo['byPlatformType']['RNA-Seq'])){
			$sampleData['RNA-Seq'] 		= tabix_search_records_with_index_all('', 
												array_keys($getSampleIDsExistenceInfo['byPlatformType']['RNA-Seq']), 
												'GeneFPKM', 
												'GetArrayAssoc');
		}
	}
	

	
	//Expression_matrix.csv
	if (true){
		$sampleDataMatrix   = array();
		foreach($sampleData['RNA-Seq'] as $tempKey => $currentData){
			$currentGeneIndex 	= $currentData['GeneIndex'];
			$currentSampleIndex = $currentData['SampleIndex'];
			$sampleDataMatrix[$currentGeneIndex][$currentSampleIndex] = $currentData['FPKM'];
		}
		
		
		foreach($sampleData['Microarray'] as $tempKey => $currentData){
			$currentGeneIndex 	= $currentData['GeneIndex'];
			$currentSampleIndex = $currentData['SampleIndex'];
			$sampleDataMatrix[$currentGeneIndex][$currentSampleIndex] = $currentData['Value'];
		}
	
		
		
		if (true){
			$inputFilePath = "{$path}Expression_matrix.csv";
			$results['Summary']['Expression_matrix.csv'] = $inputFilePath;
			
			$fp = fopen($inputFilePath, 'w');
			
			$dataArray   = array();
			$dataArray['Header']['First'] = '';
			foreach($getSampleIDsExistenceInfo['SampleIndexes'] as $tempKey => $currentSampleIndex){
				$dataArray['Header'][$currentSampleIndex] = $currentSampleIndex;
			}
			
			fputcsv($fp, $dataArray['Header']);
			
			foreach($sampleDataMatrix as $currentGeneIndex => $currentcSampleInfo){
				
				$currentRow = array($currentGeneIndex);
				
				foreach($getSampleIDsExistenceInfo['SampleIndexes'] as $tempKey => $currentSampleIndex){
					
					if (isset($sampleDataMatrix[$currentGeneIndex][$currentSampleIndex])){
						$value = $sampleDataMatrix[$currentGeneIndex][$currentSampleIndex];
					} else {
						$value = 'NA';	
					}
					
					$currentRow[] = $value;
				}

				fputcsv($fp, $currentRow);
				
			}
			
			fclose($fp);
		
			unset($dataArray);

		}
	}
	
	
	//Sample_table.csv
	if (true){
		
		$inputFilePath = "{$path}Sample_table.csv";
		
		$results['Summary']['Sample_table.csv'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		$currentRow = array('SampleIndex', 'SampleID', 'Type', 'Comp_Name', 'Comp_Number');
		fputcsv($fp, $currentRow);
		
		foreach($comparisonInput as $i => $currentComparison){

			foreach($currentComparison['Case'] as $tempKeyX => $currentSampleID){
				
				$currentRow = array();
				$currentSampleID_lower 	= strtolower($currentSampleID);

				if (!isset($getSampleIDsExistenceInfo['reversed-strtolower'][$currentSampleID_lower])){
					continue;
				} else {
					$currentSampleIndex = $getSampleIDsExistenceInfo['reversed-strtolower'][$currentSampleID_lower];
				}
				
				$currentRow['SampleIndex']	= $currentSampleIndex;
				$currentRow['SampleID']		= $getSampleIDsExistenceInfo['Index-ID'][$currentSampleIndex];
				$currentRow['Type']			= 1;
				$currentRow['Comp_Name']	= $currentComparison['Name'];
				$currentRow['Comp_Number']	= $i;
				
				fputcsv($fp, $currentRow);
			}
			
			foreach($currentComparison['Control'] as $tempKeyX => $currentSampleID){
				
				$currentRow = array();
				$currentSampleID_lower 	= strtolower($currentSampleID);

				if (!isset($getSampleIDsExistenceInfo['reversed-strtolower'][$currentSampleID_lower])){
					continue;
				} else {
					$currentSampleIndex = $getSampleIDsExistenceInfo['reversed-strtolower'][$currentSampleID_lower];
				}
				
				$currentRow['SampleIndex']	= $currentSampleIndex;
				$currentRow['SampleID']		= $getSampleIDsExistenceInfo['Index-ID'][$currentSampleIndex];
				$currentRow['Type']			= 0;
				$currentRow['Comp_Name']	= $currentComparison['Name'];
				$currentRow['Comp_Number']	= $i;
				
				fputcsv($fp, $currentRow);
			}
			
			
		}
		
		
		
		fclose($fp);
		
	}
	
	
	$results['Summary']['Gene_Annoation.csv'] = $APP_CONFIG['Meta_Analysis2']['Gene_Annotation'];

	//command.R
	if (true){
		$R_Script = 
'options(stringsAsFactors=F)
gene_annot_file="{Gene_Annoation.csv}"
Run_Rankprod={Run_Rankprod}
library(data.table); library(tidyverse);library(limma)
library(MetaDE); library(RankProd)
exp_data=fread("{Expression_matrix.csv}", header=T)
geneIndex=exp_data$V1
exp_data=data.matrix(exp_data[, 2:ncol(exp_data)])
rownames(exp_data)=geneIndex
gene_annot=fread(gene_annot_file)

sample_info=read_csv("{Sample_table.csv}")

#Run limma first

limma_tt<-function(mset, types) {
	design <- model.matrix(~ 0+as.factor(types) )
	colnames(design)=c("Ctrl", "Case")
	fit <- lmFit(mset, design)
	contrast.matrix<-makeContrasts(Case_vs_Ctrl=Case-Ctrl, levels=design)
	fit2 <- contrasts.fit(fit, contrast.matrix) 
	fit2 <- eBayes(fit2)
	tt=topTable(fit2, n=nrow(mset), confint=T)
	SE <- sqrt(fit2$s2.post) * fit2$stdev.unscaled
	GeneIndex=rownames(tt)
	tt=cbind(GeneIndex, tt, SE=SE[rownames(tt), 1])
}

comp_list=unique(sample_info$Comp_Number)
n=0
sel=match(as.character(colnames(exp_data)), as.character(sample_info$SampleIndex) )
if (sum(is.na(sel))>0) {stop("Names from expression data do not match SampleIndex from sample info file!\n")}
exp_Comp=sample_info$Comp_Number[sel]
exp_Type=sample_info$Type[sel]



for (comp in comp_list) {
	n=n+1
	sel1=which(exp_Comp==comp)
	mset=log2(exp_data[, sel1]+0.5)  #take log2 values
	types=exp_Type[sel1]
	tt=limma_tt(mset, types)
	tt1=tt[rownames(exp_data), ]%>%select(logFC, SE, CI.L, CI.R, P.Value, adj.P.Val)
	names(tt1)=str_c(names(tt1), "_", comp)
	if (n==1) {ES_input=tt1} else{ES_input=cbind(ES_input, tt1) }
	tt=tt[!is.na(tt$logFC), ]
	tt$GeneIndex=as.numeric(tt$GeneIndex)
	tt<-tt%>%left_join(gene_annot)
	out_file=str_c("Limma_out_comp_", comp, ".csv")
	fwrite(tt, out_file)
}
	

ES_input_new=list(ES=ES_input%>%select(contains("logFC_")), Var=ES_input%>%select(contains("SE_")) )
meta_ES=MetaDE.ES(ES_input_new, meta.method="REM")
meta_ES_out=data.frame(ES_pval=meta_ES$pval, ES_FDR=meta_ES$FDR, GeneIndex=as.numeric(rownames(exp_data)) ); #ES_FDR was not honored, REM is header
colnames(meta_ES_out)[2]="ES_FDR"
meta_ES_out=cbind(meta_ES_out,ES_input)
#get a weighted logFC, weight is 1/SE
weight=1/( ES_input%>%select(contains("SE_")) )
ES=ES_input%>%select(contains("logFC_"))
meta_ES_out<-meta_ES_out%>%mutate(logFC_Ave=rowSums(ES*weight, na.rm=T)/rowSums(weight, na.rm=T) )

meta_ES_out<-meta_ES_out%>%left_join(gene_annot)
meta_ES_out<-meta_ES_out%>%select(GeneIndex, Symbol, Description, ES_pval, ES_FDR, logFC_Ave, logFC_1:EntrezID)
write.csv(meta_ES_out, "MetaDE.ES_Output.csv", row.names=F)


#Make some stats
exp_data_clean=exp_data[rowSums(is.na(exp_data))==0, ]; 
#dim(exp_data_clean) #9560 for review only
#make some stats
GeneCount=data.frame(SampleIndex=as.numeric(colnames(exp_data)), N_Gene=colSums(!is.na(exp_data)), N_NAs=colSums(is.na(exp_data)) )
sample_info<-sample_info%>%left_join(GeneCount)
comp_sample_count<-sample_info%>%group_by(Comp_Number)%>%
	summarize(Case_Samples=n_distinct(SampleID[Type==1]), Control_Samples=n_distinct(SampleID[Type==0]) )
comp_gene_count<-sample_info%>%group_by(Comp_Name, Comp_Number)%>%summarize(Ave_N_Gene=mean(N_Gene), Ave_N_NAs=mean(N_NAs) )%>%
	arrange(Comp_Number)%>%left_join(comp_sample_count)
	write.csv(sample_info, "Sample_geneCount.csv", row.names=F)
write.csv(comp_gene_count, "Comparison_geneCount.csv", row.names=F)
cat("All_genes\t", nrow(exp_data), "\nGenes_no_NAs\t", nrow(exp_data_clean), "\n", sep="", file="NGene_info.txt")

##Now run RP analysis
if (Run_Rankprod) {
	RP.adv.out <- RP.advance(exp_data_clean, exp_Type, exp_Comp, logged=F,
	 gene.names=rownames(exp_data_clean),rand=123,  na.rm=T,  MinNumOfValidPairs=2)
	pdf("RP_plot.pdf")
	plotRP(RP.adv.out, cutoff=0.05)
	dev.off()
	#topGene(RP.adv.out,cutoff=0.001,method="pfp",logged=T,gene.names=rownames(exp_data_clean))
	outdata=topGene(RP.adv.out,num.gene=nrow(exp_data_clean), method="pfp",logged=F, gene.names=rownames(exp_data_clean) )
	RP1=outdata[[1]]; RP2=outdata[[2]]
	colnames(RP1)=str_c("Up_", c("gindex", "RP", "FC", "PFP", "P.Val") ) 
	colnames(RP2)=str_c("Down_", c("gindex", "RP", "FC", "PFP", "P.Val")  )
	RP<-data.frame(RP1)%>%mutate(GeneIndex=rownames(RP1)) %>%left_join(data.frame(RP2), by=c("Up_gindex"="Down_gindex") )
	RP<-RP%>%mutate(logFC_RP=log2(1/Up_FC), P.Val_RP=ifelse(logFC_RP>0, Up_P.Val, Down_P.Val),
	 FDR_RP=ifelse(logFC_RP>0, Up_PFP, Down_PFP), RankProd=ifelse(logFC_RP>0, Up_RP, Down_RP))
	RP_ES<- RP %>%select(GeneIndex,logFC_RP:RankProd) %>%mutate(GeneIndex=as.numeric(GeneIndex) )
	RP_ES<- left_join(meta_ES_out, RP_ES)%>%select(GeneIndex, Symbol, Description, logFC_RP:RankProd, ES_pval:EntrezID)
	write.csv(RP_ES, "RP_ES_data.csv", row.names=F)
}
';
		
		
		$R_Script = str_replace('{Gene_Annoation.csv}', $results['Summary']['Gene_Annoation.csv'], $R_Script);
		$R_Script = str_replace('{Expression_matrix.csv}', $results['Summary']['Expression_matrix.csv'], $R_Script);
		$R_Script = str_replace('{Sample_table.csv}', $results['Summary']['Sample_table.csv'], $R_Script);
		
		if ($otherOptions['rank_product_analysis']){
			$R_Script = str_replace('{Run_Rankprod}', 'TRUE', $R_Script);
		} else {
			$R_Script = str_replace('{Run_Rankprod}', 'FALSE', $R_Script);
		}
		
		
		
		$inputFilePath = "{$path}command.R";
		
		$results['Summary']['command.R'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		fwrite($fp, $R_Script);
		
		
		fclose($fp);
		
	}
	
	//run.sh
	if (true){
		$results['Summary']['R-Command'] = "{$BXAF_CONFIG['RSCRIPT_BIN']} {$results['Summary']['command.R']}";
		$results['Summary']['R-Command_Output'] = "{$path}run.log";
		$results['Summary']['Status.log'] = "{$path}Status.log";
		
		file_put_contents($results['Summary']['Status.log'], 'Prepared');
		
		$results['Summary']['Bash'] = "{$path}run.sh";
		
		$cmds = array();
		$cmds[] = "cd {$path}";
		$cmds[] = "rm Limma_out_comp_*.csv";
		$cmds[] = "rm MetaDE.ES_Output.csv";
		$cmds[] = "rm NGene_info.txt";
		$cmds[] = "rm Sample_geneCount.csv";
		$cmds[] = "echo 'Running' > Status.log";
		$cmds[] = "{$results['Summary']['R-Command']} &> {$results['Summary']['R-Command_Output']}";
		$cmds[] = "echo 'Finished' > Status.log";
		$bash 	= implode("\n", $cmds) . "\n";
		
		file_put_contents($results['Summary']['Bash'], $bash);
		chmod($results['Summary']['Bash'], 0755);
	}
	

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}

function processMetaAnalysis2($urlKey, $dataArray, $ID){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	$dir = $dataArray['prepareMetaAnalysisData2']['Summary']['Path'];
	
	if ($dir == ''){
		return $dataArray;
	}
	
	if (!is_dir($dir)){
		return $dataArray;	
	}
	
	
	$version  = '2019-04-05 18:13';

	$cacheKey = __FUNCTION__ . '::' . md5(
										$urlKey . '::' . 
										json_encode($dataArray) . '::' . 
										$version);
										
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	
	$dataArray['POST']['User']		= getUserInfo($dataArray['POST']['User_ID']);
	
	$status = trim(file_get_contents($dataArray['prepareMetaAnalysisData2']['Summary']['Status.log']));
	
	if ($status == 'Prepared'){
		$cmd = "nohup {$dataArray['prepareMetaAnalysisData2']['Summary']['Bash']} >/dev/null 2>&1 &";
		shell_exec($cmd);
		
		$dataArray['prepareMetaAnalysisData2']['Status'] = 'Running';
		$dataArray['POST']['Status_HTML'] = getInternalDataJobStatusHTML(2);
		
	} elseif ($status == 'Running') {
		$dataArray['prepareMetaAnalysisData2']['Status'] = 'Running';
		$dataArray['POST']['Status_HTML'] = getInternalDataJobStatusHTML(2);
		
	} elseif ($status == 'Finished') {
		
		$dataArray['prepareMetaAnalysisData2']['Status'] = 'Finished';
		$dataArray['POST']['Status_HTML'] = getInternalDataJobStatusHTML(1);
		
		
		
		if (is_file($dir . 'Comparison_geneCount.csv')){
			$dataArray['prepareMetaAnalysisData2']['Summary']['Comparison_geneCount.csv'] = $dir . 'Comparison_geneCount.csv';
		}
		
		if (is_file($dir . 'Comparison_List.csv')){
			$dataArray['prepareMetaAnalysisData2']['Summary']['Comparison_List.csv'] = $dir . 'Comparison_List.csv';
		}
		
		if (is_file($dir . 'Sample_geneCount.csv')){
			$dataArray['prepareMetaAnalysisData2']['Summary']['Sample_geneCount.csv'] = $dir . 'Sample_geneCount.csv';
		}
		
		if (is_file($dir . 'NGene_info.txt')){
			$dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info.txt'] = $dir . 'NGene_info.txt';
			
			$temp = file_get_contents($dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info.txt']);
			
			$temp = array_clean(explode("\n", $temp));
			
			foreach($temp as $tempKey => $tempValue){
				$temp[$tempKey] = array_clean(explode("\t", $tempValue));	
			}
			
			
			$dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info']['All_genes'] = $temp[0][1];
			$dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info']['Genes_no_NAs'] = $temp[1][1];
		}
		
		
		unset($resultFileKey);
		if ((!$dataArray['POST']['rank_product_analysis']) && (is_file($dir . 'MetaDE.ES_Output.csv'))){
			$resultFileKey = 'MetaDE.ES_Output.csv';
		}
		
		if (($dataArray['POST']['rank_product_analysis']) && (is_file($dir . 'RP_ES_data.csv'))){
			$resultFileKey = 'RP_ES_data.csv';
		}
		
		if ($resultFileKey != ''){

			$dataArray['prepareMetaAnalysisData2']['Summary'][$resultFileKey] = $dir . $resultFileKey;

			$rawData = readFirstFewLinesFromFile($dataArray['prepareMetaAnalysisData2']['Summary'][$resultFileKey], 0, 1, 'csv');
			
			$dataHTML = array();
			$dataHTML['Headers']['Checkbox'] 	= "<div class='text-center'><input type='checkbox' class='selectAllTrigger'/></div>";
			$dataHTML['Headers']['Checkbox'] 	= "&nbsp;";
			$dataHTML['Headers']['Actions'] 	= 'Actions';
			
			$sortID = 0;
			$currentCount = 0;
			
			foreach($rawData['Header'] as $tempKey => $currentHeader){
				if ($currentHeader == 'GeneIndex') continue;
				
				$dataHTML['Headers'][$currentHeader] = $currentHeader;
			}
			
			foreach($rawData['Body'] as $currentCount => $currentRecord){
				
				
				unset($currentActions);
				$tempURL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$currentRecord['GeneIndex']}";
				$currentActions[] 	= "<a title='Review Details' href='{$tempURL}'>" . printFontAwesomeIcon('fas fa-list') . "</a>";
				
				if (0 && $APP_CONFIG['APP']['Module']['RNA-Seq']){
					$tempURL = "app_gene_expression_rnaseq_single.php?GeneName={$currentRecord['Symbol']}";
					$currentActions[] 	= "<a title='Gene Expressions from RNA-Seq'	href='{$tempURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-chart-pie') . "</a>";
				}
				
				if (0 && $APP_CONFIG['APP']['Module']['Microarray']){
					$tempURL = "app_gene_expression_microarray_single.php?GeneName={$currentRecord['Symbol']}";
					$currentActions[] 	= "<a title='Gene Expressions from Microarray'	href='{$tempURL}' target='_blank'>" . printFontAwesomeIcon('far fa-chart-bar') . "</a>";
				}
				
				
				if (true){
					$tempURL = "app_meta_analysis2_forest.php?GeneIndex={$currentRecord['GeneIndex']}&key={$urlKey}&ID={$ID}";
					$currentActions[] 	= "<a title='Create Forest Plot'	href='{$tempURL}' target='_blank'>" . printFontAwesomeIcon('far fa-chart-bar') . "</a>";
				}
			
				$dataHTML['Body'][$currentCount][]= "<div class='text-center'><input currentcount='{$currentCount}' type='checkbox' class='recordCheckbox' value='{$currentRecord['GeneIndex']}'/></div>";
				
				
				
				$dataHTML['Body'][$currentCount][] = implode('&nbsp;', $currentActions);
				

				foreach($currentRecord as $currentHeader => $currentValue){
					
					if ($currentHeader == 'GeneIndex') continue;
					
					
					
					if ((strpos($currentHeader, '_') !== FALSE) || ($currentHeader == 'RankProd')){
						if (is_numeric($currentValue)){
							
							$decimal = 5;
							
							$currentValueForSort 	= number_format($currentValue, 10);
							$currentValue 			= round($currentValue, $decimal);
							
							unset($type);
							if (strpos($currentHeader, 'logFC_') === 0){
								$type = 'Log2FoldChange';
							} elseif (strpos($currentHeader, 'P.Value_') === 0){
								$type = 'PValue';
							} elseif (strpos($currentHeader, 'adj.P.Val_') === 0){
								$type = 'AdjustedPValue';
							}
							

							$currentColor 			= getStatScaleColor($currentValue, $type);
							$dataHTML['Body'][$currentCount][] = "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
						} else {
							
							if ($currentValue == 'NA'){
								$currentValue = '';	
							}
							
							$dataHTML['Body'][$currentCount][] = "<span is_numeric='true'>{$currentValue}</span>";
						}
			
					} else {
						$dataHTML['Body'][$currentCount][] = $currentValue;
					}
					
				}
				
			}

			$dataArray['prepareMetaAnalysisData2']['Processed'][$resultFileKey]['HTML'] = $dataHTML;
			
		}
		
		
		
		
		
		
		
	}
	
	if ($dataArray['POST']['rank_product_analysis']){
		$dataArray['POST']['rank_product_analysis_HTML'] = 'Yes';
	} else {
		$dataArray['POST']['rank_product_analysis_HTML'] = 'No';
	}
	
	if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){
		putSQLCache($cacheKey, $dataArray, '', __FUNCTION__);
	}
	
	return $dataArray;
	
	
	
}



function processMetaAnalysis2_Gene($urlKey, $dataArray, $geneIndex){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	$dir = $dataArray['prepareMetaAnalysisData2']['Summary']['Path'];
	
	if ($dir == ''){
		return false;
	}
	
	if (!is_dir($dir)){
		return false;	
	}
	
	
	$version  = '2018-08-02 14:30';

	$cacheKey = __FUNCTION__ . '::' . md5(
										$urlKey . '::' . 
										json_encode($dataArray) . '::' . 
										$geneIndex . '::' .
										$version);
										
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	if (true){
		$sql_table 				= 'GeneCombined';
		$sql_column 			= 'GeneName';
		$SQL 					= "SELECT * FROM {$sql_table} WHERE `GeneIndex` = '{$geneIndex}'";
		$results['GeneInfo'] 	= getSQL($SQL, 'GetRow', $sql_table);
		
		$results['Gene_HTML']	= $results['GeneInfo'][$sql_column];
	}
	
	if (true){
		
		$path			 = $dir . "Forest_{$geneIndex}/";
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		$results['Forest']['Summary']['Path'] = $path;
		
		if ((!$dataArray['POST']['rank_product_analysis']) && (is_file($dir . 'MetaDE.ES_Output.csv'))){
			$resultFileKey = 'MetaDE.ES_Output.csv';
		}
		
		if (($dataArray['POST']['rank_product_analysis']) && (is_file($dir . 'RP_ES_data.csv'))){
			$resultFileKey = 'RP_ES_data.csv';
		}
		
		if ($resultFileKey != ''){
			$results['Forest']['Summary'][$resultFileKey] = $dir . $resultFileKey;

			$rawData = readFirstFewLinesFromFile($dataArray['prepareMetaAnalysisData2']['Summary'][$resultFileKey], 0, 1, 'csv');
			
			
			foreach($rawData['Body'] as $rowID => $currentRow){
				
				if ($currentRow['GeneIndex'] == $geneIndex){
					
					$inputFilePath = "{$path}subdata.csv";
					$results['Forest']['Summary']['subdata.csv'] = $inputFilePath;
					
					$fp = fopen($inputFilePath, 'w');
		
					$dataArray   = array();
					fputcsv($fp, $rawData['Header']);
					
					fputcsv($fp, $currentRow);
					
					fclose($fp);
					
					
					break;	
				}
			}
			
			
		}

		//command.R
		if (true){
			$R_Script = file_get_contents($BXAF_CONFIG['SHARE_LIBRARY_DIR'] . "R_Files/Forest_Plot.R");
			
			$inputFilePath = "{$path}command.R";
			
			$results['Forest']['Summary']['command.R'] = $inputFilePath;
			
			$fp = fopen($inputFilePath, 'w');
			fwrite($fp, $R_Script);
			fclose($fp);
			
			$fp = fopen($inputFilePath, 'w');
			
			fwrite($fp, $R_Script);
			
			
			fclose($fp);
		}
		
	
	
	
		//run.sh
		if (true){
			$results['Forest']['Summary']['R-Command'] = "{$BXAF_CONFIG['RSCRIPT_BIN']} {$results['Forest']['Summary']['command.R']}";
			$results['Forest']['Summary']['R-Command_Output'] = "{$path}run.log";
			$results['Forest']['Summary']['Status.log'] = "{$path}Status.log";
			
			file_put_contents($results['Forest']['Summary']['Status.log'], 'Prepared');
			
			$results['Forest']['Summary']['Bash'] = "{$path}run.sh";
			
			$cmds = array();
			$cmds[] = "cd {$path}";
			$cmds[] = "echo 'Running' > Status.log";
			$cmds[] = "{$results['Forest']['Summary']['R-Command']} &> {$results['Forest']['Summary']['R-Command_Output']}";
			$cmds[] = "echo 'Finished' > Status.log";
			$bash 	= implode("\n", $cmds) . "\n";
			
			file_put_contents($results['Forest']['Summary']['Bash'], $bash);
			chmod($results['Forest']['Summary']['Bash'], 0755);
			
			shell_exec($results['Forest']['Summary']['Bash']);
		}
		
		if (true){
			if (is_file("{$path}forest_plot.pdf")){
				$results['Forest']['Summary']['forest_plot.pdf'] = "{$path}forest_plot.pdf";
			}
			
			if (is_file("{$path}forest_plot.png")){
				$results['Forest']['Summary']['forest_plot.png'] = "{$path}forest_plot.png";
			}
			
			if (is_file("{$path}forest_plot.svg")){
				$results['Forest']['Summary']['forest_plot.svg'] = "{$path}forest_plot.svg";
			}
			
			if (is_file("{$path}meta_analysis_summary.csv")){
				$results['Forest']['Summary']['meta_analysis_summary.csv'] = "{$path}meta_analysis_summary.csv";
			}
		}
		
	}
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
}

function deleteMetaAnalysis($id){
	
	global $APP_CONFIG;
	
	$user_id 	= intval($_SESSION['BXAF_USER_LOGIN_ID']);
	$id 		= intval($id);
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_Meta_Analysis'];
	
	if ($id > 0){
		$SQL = "DELETE FROM `{$SQL_TABLE}` WHERE (`ID` = {$id})";
		execSQL($SQL);
	}
	
	return true;
	
}

?>