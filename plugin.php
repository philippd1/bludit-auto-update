<?php
class autoUpdater extends Plugin
{
	public function form()
	{
		$html  = '';
		$html .= <<<Bludit_Updater
		<div id="autoupdater_dynamic_content"></div>
		Bludit_Updater;
		$js = file_get_contents(dirname(__FILE__) . '/main.js');
		$html .= <<<Bludit_Updater
		<script>{$js}</script>
		Bludit_Updater;
		return $html;
	}
}
