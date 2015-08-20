<?php
/**
 * 
 * 
 * User: zouyi
 * Date: 2015-08-20 09:29
 */
set_time_limit(0);
require_once("vendor/autoload.php");

use Sunra\PhpSimple\HtmlDomParser;

\di3\Benchmark\Benchmark::start('total');
$base_url = 'https://packagist.org/explore/popular';
$repository = 'repository/packagist';
function HttpSend($url){
    return \Httpful\Request::get($url)->timeout(3)->send();
}


    $file = new \League\Flysystem\File();

    $fs = new \League\Flysystem\Adapter\Local(__DIR__.'/',FILE_APPEND);
    $filesystem = new \League\Flysystem\Filesystem($fs,array('visibility'=>'public'));
$filesystem->get('all.txt',$file);
$data = $file->read();
$data = str_replace('https://packagist.org/explore/popular?page=','',$data);
$data_array = explode("\r\n",$data);
for($i=1;$i<=400;$i++){
if(in_array($i,$data_array))continue;
    $url = $base_url.'?page='.$i;
    try{

        $result = HttpSend($url);
        $html = $result->body;
        $dom = HtmlDomParser::str_get_html( $html );
        echo "Fetching ".$url. "\r\n";
        $data = "";
        foreach($dom->find("a[href^=\"/packages]") as $composer) {
            $file_path = $composer->href.'.json';
            $composer_url = 'https://packagist.org'.$file_path;

            if($composer->href == '/packages/submit') continue;
            $data .= $composer_url. "\r\n";

        }

        $filesystem->get('packagelist.txt',$file);
        $file->write($data);

        $filesystem->get('all.txt',$file);
        $file->write($url. "\r\n");

    }catch (exception $e){
        echo "$e";
    }

}

\di3\Benchmark\Benchmark::end('total');
\di3\Benchmark\Benchmark::echoStringTimeDiff('total');
