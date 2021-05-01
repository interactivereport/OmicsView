<?php
include_once(dirname(__DIR__) . "/config/config.php");

if(isset($_GET['name']) && $_GET['name'] != ''){
    $sql = "SELECT `ComparisonIndex` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ComparisonID` = ?s";
    $_GET['id'] = $BXAF_MODULE_CONN -> get_one($sql, $_GET['name'] );

    if($_GET['id'] <= 0){
        $sql = "SELECT `ComparisonIndex` FROM `App_User_Data_Comparisons` WHERE `ComparisonID` = ?s";
        $_GET['id'] = $BXAF_MODULE_CONN -> get_one($sql, $_GET['name'] );
    }
}

if($_GET['id'] <= 0){
    echo "Comparison '" . $_GET['name'] . "' is not found";
}
else {
    header("Location: " . $BXAF_CONFIG['BXAF_APP_URL'] . "plot/search_comparison/single_comparison.php?type=comparison&id=" . $_GET['id']);
}
exit();

?>