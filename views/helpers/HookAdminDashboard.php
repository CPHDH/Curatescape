<?php
class Curatescape_View_Helper_HookAdminDashboard extends Zend_View_Helper_Abstract{
	public function HookAdminDashboard($view, $html = null){
		if(option('curatescape_dashboard_resources')){
			if(option('curatescape_app_android') || option('curatescape_app_ios') || option('curatescape_google_analytics')){
				$html .= '<section class="panel five columns curatescape-panel">';
					$html .= '<h2>'.__('Project Management').'</h2>';
					// analytics
					if(option('curatescape_google_analytics')){
						$html .= '<h3>'.__('Analytics').'</h3>';
						$html .= '<p>'.__('Use Google Analytics to track website usage, user demographics, campaign performance, and more. <a target="_blank" href="https://curatescape.org/docs/project-launch-guide/#analytics">Learn more about website analytics</a>.').' </p>';
						$html .= '<a target="_blank" class="appstore blue button" href="https://analytics.google.com/analytics/web">'.__('Google Analytics').'</a>';
					}
					// app stores
					if(option('curatescape_app_android') || option('curatescape_app_ios')){
						$html .= '<h3>'.__('App Stores').'</h3>';
						$html .= '<p>'.__('Use the links below to manage your app store profiles, track downloads, and more. <a target="_blank" href="https://curatescape.org/docs/project-launch-guide/#analytics">Learn more about app management</a>.').' </p>';
						if(option('curatescape_app_ios')){
							$html .= '<a target="_blank" class="appstore blue button" href="https://appstoreconnect.apple.com/login">'.__('App Store Connnect').'</a>';
						}
						if(option('curatescape_app_android')){
							$html .= '<a target="_blank" class="appstore blue button" href="https://play.google.com/console/developers">'.__('Google Play Console').'</a>';
						}
					}
				$html .= '</section>';	
			}
			if($fileStats = $this->dashboardFileStats()){
				// files
				$html .= '<section class="panel five columns curatescape-panel">';
					$html .= '<h2>'.__('File Information').'</h2>';
					$html .= '<h3>'.__('Statistics').'</h3>';
					$html .= $fileStats;
					// file requirements
					$html .= '<span class="highlight">';
						$html .= '<h3>'.svg('information-circle').__('Recommended Formats').'</h3>';
						$html .= '<p>'.__('To maximize compatibility with all app platforms and web browsers, the following file formats are recommended:').'</p>';
						$html .= '<ul>';
						$html .= '<li>'.__('Images').': JPG or PNG</li>';
						$html .= '<li>'.__('Audio').': MP3</li>';
						$html .= '<li>'.__('Video').': MP4 (H.264)</li>';
						$html .= '<li>'.__('Documents').': PDF</li>';
						$html .= '</ul>';
					$html .= '</span>';
				$html .= '</section>';
			}
			//resources
			$html .= '<section class="panel five columns curatescape-panel">';
				$html .= '<h2>'.__('Curatescape Resources').'</h2>';
				$html .= '<h3>'.__('Documentation').'</h3>';
				$html .= '<p>'.__('For detailed information on setup, deployment, and usage, please visit %s or contact your project manager.', '<a target="_blank" href="https://curatescape.org/docs/">curatescape.org/docs</a>').'</p>';
				$html .= '<h3>'.__('Support & Troubleshooting').'</h3>';
				$html .= '<p>'.__('Join the %s to request support, suggest features, share tips, and more.', '<a target="_blank" href="https://forum.curatescape.org/">Curatescape forum</a>').'</p>';
				$html .= '<h3>'.__('News & Announcements').'</h3>';
				$html .= '<p>'.__('Sign up for the %1s and visit the %2s for occassional tips and project updates.', '<a target="_blank" href="https://curatescape.us6.list-manage.com/subscribe?u=597554b8203b974d59fc51d5f&id=844240ad4f">Curatescape newsletter</a>', '<a target="_blank" href="https://curatescape.org/blog/">Curatescape blog</a>').' </p>';
				$html .= '<h3>'.__('Developers & Server Admins').'</h3>';
				$html .= '<p>'.__('Watch the %s and configure custom notifications for new releases. Pull requests are welcome.', '<a target="_blank" href="https://github.com/CPHDH/Curatescape">Curatescape Github repository</a>').' </p>';
			$html .= '</section>';
		}
		echo $html;
	}
	private function dashboardFileStats($html = null, $totalFiles = 0, $images = 0, $audio = 0, $video = 0, $docs = 0, $other = 0){
		$file_dir = $_SERVER['DOCUMENT_ROOT'].'/files/original/';
		if(!is_dir($file_dir)) return null;
		$dir = opendir($file_dir);
		while ($file = readdir($dir)) {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if ($file == '.' || $file == '..' || in_array($ext, array(null,'','html','htaccess'))) {
				continue;
			}
			elseif(in_array($ext, array('jpg','jpeg','png','apng','gif','tif','tiff','webp','avif','jp2','jfif','pjpeg','pjp'))){
				$images++;
			}
			elseif(in_array($ext, array('mp4','m4v','mov','webm'))){
				$video++;
			}
			elseif(in_array($ext, array('mp3','wav','ogg','aac','flac','m4a'))){
				$audio++;
			}
			elseif(in_array($ext, array('doc','docx','pdf','csv','xls','xlsx','txt','rtf','ppt','pptx'))){
				$docs++;
			}
			else{
				$other++;
			}
			$totalFiles++;
		}
		if(!$totalFiles) return null;
		$html .= '<p>'.__('Your files directory contains <strong>%1s original files</strong> for an average of about <strong>%2s files per item</strong>. Below is a break down by files by type:', number_format($totalFiles), round($totalFiles/total_records('Item'), 1)).'</p>';
		$html .= '<ul>';
			$html .= $images ? '<li>'.__('Images').': '.number_format($images).'</li>' : null;
			$html .= $audio ? '<li>'.__('Audio').': '.number_format($audio).'</li>' : null;
			$html .= $video ? '<li>'.__('Video').': '.number_format($video).'</li>' : null;
			$html .= $docs ? '<li>'.__('Documents').': '.number_format($docs).'</li>' : null;
			$html .= $other ? '<li>'.__('Other').': '.number_format($other).'</li>' : null;
		$html .= '</ul>';
		return $html;
	}
}