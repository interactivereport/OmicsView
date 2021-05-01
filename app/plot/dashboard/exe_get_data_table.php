<?php


  // print_r($_POST);
  $COMPARISON_NAMES = array();
  foreach (explode(",", $_POST['data']) as $comp) {
    $COMPARISON_NAMES[] = $comp;
  }



  echo "<table class='table table-bordered datatable dc-chart' id='table'>
    <thead>
      <tr>
        <th class='dc-table-head'>Comparison ID</th>
        <th class='dc-table-head'>{$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_CellType']}</th>
        <th class='dc-table-head'>{$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_DiseaseState']}</th>
        <th class='dc-table-head'>Comparison Category</th>
        <th class='dc-table-head'>Comparison Contrast</th>
        <th class='dc-table-head'>{$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_Treatment']}</th>
        <th class='dc-table-head'>Subject Treatment</th>
        <th class='dc-table-head'>PlatformName</th>
      </tr>
    </thead>
    <tbody>";

  foreach ($COMPARISON_NAMES as $comparison_name) {
    $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
          WHERE `ComparisonID`='" . $comparison_name . "'";
    $data = $DB -> get_row($sql);

    if (is_array($data) && count($data) > 1) {
      echo '
      <tr class="dc-table-row">
        <td class="dc-table-column _0">
          <p style="white-space:nowrap; margin-bottom:0px;">
            <input type="checkbox" class="comparison_checkbox m-r-sm" rowid="' . $data['ComparisonIndex'] . '">
            <a href="../search_comparison/single_comparison.php?type=comparison&id=' . $data['ComparisonIndex'] . '"
               title="View Detail" target="_blank">
              ' . $data['ComparisonID'] . '
            </a><br />
            <a href="../search_comparison/single_comparison.php?type=comparison&id=' . $data['ComparisonIndex'] . '"
               class="mr-3"
               title="View Detail" target="_blank">
              <i class="fa fa-list-ul"></i>
            </a>
            <a href="../volcano/index.php?id=' . $data['ComparisonIndex'] . '"
               class="mr-3"
               title="View Volcano Plot" target="_blank">
              <i class="fa fa-pie-chart"></i>
            </a>
            <a href="../pvjs/index.php?id=' . $data['ComparisonIndex'] . '"
               title="View Pathway" target="_blank">
              <i class="fa fa-bar-chart"></i>
            </a>
          </p>
        </td>
        <td class="dc-table-column _1">' . $data['Case_CellType'] . '</td>
        <td class="dc-table-column _2">' . $data['Case_DiseaseState'] . '</td>
        <td class="dc-table-column _3">' . $data['ComparisonCategory'] . '</td>
        <td class="dc-table-column _4">' . $data['ComparisonContrast'] . '</td>
        <td class="dc-table-column _5">' . $data['Case_Treatment'] . '</td>
        <td class="dc-table-column _6">' . $data['Case_SubjectTreatment'] . '</td>
        <td class="dc-table-column _7">' . $data['PlatformName'] . '</td>
      </tr>';
    }

  }

  echo '</tbody></thead></table>';



  exit();


?>