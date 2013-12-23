<?php
$config=array(
'api_key'=>'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', // API key to use. Get one here: http://steamcommunity.com/dev/apikey
'main_folder'=>'../main/', //Location of the main folder
'check_count'=>false, // Change this to true to check that all archives are present upon the page. Essentially a dev thing, best keep it off for shipping.
'game'=>440, //440 = TF2, 570 = Dota
'ext'=>'vpk', //.vpk, .zip, .rar etc
'archive_subfolder'=>'archives', //subfolder that contains archives

//Most of the stuff above can be left the same over a number of releases. The stuff below are the bits you will have to change.
'hat_name'=>'HAT NAME', //Release hat name
'hat_prefix'=>'hatprefix', //Stuff at the beginning of the archive
'workshop_id'=>'XXXXXXXX' //Bit after ?id= in workshop submission. Leave blank for no workshop button
);
$main_file = $config['main_folder']."main.php";
include_once($main_file);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $config['hat_name']?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo get_style_file()?>">
	<meta charset=utf-8>
</head>
<body>
	<?php
		execute();
	?>
</body>
</html>