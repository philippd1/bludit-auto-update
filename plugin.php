<?php
class autoUpdater extends Plugin{
	public function init(){
		$this->formButtons = false;
	}
	public function form(){
		$html = '<label>This plugin automatically downloads and installs the latest Bludit version offered on the official Bludit site.<br><b>Warning:</b> This will overwrite your bludit installation.</label>';
		
		$html .= '<br>Current Bludit Version: '.BLUDIT_VERSION.'<br>Current Bludit Build: '.BLUDIT_BUILD.'<br>';

		// get latest version
		
		$opts = ['http' => ['method' => 'GET','header' => ['User-Agent: PHP']]];
		$context = stream_context_create($opts);
		$content = file_get_contents("https://version.bludit.com/", false, $context);
		$html .= "[".$content."]";//NOTHING


		$html .= "<br>";
		
		$content = file_get_contents("https://version.bludit.com/");
		$html .= "[".$content."]";//NOTHING
		
		$html .= "<br>";

		// compare installed build to latest build

		$html .= '<a class="btn btn-primary">update Bludit now</a>';//only show this if bludit version is old
		
		return $html;
	}
}
?>