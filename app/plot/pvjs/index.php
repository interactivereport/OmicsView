<?php

header("Location: ../../bxgenomics/tool_pathway/index.php" . ($_SERVER['QUERY_STRING'] == '' ? '' : ('?' . $_SERVER['QUERY_STRING'] )) );
exit();
