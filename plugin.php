<?php
class autoUpdater extends Plugin
{
	public function form()
	{
		return '<div id="autoupdater_dynamic_content"></div>';
	}
	public function adminSidebar()
	{
		global $L;
		$html = '<p id="AUTOUPDATER-current-version">Version <span class="badge badge-warning">' . BLUDIT_VERSION . '</span></p>';
		$html .= '<a id="AUTOUPDATER-new-version" style="display: none;" href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/autoUpdater">' . $L->get('New version available<br><button class="btn btn-outline-primary">Update now! 🚀</button>') . '</a>';
		$html .= '<p id="AUTOUPDATER-newest-version" style="display: none;">' . $L->get('This is the newest version 🔥') . '</p>';
		return $html;
	}
	public function adminBodyEnd()
	{
		return '<script>' . file_get_contents($this->phpPath() . DS . 'main.js') . '</script>';
	}
}
