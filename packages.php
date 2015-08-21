<?php
/**
 *
 *
 * User: zouyi
 * Date: 2015-08-20 09:29
 */
set_time_limit(0);
require_once("vendor/autoload.php");

define('USE_PROXY',0);
function HttpSendLong($url)
{
    if (USE_PROXY) {
        echo 1;
        return \Httpful\Request::get($url)->timeout(300)->useProxy('10.199.75.12', '8080')->send();
    }
    return \Httpful\Request::get($url)->timeout(60)->send();
}
function HttpSend($url)
{
    if (USE_PROXY) {
        echo 2;
        return \Httpful\Request::get($url)->timeout(5)->useProxy('10.199.75.12', '8080')->send();
    }
    return \Httpful\Request::get($url)->timeout(5)->send();
}

class BuildComposer
{

    public $packagist_url = '';

    public $packages_json_url = '';

    public $package_url = '';

    public $repository_path = '';

    public $package_path = '';
 
    public $package_path2 = ''; 
    static private  $instance;

    public function __construct()
    {
        $this->packagist_url = 'https://packagist.org';
        $this->packages_json_url = $this->packagist_url . '/packages.json';

        $this->package_url = $this->packagist_url . "/p/%package%$%hash%.json";


        $this->repository_path = 'repository/packagist';
        $this->package_path = "/pub/repository/packagist/packages/%package%.json";
$this->package_path2 = $this->repository_path."/packages/%package%.json"; 
   }


    static public function object(){
        if(self::$instance){

            return self::$instance;
        }
        return self::$instance = new BuildComposer();
    }
    /**
     * Á³Ì     * 1 read packages.json
     * 2 get provider-includes
     * 3 read %provider%$%hash%.json
     * 4 get keys(package name)
     * 5 save package to local
     */
    public function init()
    {
        $result = HttpSendLong($this->packages_json_url);
        $json = $result->raw_body;
        $provider = json_decode($json, TRUE);
        var_dump($provider);
        $provider['providers-url'] = $this->package_path;
        $provider['fetch_packagist_time'] = time();
        $provider['fetch_packagist_date'] = date("Y-m-d H:i:s");
        $provider_includes = $provider['provider-includes'] ;
        $provider['provider-includes'] = array();
        foreach ($provider_includes as $file_name => $data) {
            $provider['provider-includes'][str_replace("$%hash%", '', $file_name)] = $data;
        }
        $json = json_encode($provider);
        file_put_contents($this->repository_path . "/packages.json", $json);

        foreach ($provider_includes as $file_name => $data) {
            $file = str_replace("%hash%", $data['sha256'], $file_name);
            $provider_file_path = $this->repository_path . '/' . $file;
            $static_file_path = $this->repository_path . '/' . str_replace("$%hash%.json", '', $file_name) .'.json';

            if (hash_file('sha256',$static_file_path) == $data['sha256']) {
                echo "skip $file\r\n";
                continue;
            }

            $json_url = $this->packagist_url ."/". $file;
            $json = HttpSendLong($json_url);
            file_put_contents($provider_file_path, $json->raw_body);
            unlink($static_file_path);
            rename($provider_file_path,$static_file_path);

            echo $file ."  TO  ".$static_file_path."\r\n";
        }
    }

    public function getjson(){

        $file = new \League\Flysystem\File();

        $fs = new \League\Flysystem\Adapter\Local(__DIR__.'/');
        $filesystem = new \League\Flysystem\Filesystem($fs);
        $filesystem->get($this->repository_path.'/packages.json',$file);
        $data = $file->read();
        $data = json_decode($data,TRUE);
        $providers = array_keys($data['provider-includes']);

         foreach($providers as $provider){
             $filesystem->get($this->repository_path.'/'.$provider,$file);
             $provider_json = $file->read();
             $provider_array = json_decode($provider_json,TRUE);

             $packages = array_keys($provider_array['providers']);


             $array = array();
             foreach($packages as $package){
                 $array[$package] = str_replace("%package%",$package,$this->package_url);
                 $array[$package] = str_replace("%hash%",$provider_array['providers'][$package]['sha256'],$array[$package]);
             }
             foreach($array as $package => $json_url){

                 $composer_url = $json_url;
                 $package_path = str_replace("%package%",$package,$this->package_path2);
//echo $package_path." ".hash_file('sha256',$package_path)." ".$provider_array['providers'][$package]['sha256'];exit;
                 if(hash_file('sha256',$package_path) == $provider_array['providers'][$package]['sha256'] ){
                     echo "Skip ".$package ."\r\n";
                     continue;
                 }


                 try{
unlink($package_path);
echo $composer_url;
                     $result = HttpSend($composer_url);
                     $filesystem->get($package_path,$file);
                     $file->write($result->raw_body);
                     echo  "Fetching ".$composer_url. "\r\n";


                 }catch (exception $e){
                     echo "Error ".$package ."\r\n";
                 }

             }
         }

    }
}

//BuildComposer::object()->init();
BuildComposer::object()->getjson();
