<?php
/**
 * 
 * 
 * User: zouyi
 * Date: 2015-08-20 09:29
 */
set_time_limit(0);
require_once("vendor/autoload.php");


$url = 'https://packagist.org/packages.json';
$repository = 'repository/packagist';
function HttpSend($url){
    return \Httpful\Request::get($url)->timeout(50)->send();
}

        $result = HttpSend($url);
        $json = $result->raw_body;

file_put_contents($repository."/packages.json",$json);
$provider = json_decode($json,TRUE);
foreach($provider['provider-includes'] as $file_name =>$data){
	$file = str_replace("%hash%",$data['sha256'],$file_name);
if(file_exists($repository.'/'.$file)){echo "skip $file\r\n";continue;}
$del_path = $repository."/".str_replace("$%hash%.json",'',$file_name)."*";
	exec("rm -rf ".$del_path);
echo $del_path."\r\n";
	echo $file."\r\n";
	$json_url = 'https://packagist.org/'.$file;
	$json = HttpSend($json_url);

	file_put_contents($repository.'/'.$file,$json->raw_body);
}
