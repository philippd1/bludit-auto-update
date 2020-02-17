<?php
$AUTOUPDATE_statusfile = dirname(__FILE__) . '/status.json';
function clean_status()
{
    global $AUTOUPDATE_statusfile;
    unlink($AUTOUPDATE_statusfile);
}
function update_status($content)
{
    global $AUTOUPDATE_statusfile;
    $myfile = fopen($AUTOUPDATE_statusfile, "a") or die("Unable to open file!");
    fwrite($myfile, $content . "\n");
    fclose($myfile);
}
function download($url, $filename)
{
    $content = file_get_contents($url);
    $file = fopen($filename, 'w+');
    fwrite($file, $content);
    fclose($file);
}
function unzip($filename, $extract_location)
{
    $zip = new ZipArchive;
    $res = $zip->open($filename);
    if ($res === true) {
        $zip->extractTo($extract_location);
        $zip->close();
    } else {
        echo 'Error: Unzip failed';
    }
}
function xcopy($source, $dest)
{
    if (is_file($source)) {
        // return copy($source, $dest);
        update_status("copy {$source} to {$dest}");
        // $myfile = fopen($dest, "w") or die("Unable to open file!");
        // fwrite($myfile, file_get_contents($source));
        // fclose($myfile);
        return true;
    }
    if (!is_dir($dest)) {
        mkdir($dest);
    }

    $path = "/path/to/files";

    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ('.' === $file) continue;
            if ('..' === $file) continue;

            // do something with the file
        }
        closedir($handle);
    }
    return true;
}
// 
if (isset($_POST['action_download'])) {
    download($_POST['url'], 'bludit-new.zip');
    update_status("newest Bludit downloaded @" . time());
    echo "download-done";
}
// 
elseif (isset($_POST['action_unzip'])) {
    update_status("unzipping @" . time());
    if (!file_exists("auto-updater-temp")) {
        mkdir('auto-updater-temp');
    }
    unzip('bludit-new.zip', dirname(__FILE__) . '/auto-updater-temp/');
    echo "unzip-done";
    update_status("unzipping done @" . time());
}
// 
elseif (isset($_POST['action_update_language'])) {
    update_status("update_language @" . time());

    $dest_dir = $_POST['HTML_PATH_ROOT'] . "/bl-languages/";

    $files = glob($destination . '*'); // get all file names
    foreach ($files as $file) { // iterate files
        unlink($dest_dir . $file); // delete file
    }

    $src = $_POST['HTML_PATH_ROOT'] . 'auto-updater-temp/' . $_POST['tag'] . '/bl-languages';
    $destination = $_POST['HTML_PATH_ROOT'] . 'blang';
    update_status("try copying {$src} to {$destination}");

    $lang_folder = './auto-updater-temp/' . $_POST['tag'] . '/bl-languages/';
    $files = scandir($lang_folder);
    foreach ($files as $file) {
        $myfile = fopen($dest, "w") or die("Unable to open file!");
        fwrite($myfile, file_get_contents($lang_folder . $file));
        fclose($myfile);
    }
    echo "action_update_language-done";
    update_status("update_language done @" . time());
}
// 
elseif (isset($_POST['action_update_kernel'])) {
    update_status("update_kernel @" . time());

    echo "action_update_kernel-done";
    update_status("update_kernel done @" . time());
}
// 
elseif (isset($_POST['cleanup'])) {
    update_status("cleanup @" . time());

    echo "cleanup-done";
    update_status("cleanup done @" . time());
    update_status("Bludit was successfully upgraded to " . $_POST['tag'] . " @" . time());
}
// ---
elseif (isset($_POST['action_init'])) {
    clean_status();
    update_status("update started @" . time());
    if (!empty($_POST['url'])) {
        update_status("URL '{$_POST['url']}' received @" . time());
        update_status("downloading '{$_POST['url']}' started @" . time() . ". This could take a moment...");
        echo "init-done";
    } else {
        update_status("error: url empty");
    }
}
// /*update kernel files - start*/
// $rootPath = realpath('./auto-updater-temp/' . $bluditVersionDownloaded . '/bl-kernel/');
// $zip = new ZipArchive();
// $zip->open('./bl-kernel/temp.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
// $files = new RecursiveIteratorIterator(
//     new RecursiveDirectoryIterator($rootPath),
//     RecursiveIteratorIterator::LEAVES_ONLY
// );
// foreach ($files as $name => $file) {
//     if (!$file->isDir()) {
//         $filePath = $file->getRealPath();
//         $relativePath = substr($filePath, strlen($rootPath) + 1);
//         $zip->addFile($filePath, $relativePath);
//     }
// }
// $zip->close();
// unzip('./bl-kernel/temp.zip', './bl-kernel/');
// unlink('./bl-kernel/temp.zip');
// /*update kernel files - end*/

// //clean up
// // unlink('backup.zip');
// function rmrf($dir)
// {
//     foreach (glob($dir) as $file) {
//         if (is_dir($file)) {
//             rmrf("$file/*");
//             rmdir($file);
//         } else {
//             unlink($file);
//         }
//     }
// }
// rmrf('./auto-updater-temp/');


// echo "<br>Bludit was updated to '{$bluditVersionDownloaded}'.";
