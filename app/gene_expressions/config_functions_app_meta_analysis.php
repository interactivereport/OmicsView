<?php


function getOldSavedMetaAnalysesCount(){
	
	global $APP_CONFIG;

	$SQL = "SELECT count(*) FROM `PCA_Result`
			WHERE `Owner_ID`='{$APP_CONFIG['User_Info']['ID']}'
			AND `Type`='Meta'
			AND `bxafStatus`<5
			ORDER BY `ID` DESC";

	
	return getSQL($SQL, 'GetOne', 'PCA_Result', 0, 0);
}

function prepareMetaAnalysisData($geneIndexes, $comparisonIndexes, $genePlotColumns, $dataSource, $internalProjectIndexes, $otherOptions){
	
	global $APP_CONFIG, $BXAF_CONFIG;

	$geneIndexes 			= array_clean($geneIndexes);	
	$comparisonIndexes 		= array_clean($comparisonIndexes);
	$genePlotColumns 		= array_clean($genePlotColumns);
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version  = '2018-05-01 15:04';
	
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($comparisonIndexes) . '::' . 
										json_encode($genePlotColumns) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode($otherOptions) . '::' . 
										$version);
										
	$dirKey		= str_replace('::', '_', $cacheKey);										
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'GeneCombined';
	$sql_column 	= 'GeneName';
	
	$columnToPlotString	= implode(', ', $genePlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$valueString 	= implode(', ', $geneIndexes);
	
	
	if ($valueString == ''){
		$geneIndexIsNotSupplied = 1;
		$SQL 			= "SELECT GeneName, GeneIndex FROM {$sql_table} ORDER BY GeneName ASC";
		$geneIndexes	= array_values(getSQL($SQL, 'GetAssoc', $sql_table));
		$valueString 	= implode(', ', $geneIndexes);
		
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table} ORDER BY FIELD(GeneIndex, {$valueString})";
		$geneInfo		= getSQL($SQL, 'GetAssoc', $sql_table);
		
	} else {
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table} WHERE GeneIndex IN ({$valueString}) ORDER BY FIELD(GeneIndex, {$valueString})";
		$geneInfo		= getSQL($SQL, 'GetAssoc', $sql_table);
		
		$results['Summary']['Gene'] = array_column($geneInfo, 'GeneName');
		
	}
	
	
	if (array_size($geneInfo) <= 0){
		return false;	
	}
	
	
	//*******************
	// Search Comparisons
	//*******************
	$geneIndexString 			= implode(', ', $geneIndexes);
	$comparisonIndexString 		= implode(', ', $comparisonIndexes);
	$comparisonIndexStringOrg 	= $comparisonIndexString;
	
	
	if (true){
		$sql_table		= 'ComparisonData';
		$value_table 	= 'ComparisonData';
		
		$valueColumns = array('Log2FoldChange', 'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue');
		
		if (array_size($APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['ComparisonData-Override']['valueColumns']) > 0){
			$valueColumns = $APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['ComparisonData-Override']['valueColumns'];
		}
		
	}
		
	$valueColumnsString = implode(', ', $valueColumns);
	$SQL 		= "SELECT GeneIndex, ComparisonIndex, {$valueColumnsString} FROM {$sql_table} WHERE (GeneIndex IN ({$geneIndexString})) AND (ComparisonIndex IN ({$comparisonIndexString}))";
	
	unset($geneExpressionValue);
	if ($dataSource['public'] != ''){
		if ($geneIndexIsNotSupplied){
			$geneExpressionValue = tabix_search_records_with_index('',           $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
		} else {
			$geneExpressionValue = tabix_search_records_with_index($geneIndexes, $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
		}
	}
	
	
	if ($dataSource['private'] != ''){
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			unset($temp);
			if ($geneIndexIsNotSupplied){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, '',           $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			} else {
				$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			}
			

			
			foreach($temp as $tempKeyX => $tempValueX){
				$geneExpressionValue[] = $tempValueX;	
			}
			unset($temp);
		}
		
	}
	


	
	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentGeneIndex 		= $tempValue['GeneIndex'];
		$currentComparisonIndex = $tempValue['ComparisonIndex'];
		
		unset($keepGoing);
		if (!isset($score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue['PValue']) && ($tempValue['PValue'] < $score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue['PValue']) && !is_numeric($score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		}
		
		if (!$keepGoing) continue;
		
		$score[$currentGeneIndex][$currentComparisonIndex] = $tempValue['PValue'];
		$tabixFinalists[$currentComparisonIndex][$currentGeneIndex] = $tempValue;
		
	}
	unset($score);



	//*******************
	// Search Comparison Index
	//*******************
	$columnToPlotString	= implode(', ', $comparisonPlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$comparisonIndexString = array_clean(array_column($geneExpressionValue, 'ComparisonIndex'));
	$comparisonIndexString = implode(',', $comparisonIndexString);
	
	
	unset($comparisons);
	if ($comparisonIndexString != ''){
		
		if ($dataSource['public'] != ''){
			$sql_table	= 'Comparisons';
			$SQL 		= "SELECT `ComparisonIndex`, `ComparisonID` FROM {$sql_table} WHERE ComparisonIndex IN ({$comparisonIndexString}) ORDER BY FIELD(ComparisonIndex, {$comparisonIndexStringOrg})";	
		
			$comparisons	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table	= 'App_User_Data_Comparisons';
			$SQL 		= "SELECT `ComparisonIndex`, `ComparisonID` FROM {$sql_table} WHERE ComparisonIndex IN ({$comparisonIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ComparisonIndex, {$comparisonIndexStringOrg})";
		
			$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($temp as $tempKeyX => $tempValueX){
				$comparisons[$tempKeyX] = $tempValueX;
			}
		}
	
	}
	
	
	
	
	if (true){
		$path			 = "{$BXAF_CONFIG['WORK_DIR']}META_ANALYSIS/{$BXAF_CONFIG['APP_PROFILE']}/{$dirKey}/";
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		$results['Summary']['Path'] = $path;
	}
	
	if (true){
		$inputFilePath = "{$path}Genes_Comparisons_Raw.txt";
		
		$results['Summary']['Genes_Comparisons_Raw.txt'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		foreach($tabixFinalists as $tempKey1 => $tempValue1){
			
			foreach($tempValue1 as $tempKey2 => $currentRow){
				fputcsv($fp, $currentRow, "\t");
			}
		}
		
		fclose($fp);
	}
	
	
	if (true){

		$inputFilePath = "{$path}Comparison_Info.csv";
		
		$results['Summary']['Comparison_Info.csv'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		
		$dataArray   = array();
		$dataArray[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonIndex']['Title'];
		$dataArray[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
		
		fputcsv($fp, $dataArray);
		
		foreach($comparisons as $comparisonIndex => $comparisonID){
			
			$dataArray   = array();
			$dataArray[] = $comparisonIndex;
			$dataArray[] = $comparisonID;
			
			fputcsv($fp, $dataArray);
		}
		
		fclose($fp);
	}
	
	
	if (true){

		$inputFilePath = "{$path}Gene_Info.csv";
		
		$results['Summary']['Gene_Info.csv'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		
		$dataArray   = array();
		$dataArray[] = 'GeneIndex';
		foreach($genePlotColumns as $tempKey => $currentColumn){
			$dataArray[] = $currentColumn;
		}
		
		fputcsv($fp, $dataArray);
		
		foreach($geneInfo as $geneIndex => $currentRow){
			
			$dataArray   = array();
			$dataArray[] = $geneIndex;
			foreach($currentRow as $tempKey => $currentValue){
				$dataArray[] = $currentValue;
			}
			
			fputcsv($fp, $dataArray);
		}
		
		fclose($fp);
	}
	
	
	if (true){
		$R_Script = 
'library(MetaDE)
library(data.table)
library(dplyr)
library(RankProd)
library(stringr)
options(stringsAsFactors=F)
miss.tol = {miss.tol}
logFC_cutoff={logFC_cutoff}
sig_type="{sig_type}"  #FDR or P-value
sig_cutoff={sig_cutoff}

#read raw data
system.time(raw_data<-fread("Genes_Comparisons_Raw.txt", sep="\t", 
stringsAsFactors=FALSE, select=1:6, fill=T))
raw_data=raw_data[, V4:=as.numeric(V4) ]
raw_data=raw_data[, V5:=as.numeric(V5) ]
raw_data=raw_data[, V6:=as.numeric(V6) ]

#rank by p-value (smaller first)
raw_data=raw_data[order(V5), ]

#remove duplicate gene names (the one with the smallest p-value will be kept)
P1=paste(raw_data$V1, raw_data$V2, sep="_")
sel=!duplicated(P1)
cat("Remove duplicate: keep", sum(sel), "out of", length(P1), "unique data points\n")
raw_data=raw_data[sel, ]

#now cast for FDR, p-value and logFC matrix
system.time(logFC<-dcast(raw_data, V2~V1, value.var="V4") )
system.time(pval<-dcast(raw_data, V2~V1, value.var="V5") )
system.time(FDR<-dcast(raw_data, V2~V1, value.var="V6") )


#compute simple count statistics
Ncomp=ncol(logFC)
N.data.points=rowSums(!is.na(logFC[, 2:Ncomp]))
if (sig_type=="FDR") {
	up_sel=(logFC[, 2:Ncomp]>=logFC_cutoff & FDR[, 2:Ncomp]<=sig_cutoff)
	N_Up=rowSums(up_sel, na.rm=T)
	down_sel=(logFC[, 2:Ncomp]<=(-logFC_cutoff) & FDR[, 2:Ncomp]<=sig_cutoff)
	N_Down=rowSums(down_sel, na.rm=T)
} else {
 	up_sel=(logFC[, 2:Ncomp]>=logFC_cutoff & pval[, 2:Ncomp]<=sig_cutoff)
 	N_Up=rowSums(up_sel, na.rm=T)
 	down_sel=(logFC[, 2:Ncomp]<=(-logFC_cutoff) & pval[, 2:Ncomp]<=sig_cutoff)
 	N_Down=rowSums(down_sel, na.rm=T)
}
Meta_out=data.table(GeneIndex=logFC$V2, N.data.points, Up.Per=100*N_Up/N.data.points, Down.Per=100*N_Down/N.data.points)



###Consider use RankProducts on logFC only
logFCdata=data.matrix(logFC[, 2:Ncomp]); rownames(logFCdata)=logFC$V2
#remove rows with all NAs
logFCdata=logFCdata[rowSums(!is.na(logFCdata))>0, ]
cl=rep(1, ncol(logFCdata))
genes=rownames(logFCdata)
RP.out<- RankProducts(logFCdata, cl, logged=T, na.rm=T,  plot=F, rand=123, MinNumOfValidPairs=2,gene.names=genes)
outdata=topGene(RP.out,num.gene=nrow(logFCdata), logged=T, gene.names=genes )
RP1=outdata[[1]]; RP2=outdata[[2]]  #here Up and Down are inverted as the two classes 
colnames(RP1)=str_c("Down_", c("gindex", "RP", "FC", "PFP", "P.Val") ) 
colnames(RP2)=str_c("Up_", c("gindex", "RP", "FC", "PFP", "P.Val")  )

RP<-data.frame(RP1)%>%mutate(GeneIndex=rownames(RP1)) %>%left_join(data.frame(RP2), by=c("Down_gindex"="Up_gindex") )

RP<-RP%>%mutate(RP_logFC=log2(Up_FC), RP_Pval=ifelse(RP_logFC>0, Up_P.Val, Down_P.Val),
 RP_FDR=ifelse(RP_logFC>0, Up_PFP, Down_PFP), RankProd=ifelse(RP_logFC>0, Up_RP, Down_RP))

RP<-RP%>%select(GeneIndex, RankProd,RP_logFC,RP_Pval,RP_FDR)
RP$GeneIndex=as.numeric(RP$GeneIndex)
RP<-RP%>%mutate(RP_logFC=round(RP_logFC*100)/100)
Meta_out<-Meta_out%>%left_join(RP)


#combined pvalues
p_test=pval[, 2:Ncomp]
rownames(p_test)=pval$V2
DE_test=list(p=p_test, bp=NULL)
Meta_out$Combined_Pval_Fisher=MetaDE.pvalue(DE_test, meta.method="Fisher",miss.tol=miss.tol )$meta.analysis$pval
Meta_out$Combined_Pval_maxP=MetaDE.pvalue(DE_test, meta.method="maxP",miss.tol=miss.tol )$meta.analysis$pval

##Added new lines
Meta_out$Combined_FDR_Fisher=MetaDE.pvalue(DE_test, meta.method="Fisher",miss.tol=miss.tol )$meta.analysis$FDR
Meta_out$Combined_FDR_maxP=MetaDE.pvalue(DE_test, meta.method="maxP",miss.tol=miss.tol )$meta.analysis$FDR


############


#head(Meta_out[order(Combined_Pval_maxP), ]) #review

#now combine data
geneInfo=read.csv("Gene_Info.csv", row.names=1)
sel=match(Meta_out$GeneIndex, rownames(geneInfo) ) 
#Meta_out=cbind(Meta_out, geneInfo[sel, ])
Meta_out=cbind(geneInfo[sel, 1:ncol(geneInfo), drop=F], Meta_out)

compInfo=read.csv("Comparison_Info.csv", row.names=1)
comp.names=compInfo[colnames(logFC)[2:Ncomp], 1]
colnames(logFC)[2:Ncomp]=paste(comp.names, "logFC", sep= "_")
colnames(pval)[2:Ncomp]=paste(comp.names, "Pval", sep="_")
colnames(FDR)[2:Ncomp]=paste(comp.names, "FDR", sep="_")
comp.data=cbind(logFC[, 2:Ncomp], pval[, 2:Ncomp], FDR[, 2:Ncomp])
#set new order logFC
a=NULL
for (i in 1:(Ncomp-1) ) {	a=c(a, i, i+Ncomp-1, i+2*(Ncomp-1) ) }
comp.data=setcolorder(comp.data, a)
Meta_out=cbind(Meta_out, comp.data)
#fwrite(Meta_out, "Meta_output_RankProd.csv", row.names=F)
fwrite(Meta_out, "Meta_output.csv", row.names=F)
';
		
		
		$R_Script = str_replace('{miss.tol}', $otherOptions['Missing_Total'], $R_Script);
		$R_Script = str_replace('{logFC_cutoff}', $otherOptions['LogFC_Cutoff'], $R_Script);
		$R_Script = str_replace('{sig_type}', $otherOptions['Statistical_Type'], $R_Script);
		$R_Script = str_replace('{sig_cutoff}', $otherOptions['Statistic_Cutoff'], $R_Script);
		
		
		
		$inputFilePath = "{$path}command.R";
		
		$results['Summary']['command.R'] = $inputFilePath;
		
		$fp = fopen($inputFilePath, 'w');
		
		fwrite($fp, $R_Script);
		
		
		fclose($fp);
		
	}
	
	
	if (true){
		$results['Summary']['R-Command'] = "{$BXAF_CONFIG['RSCRIPT_BIN']} {$results['Summary']['command.R']}";
		
		$results['Summary']['Bash'] = "{$path}run.sh";
		
		$bash = "cd {$path};\n
		{$results['Summary']['R-Command']};\n";
		
		file_put_contents($results['Summary']['Bash'], $bash);
		chmod($results['Summary']['Bash'], 0755);
		
		shell_exec($results['Summary']['Bash']);
	}
	
	$results['Summary']['Meta_output.csv'] = "{$path}Meta_output.csv";
	
	if (is_file($results['Summary']['Meta_output.csv']) && filesize($results['Summary']['Meta_output.csv']) > 0){
		$results['Summary']['hasResult'] = true;
		
		$results['Output_Raw'] = readFirstFewLinesFromFile($results['Summary']['Meta_output.csv'], 0, 1);
		
		
		foreach($results['Output_Raw']['Header'] as $currentHeaderIndex => $currentHeader){
			

			$results['Headers'][$currentHeader]['Key'] 	= $currentHeaderIndex;			
			$results['Headers'][$currentHeader]['Display'] = 1;
			$results['Headers'][$currentHeader]['Title']		= $currentHeader;
			$results['Headers'][$currentHeader]['Title_HTML']	= $currentHeader;
			
			if ($currentHeader == 'GeneIndex'){
				$results['Headers'][$currentHeader]['Type']	= 'Gene';
				$results['Headers'][$currentHeader]['Display']	= 0;
				$results['Headers'][$currentHeader]['Title'] 		= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentHeader]['Title'];
				$results['Headers'][$currentHeader]['Title_HTML'] 	= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentHeader]['Title'];
			} elseif (in_array($currentHeader, $genePlotColumns)){
				$results['Headers'][$currentHeader]['Type']	= 'Gene';
				$results['Headers'][$currentHeader]['Title'] 	= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentHeader]['Title'];
				$results['Headers'][$currentHeader]['Title_HTML'] 	= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentHeader]['Title'];
			} else {
				
				if (strpos($currentHeader, '_logFC') !== FALSE){
					$results['Headers'][$currentHeader]['Type']	= 'Log2FoldChange';
					$results['Headers'][$currentHeader]['Numeric']	= 1;
					
					if (endsWith($currentHeader, '_logFC')){
						$currentHeaderDisplay = str_replace('_logFC', '<br/>(log<sub>2</sub> Fold Change)', $currentHeader);
						$results['Headers'][$currentHeader]['Title_HTML'] = $currentHeaderDisplay;
					}
					
				} elseif (strpos($currentHeader, '_Pval') !== FALSE){
					$results['Headers'][$currentHeader]['Type']	= 'PValue';
					$results['Headers'][$currentHeader]['Numeric']	= 1;
					
					if (endsWith($currentHeader, '_Pval')){
						$currentHeaderDisplay = str_replace('_Pval', '<br/>(p-value)', $currentHeader);
						$results['Headers'][$currentHeader]['Title_HTML'] = $currentHeaderDisplay;
					}
					
				} elseif (strpos($currentHeader, '_FDR') !== FALSE){
					
					$results['Headers'][$currentHeader]['Type']	= 'AdjustedPValue';
					$results['Headers'][$currentHeader]['Numeric']	= 1;
					
					if (endsWith($currentHeader, '_FDR')){
						$currentHeaderDisplay = str_replace('_FDR', '<br/>(FDR)', $currentHeader);
						$results['Headers'][$currentHeader]['Title_HTML'] = $currentHeaderDisplay;
					}
					
				} else {
					$results['Headers'][$currentHeader]['Numeric']	= 1;
					$results['Headers'][$currentHeader]['Decimal']	= 2;
					
				}

				
				if (isset($APP_CONFIG['APP']['Meta_Analysis_Headers'][$currentHeader]['Print'])){
					$results['Headers'][$currentHeader]['Title'] 		= $APP_CONFIG['APP']['Meta_Analysis_Headers'][$currentHeader]['Print'];
				}
				
				if (isset($APP_CONFIG['APP']['Meta_Analysis_Headers'][$currentHeader]['HTML'])){
					$results['Headers'][$currentHeader]['Title_HTML']	= $APP_CONFIG['APP']['Meta_Analysis_Headers'][$currentHeader]['HTML'];
				}
				
			}
			
			
		}
		
		
		
		
		
		
		
		
		
	} else {
		$results['Summary']['hasResult'] = false;
	}
	
	
	$results['Summary']['ComparisonIndex'] = $comparisons;
	
	
	
	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}


function processMetaAnalysisData($headers, $rawData, $otherOptions){

	global $APP_CONFIG;
	
	foreach($rawData as $currentKey => $currentRecord){
		if ($otherOptions['n_data_points_enable']){
			if ($currentRecord['N.data.points'] < $otherOptions['n_data_points_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['RP_Pval_enable']){
			if ($currentRecord['RP_Pval'] > $otherOptions['RP_Pval_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['RP_logFC_max_enable']){
			if ($currentRecord['RP_logFC'] > $otherOptions['RP_logFC_max_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		
		if ($otherOptions['RP_logFC_min_enable']){
			if ($currentRecord['RP_logFC'] < $otherOptions['RP_logFC_min_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['Combined_Pval_maxP_enable']){
			if ($currentRecord['Combined_Pval_maxP'] > $otherOptions['Combined_Pval_maxP_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['Combined_Pval_Fisher_enable']){
			if ($currentRecord['Combined_Pval_Fisher'] > $otherOptions['Combined_Pval_Fisher_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['up_per_enable']){
			if ($currentRecord['Up.Per'] < $otherOptions['up_per_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
		
		if ($otherOptions['down_per_enable']){
			if ($currentRecord['Down.Per'] < $otherOptions['down_per_value']){
				unset($rawData[$currentKey]);
				continue;
			}
		}
	}
	
	if ($otherOptions['display_enable']){
		foreach($headers as $currentKey => $currentInfo){
			
			$currentType = $currentInfo['Type'];
			
			if (!isset($APP_CONFIG['APP']['Meta_Analysis']['Display'][$currentType])) continue;
			
			if (!in_array($currentType, $otherOptions['display'])){
				
				$headers[$currentKey]['Display'] = 0;
				
			}
		}
	}
	

	
	
	$results['Headers'] = $headers;
	$results['Body']	= $rawData;
	
	return $results;
	
	
}


?>