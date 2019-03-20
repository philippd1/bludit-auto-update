<?php
/*  ENTIRE BACKUP - START  */
// $rootPath = realpath('.');
// $zip = new ZipArchive();
// $zip->open('backup.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
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
/*  ENTIRE BACKUP - END    */
function download($url, $filename){
    $content = file_get_contents($url);
    $file = fopen($filename, 'w+');
    fwrite($file, $content);
    fclose($file);
}
function unzip($filename, $unzip_location){
    $zip = new ZipArchive;
    $res = $zip->open($filename);
    if ($res === true) {
        $zip->extractTo($unzip_location);
        $zip->close();
    }
}
// ======== START UPDATE
// find, download and unzip bludit
$daten = file_get_contents("http://bludit.com/de/");
preg_match_all("!<a .*?href=\"([^\"]*\.zip)\"[^>]*>(.*?)</a>!", $daten, $found);
$bluditZIPLocation = $found[1][0];
download($bluditZIPLocation, 'bludit-new.zip');
// https://github.com/bludit/bludit/archive/master.zip
mkdir('auto-updater-temp');
unzip('bludit-new.zip', './auto-updater-temp/');
unlink('bludit-new.zip');
$bluditVersionDownloaded = null;
if ($handle = opendir('./auto-updater-temp')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $bluditVersionDownloaded = $entry;
        }
    }
    closedir($handle);
}
/*update language files - start*/
$rootPath = realpath('./auto-updater-temp/' . $bluditVersionDownloaded . '/bl-languages/');
$zip = new ZipArchive();
$zip->open('./bl-languages/temp.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $zip->addFile($filePath, $relativePath);
    }
}
$zip->close();
unzip('./bl-languages/temp.zip', './bl-languages/');
unlink('./bl-languages/temp.zip');
/*update language files - end*/
/*update kernel files - start*/
$rootPath = realpath('./auto-updater-temp/' . $bluditVersionDownloaded . '/bl-kernel/');
$zip = new ZipArchive();
$zip->open('./bl-kernel/temp.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $zip->addFile($filePath, $relativePath);
    }
}
$zip->close();
unzip('./bl-kernel/temp.zip', './bl-kernel/');
unlink('./bl-kernel/temp.zip');
/*update kernel files - end*/
//clean up
// unlink('backup.zip');
function rmrf($dir){
    foreach (glob($dir) as $file) {
        if (is_dir($file)) {
            rmrf("$file/*");
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}
rmrf('./auto-updater-temp/');
?>