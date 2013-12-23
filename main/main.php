<?php
error_reporting(0);
$item_num =0;
$archive_num=0;
$class_pattern="/(scout|soldier|pyro|demoman|heavy|engineer|sniper|medic|spy)/";
$config['hat_prefix']=$config['hat_prefix']."_";
$config["schema_file"] = get_schema_file();
$items=load_data();
$files = scandir(getcwd().'/'.$config['archive_subfolder']);
$class_items = array();
function get_schema_file() {
	global $config;
	return $config['main_folder']."schema".$config["game"].".json";
}
function get_style_file() {
	global $config;
	return $config['main_folder']."style.css";
}
function do_curl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function load_schema() {
	global $config;
	$api_url='http://api.steampowered.com/IEconItems_'.rawurlencode($config['game']).'/GetSchema/v0001/?key='.rawurlencode($config['api_key']).'&format=json&language=en';
	$api_return = json_decode(do_curl($api_url),true);
	$items = $api_return["result"]["items"];
	write_cache($items);
	return $items;
}
function write_cache($cache) {
	global $config;
	$dirname = dirname($config['schema_file']);
	if (!is_dir($dirname))
	{
		mkdir($dirname, 0755, true);
	}
	$content=json_encode($cache);
	$f=fopen($config['schema_file'], 'wb');
	if(!$f) return;
	fwrite($f, $content);
	fclose($f);
}
function read_cache() {
	global $config;
	if(!file_exists($config['schema_file'])) {return false;echo "Cache does not exist. Please ensure the path is valid.";}
	$cache=json_decode(file_get_contents($config['schema_file']),true);
	if(!isset($cache)) {return false;echo "Cache not set. Please ensure the path is valid.";}
	return $cache;
}
function load_data() {
	global $config;
	$cache=read_cache();
	if($cache){
		return $cache;
	}
	return load_schema();
}
function get_schema_info($archive_link,$noitem,$class) {
	global $items,$class_items;
	foreach ($items as $item) {
		$item_no = str_replace(" ","_",$item["name"]);
		$item_no = preg_replace("/[^-a-zA-Z0-9_]+/","",$item_no);
		if($item_no===$noitem){
			$mod_item = array();
			$mod_item[0]=$archive_link;
			$mod_item[1]=$item["image_url"];
			$mod_item[2]=$item["item_name"];
			if(count($item["used_by_classes"])===1) {
				$class=strtolower($item["used_by_classes"][0]);
			} elseif (!isset($item["used_by_classes"])) {
				$class="all-class";
			}
			$class_items[$class][]=$mod_item;
		}
	}
}
function render_class_items() {
	global $class_items, $config,$item_num;
	foreach($class_items as $key => $class) {
		echo "<div>Click the <span class=\"class\">".ucfirst($key)."</span> item you'd like to replace</div>";
		foreach($class as $mod_item) {
			$item_num++;
			echo "<a href=".$config['archive_subfolder'].'/'.$mod_item[0]."><img src=\"".$mod_item[1]."\" alt=\"".$mod_item[2]."\" title=\"".$mod_item[2]."\"></img></a>";
		}
	}
}
function execute() {
	global $files,$archive_num,$config,$item_num,$class_pattern;
	echo "<div id=\"hat_links\">";
	foreach ($files as $f) {
		if($f!="."&&$f!=".."&&$f!="place_archives_here.txt") {
			$archive_num++;
			$noext = str_replace(".".$config['ext'],"",$f);
			$noitem = str_replace($config['hat_prefix'],"",$noext);
			$split_name=explode("_",$noitem);
			$class_name = $split_name[count($split_name)-1];
			if(preg_match($class_pattern,$class_name)) {
				array_pop($split_name);
				$noitem=implode("_",$split_name);
				get_schema_info($f,$noitem,$class_name);
			} else {
				get_schema_info($f,$noitem);
			}
		}
	}
	render_class_items();
	echo "</div>";
	if($config['check_count']){
		echo "<div>$archive_num archives, $item_num items present</div>";
	}
	if($config['workshop_id']) {
		echo "<a href=\"http://steamcommunity.com/sharedfiles/filedetails/?id=".$config['workshop_id']."\" id=\"workshop_link\" target=\"_blank\"><img src=\"".$config['main_folder']."workshop.jpg\"></a>";
	}
}
?>