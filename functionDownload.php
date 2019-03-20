	<?php
    function downloadAndExtendHTML($url, $file_name, $html) {
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
			$html .= 'Error: This plugin does not work on your server. There are neither cUrl nor allow_url_fopen enabled / installed.';
		}
		$dat = fopen($file_name,'w+');
		fwrite($dat,$content);
		fclose($dat);
		return $html;
	}
    ?>