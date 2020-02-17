<?php
class autoUpdater extends Plugin
{
	public function form()
	{
		$html  = '';
		$html .= <<<Bludit_Updater
		<div id="autoupdater_dynamic_content"></div>
		Bludit_Updater;
		return $html;
	}
	public function adminSidebar()
	{
		global $L;
		$html = '<p id="AUTOUPDATER-current-version">Version ' . (defined('BLUDIT_PRO') ? '<span style="color: #ffc107"></span>' : '') . '<span class="badge badge-warning">' . BLUDIT_VERSION . '</span></p>';
		$html .= '<a id="AUTOUPDATER-new-version" style="display: none;" href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/autoUpdater">' . $L->get('New version available<br><button class="btn btn-outline-primary">Update now! 🚀</button>') . '</a>';
		return $html;
	}
	public function adminBodyEnd()
	{
		return '<script>' . file_get_contents($this->phpPath() . DS . 'main.js') . '</script>';
	}
}
