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
function rmrf($dir)
{
    foreach (glob($dir) as $file) {
        if (is_dir($file)) {
            rmrf("$file/*");
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}
if (isset($_POST['action_download'])) {
    download($_POST['url'], 'bludit-new.zip');
    update_status("newest Bludit downloaded @" . time());
    echo "download-done";
} elseif (isset($_POST['action_unzip'])) {
    update_status("unzipping @" . time());
    if (!file_exists("auto-updater-temp")) {
        mkdir('auto-updater-temp');
    }
    unzip('bludit-new.zip', dirname(__FILE__) . '/auto-updater-temp/');
    echo "unzip-done";
    update_status("unzipping done @" . time());
} elseif (isset($_POST['action_update_language'])) {
    update_status("update_language @" . time());
    $files_array = scandir('./auto-updater-temp/bludit-' . $_POST['tag'] . '/bl-languages');
    foreach ($files_array as $file) {
        if ($file != "." && $file != "..") {
            $myfile = fopen('../../bl-languages/' . $file, "w") or die("Unable to open file!");
            fwrite($myfile, file_get_contents('./auto-updater-temp/bludit-' . $_POST['tag'] . '/bl-languages/' . $file));
            fclose($myfile);
        }
    }
    echo "action_update_language-done";
    update_status("update_language done @" . time());
} elseif (isset($_POST['action_update_kernel'])) {
    update_status("update_kernel @" . time());
    $source_path = realpath('./auto-updater-temp/bludit-' . $_POST['tag'] . '/bl-kernel/');
    $zip = new ZipArchive();
    $zip->open('./bl-kernel-temp.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source_path),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source_path) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();
    unzip('./bl-kernel-temp.zip', '../../bl-kernel/');
    unlink('./bl-kernel-temp.zip');

    echo "action_update_kernel-done";
    update_status("update_kernel done @" . time());
} elseif (isset($_POST['cleanup'])) {
    update_status("cleanup @" . time());
    echo "cleanup-done";
    rmrf('./auto-updater-temp/');
    rmrf('./bludit-new.zip');
    update_status("cleanup done @" . time());
    update_status("Bludit was successfully upgraded to " . $_POST['tag'] . " @" . time());
} elseif (isset($_POST['action_init'])) {
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
