<?php
class autoUpdater extends Plugin
{
	public function form()
	{
		global $Language;
		include 'auto_update.php';
		return '<label>This plugin automatically downloads and installs the latest Bludit version offered on the official Bludit site.<br><b>Warning:</b> This will overwrite your bludit installation.</label>';
	}
}
?>