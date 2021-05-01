<?php

$currentTable = 'Projects';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 		= 'Project Info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 		= 'download/Project_Info.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 		= 'App_User_Data_Projects';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Project information is missing. Please provide a project info file.';

$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['projectname']		 			= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['project_name']		 		= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['project name']		 		= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['project_id']		 			= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['project id']		 			= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['title']			 			= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['name']			 			= 'ProjectID';

$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'project';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'projects';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'project_info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'projects_info';


$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['platformname']	 	= 'PlatformType';


$currentHeader = 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'LandPlatforms';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Land Platforms';

$currentHeader = 'Accession';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Accession';

$currentHeader = 'ExperimentType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Experiment Type';

$currentHeader = 'ContactAddress';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Address';

$currentHeader = 'ContactDepartment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Department';

$currentHeader = 'ContactLaboratory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Laboratory';

$currentHeader = 'ContactName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Name';

$currentHeader = 'ContactEmail';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Email';

$currentHeader = 'ContactPhone';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Phone';

$currentHeader = 'ContactFax';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Fax';

$currentHeader = 'ContactWebLink';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Web Link';

$currentHeader = 'Keywords';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Keywords';

$currentHeader = 'ReleaseDate';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Release Date';

$currentHeader = 'BioProject';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'NCBI BioProject URL';

$currentHeader = 'Platform';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform';

$currentHeader = 'PlatformType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Type';

$currentHeader = 'PlatformProvider';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Provider';

$currentHeader = 'PlatformOrganism';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Organism';

$currentHeader = 'PlatformDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Description';

$currentHeader = 'PlatformTechnology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Technology';

$currentHeader = 'Description';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Description';

$currentHeader = 'Design';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Design';

$currentHeader = 'Disease';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Disease';

$currentHeader = 'StudyType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Study Type';

$currentHeader = 'TherapeuticArea';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Therapeutic Area';

$currentHeader = 'Comment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comment';

$currentHeader = 'SuperSeries';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Super Series';

$currentHeader = 'Collection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Collection';

$currentHeader = 'ProtocolDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Protocol Description';

$currentHeader = 'Title';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Title';

$currentHeader = 'Contributors';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contributors';

$currentHeader = 'WebLink';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'WebLink';

$currentHeader = 'ContactOrganization';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Contact Organization';

$currentHeader = 'PubMed';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'PubMed';

$currentHeader = 'PubMed_Authors';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'PubMed Authors';

$currentHeader = 'ProjectCollection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project Collection';






?>