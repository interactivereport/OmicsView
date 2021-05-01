<?php

include('config_init.php');

$geneIndex = search_gene_index($_GET['Gene']);

header("Location: app_gene_review.php?ID={$geneIndex}");
exit();


?>