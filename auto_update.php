<?php
$error_counter = 0;
$zip_download_url = "https://github.com/bludit/bludit/archive/master.zip";

function delete_directory($dirname){
    if(is_dir($dirname)){
        $dir_handle = opendir($dirname);
    }
    if(!$dir_handle){
        echo "directory not found ({$dirname})";
        return  false;
    }
    
    while($file=readdir($dir_handle)){
        if($file!="." && $file!=".."){
            if(!is_dir($dirname."/".$file)){
                @unlink($dirname."/".$file);
            } else {
                delete_directory($dirname.'/'.$file);
            }
        }
    }
    
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

function copyFolder($source, $dest, &$statsCopyFolder, $recursive = false){
    
    if (!is_dir($dest)){
        mkdir($dest);
    }
    
    $handle = @opendir($source);
    
    if(!$handle){
        return false;
    }
    
    while ($file = @readdir ($handle)){
        if (preg_match("/^\.{1,2}$/",$file)){
            continue;
        }
        
        if(!$recursive && $source != $source.$file."/"){
            if(is_dir($source.$file))
            continue;
        }
        
        if(is_dir($source.$file)){
            copyFolder($source.$file."/", $dest.$file."/",
            $statsCopyFolder, $recursive);
        } else {
            copy($source.$file, $dest.$file);
            @$statsCopyFolder['files']++;
            @$statsCopyFolder['bytes'] += filesize($source.$file);
        }
    }
    @closedir($handle);
}

function download($url, $file_name) {
    $datei = $url;
    if ((function_exists('curl_version'))){
        $options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false, CURLOPT_FOLLOWLOCATION => true, CURLOPT_ENCODING=> "", CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 120, CURLOPT_TIMEOUT => 120, CURLOPT_MAXREDIRS => 10, CURLOPT_SSL_VERIFYPEER => false);
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);
    }
    
    else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')){
        if (strpos($datei, 'https') === true){
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $content =  file_get_contents($datei, false, stream_context_create($arrContextOptions));
        } else {
            $content = file_get_contents($datei);
        }
    } else {
        echo 'Error: This plugin does not work on your server. There are neither cUrl nor allow_url_fopen enabled / installed.';
    }
    $dat = fopen($file_name,'w+');
    fwrite($dat,$content);
    fclose($dat);
}

function unzip($file_name,$unzip_location){
    $zip = new ZipArchive;
    $res = $zip->open($file_name);
    if ($res === TRUE) {
        $zip->extractTo($unzip_location);
        $zip->close();
    } else {
        echo 'Error: unzip failed';
    }
}

if (!file_exists("temp_bludit_new")){
    mkdir("temp_bludit_new");
}
download($zip_download_url, 'temp.zip');
unzip('temp.zip',HTML_PATH_ROOT."temp_bludit_new");
$error_counter = $error_counter+1;
@unlink('temp.zip');


copyFolder(HTML_PATH_ROOT."temp_bludit_new/".basename($found[1][0],".zip")."/", HTML_PATH_ROOT."/", $statsCopyFolder, $recursive = true);//copy to root folder

delete_directory(HTML_PATH_ROOT."temp_bludit_new");

if ($error_counter == 0) {
    echo '<br>Bludit Update Error: Bludit konnte nicht eingelesen werden.';
} else {
    echo '<br>Bludit updated.';
}

echo '<br>Bludit was updated. When you see Errors or any problems, please report them in the Bludit Forum/ GitHub repository of this plugin.<br><br>';
?>