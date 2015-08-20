<?php
/**
 * 
 * 
 * User: zouyi
 * Date: 2015-08-20 09:29
 */
set_time_limit(0);
require_once("vendor/autoload.php");

\di3\Benchmark\Benchmark::start('total');
$base_url = 'https://packagist.org/explore/popular';
$repository = 'repository/packagist';
function HttpSend($url){
    return \Httpful\Request::get($url)->timeout(10)->send();
}


$file = new \League\Flysystem\File();

$fs = new \League\Flysystem\Adapter\Local(__DIR__.'/');
$filesystem = new \League\Flysystem\Filesystem($fs);
$filesystem->get('packagelist.txt',$file);
$data = $file->read();

$array = explode("\r\n",$data);
foreach($array as $json_url){
    $composer_url = $json_url;
    $file_path = str_replace('https://packagist.org','',$composer_url);
if(file_exists($repository.$file_path)){echo 'skip '.$file_path."\r\n";continue;}
    try{

        echo  "Fetching ".$composer_url. "\r\n";
        $result = HttpSend($composer_url);
        $filesystem->get($repository.$file_path,$file);
        $file->write($result->raw_body);


    }catch (exception $e){

    }

}

\di3\Benchmark\Benchmark::end('total');
\di3\Benchmark\Benchmark::echoStringTimeDiff('total');
