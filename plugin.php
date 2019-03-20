<?php
class autoUpdater extends Plugin{
	public function init(){
		$this->formButtons = false;
		$this->dbFields = array(
			'auto_update_on_off'=>false
		);
	}
	public function form(){
		$html = '<label>This plugin automatically downloads and installs the latest Bludit version offered on the official Bludit site.<br><b>Warning:</b> This will overwrite your bludit installation.</label>';
		
		$html .= '<br>Current Bludit Version: '.BLUDIT_VERSION.'<br>Current Bludit Build: '.BLUDIT_BUILD.'<br>';
		
		$versionJSON = file_get_contents("https://version.bludit.com");
		$versionJSON = json_decode($versionJSON);
		$newest_build = $versionJSON->stable->build;
		
		if(BLUDIT_BUILD < $newest_build){
			include("update-bludit.php");
			$html .= "Bludit needs update";
		} else {
			$html .= <<<EOF
			<div class="alert alert-success" role="alert"><strong>up to date</strong><br>Your current Bludit installation is up to date</div>
			EOF;
		}		
		return $html;
	}
}
?>