args = commandArgs(trailingOnly=TRUE)
library(stringr)
library(pathview)
input.file=args[1]
name=str_replace(input.file, ".csv", "")
pathway=args[2]
species=substr(pathway, 1, 3)
pathway.id=str_extract(pathway, "\\d+")
gene.limit=as.numeric(args[3])
kegg.dir=args[4]
data=read.csv(input.file)
FCdata=data.matrix(data[, 2:ncol(data)])
rownames(FCdata)=data[, 1]
pv.out <- pathview(gene.data = FCdata,  gene.idtype = "entrez", kegg.dir=kegg.dir,
	  pathway.id = pathway.id, species = species, same.layer = F, map.symbol=T,node.sum="mean",
	  out.suffix =name, kegg.native = T,low = list(gene = "blue", cpd = "blue"), mid =
	list(gene = "white", cpd = "gray"), high = list(gene = "red", cpd =
	"yellow"),  limit = list(gene = gene.limit, cpd = 2),new.signature=F)  #for exp, limit to 3, looks better and match better with heatmap
keggdata=pv.out$plot.data.gene
list3=unique(str_split(paste(keggdata$all.mapped, collapse=','), ',')[[1]])
list3=list3[list3!='']
sel=which(rownames(FCdata) %in% list3)
cat("Kegg", str_c(pathway, '_', name),nrow(keggdata), "List genes ",  sum(is.na(keggdata[,9])), "no data", length(list3), "mapped", length(sel), "in FCdata\n") #use keggdata[,9] for searching NA
write(list3, str_c(pathway, "_", name, ".geneID.list"), sep="\n")

