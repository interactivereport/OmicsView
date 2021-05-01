<?php



  $TYPE = $_GET['type'];
  $options = $BXAF_CONFIG['TBL_PREFERENCE_ALL_OPTIONS']['dashboard_displayed_' . $TYPE];

  $result = array();
  foreach ($options as $option) {
    if (isset($_POST['displayed_options_checkbox_' . str_replace('.', '_', (str_replace(' ', '_', $option)))])) {
      $result[] = $option;
      echo $option;
    }
  }

  $info = array(
    'Detail' => serialize($result)
  );
  $DB -> update(
    $BXAF_CONFIG['TBL_USERPREFERENCE'],
    $info,
    "`User_ID`={$BXAF_CONFIG['BXAF_USER_CONTACT_ID']} AND `Category`='dashboard_displayed_" . $TYPE . "'"
  );

  exit();


?>