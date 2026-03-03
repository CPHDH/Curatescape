<?php
class Curatescape_View_Helper_HookAdminDashboard extends Zend_View_Helper_Abstract{
	public function HookAdminDashboard($view=null, $html = null){
		return $this;
	}
	public function dashboardWidgets(){
		$cache = get_view()->CuratescapeCache();
		$cacheDuration = 3600 * 168; // 168 hours (1 week)
		// recent tours
		echo $this->displayDashboardTours();
		// content audit (cache cleared on item save)
		if(option('curatescape_dashboard_audit')){
			echo $this->displayDashboardAudit($cache,$cacheDuration);
		}
		// file stats and info (cache cleared on item save)
		if(option('curatescape_dashboard_stats')){
			echo $this->displayDashboardStats($cache,$cacheDuration);
		}
		// project management
		if(option('curatescape_dashboard_project_mgmt')){
			echo $this->displayDashboardProjectManagement();
		}
		// resources
		if(option('curatescape_dashboard_resources')){
			echo $this->displayDashboardResources();
		}
	}
	public function refreshDashboardWidgets($cache){
		// called from HookAfterSaveItem or via Job Dispatcher
		// content audit contains admin URLs — skip in CLI context where router base URL is wrong
		if(option('curatescape_dashboard_audit') && PHP_SAPI !== 'cli'){
			$html = $this->generateDashboardWarnings();
			if($html) {
				$cache->WriteCacheFile(_HTML_DASHBOARD_CONTENT_AUDIT_, $html, true);
			}
		}
		if(option('curatescape_dashboard_stats')){
			$fileStats = $this->generateDashboardFilesStats();
			if($fileStats) {
				$html = $this->formatFilesSummary($fileStats);
				if($html) {
					$cache->WriteCacheFile(_HTML_DASHBOARD_FILE_STATS_, $html, true);
				}
			}
		}
		return $this;
	}
	private function displayDashboardAudit($cache,$cacheDuration,$html = null){
		if (
			$cacheFile = $cache->GetCacheFile(
				_HTML_DASHBOARD_CONTENT_AUDIT_, $cacheDuration, false)
		) {
			return $cacheFile;
		} else {
			$html = $this->generateDashboardWarnings();
			if($html) {
				$cache->WriteCacheFile(_HTML_DASHBOARD_CONTENT_AUDIT_, $html);
			}
			return $html;
		}
	}
	private function displayDashboardStats($cache,$cacheDuration,$html = null){
		if (
			$cacheFile = $cache->GetCacheFile(
				_HTML_DASHBOARD_FILE_STATS_, $cacheDuration, false)
		) {
			return $cacheFile;
		} else {
			if($fileStats = $this->generateDashboardFilesStats()){
				$html = $this->formatFilesSummary($fileStats);
				if($html) {
					$cache->WriteCacheFile(_HTML_DASHBOARD_FILE_STATS_, $html);
				}
				return $html;
			}
		}
	}
	private function displayDashboardProjectManagement($html = null)
	{
		if(
			option('curatescape_app_android') ||
			option('curatescape_app_ios')
		){
			$html .= '<section class="panel five columns curatescape-panel">';
				$html .= '<h2>'.__('Project Management').'</h2>';
				$ga = get_theme_option('google_analytics'); // theme option
				$ma = get_option('matomoURL'); // Matomo plugin
				if($ga || $ma){
					$html .= '<h3>'.__('Analytics').'</h3>';
					$html .= '<p>'.__('Track website usage, user demographics, campaign performance, and more.').' </p>';
					if($ga){
						$html .= '<a target="_blank" class="appstore blue button" href="https://analytics.google.com/analytics/web">'.__('Google Analytics').'</a>';
					}
					if($ma){
						$html .= '<a target="_blank" class="appstore blue button" href="'.$ma.'">'.__('Matomo Analytics').'</a>';
					}
				}
				if(option('curatescape_app_android') || option('curatescape_app_ios')){
					$html .= '<h3>'.__('App Stores').'</h3>';
					$html .= '<p>'.__('Manage your app store profiles, track downloads, and more. <a target="_blank" href="https://curatescape.org/docs/project-launch-guide/#analytics">Learn more about app management</a>.').' </p>';
					if(option('curatescape_app_ios')){
						$html .= '<a target="_blank" class="appstore blue button" href="https://appstoreconnect.apple.com/login">'.__('App Store Connnect').'</a>';
					}
					if(option('curatescape_app_android')){
						$html .= '<a target="_blank" class="appstore blue button" href="https://play.google.com/console/developers">'.__('Google Play Console').'</a>';
					}
				}
			$html .= '</section>';
		}
		return $html;
	}
	private function displayDashboardTours($html = null)
	{
		$db = get_db();
		$table = $db->getTable('CuratescapeTour');
		$select = $table->getSelect();
		$results = $table->fetchObjects($select);
		$tourItems = null;
		for($i=0;$i<=10;$i++){
			if(array_key_exists($i, $results) && is_object($results[$i])){
				$tourItems .= '<p class="recent">';
					$tourItems .= '<a href="/admin/tours/show/'.$results[$i]->id.'">'
					.$results[$i]->title.'</a>';
				$tourItems .= '</p>';
				$tourItems .= '<p class="dash-edit">';
					$tourItems .= '<a href="/admin/tours/edit/'.$results[$i]->id.'">'
					.__('Edit').'</a>';
				$tourItems .= '</p>';
			}
		}
		if($tourItems){
			$html .= '<section class="panel five columns omega">';
			$html .= '<h2>'.__('Recent Tours').'</h2>';
			$html .= '<div class="recent-row">';
			$html .= ''.$tourItems.'';
			$html .= '</div>';
			$html .= '<div class="add-new-link"><p><a class="add-tour green button" href="'.html_escape(url('tours/add/')).'">'.__('Add a new tour').'</a></p></div>';
			$html .= '</section>';
			return $html;
		}
	}
	private function displayDashboardResources($html = null)
	{
		$html .= '<section class="panel five columns curatescape-panel">';
			$html .= '<h2>'.__('Curatescape Resources').'</h2>';
			$html .= '<h3>'.__('Documentation').'</h3>';
			$html .= '<p>'.__('For detailed information on setup, deployment, and usage, please visit %s or contact your project manager.', '<a target="_blank" href="https://curatescape.org/docs/">curatescape.org/docs</a>').'</p>';
			$html .= '<h3>'.__('Support & Troubleshooting').'</h3>';
			$html .= '<p>'.__('Join the %s to request support, suggest features, share tips, and more.', '<a target="_blank" href="https://forum.curatescape.org/">Curatescape forum</a>').'</p>';
			$html .= '<h3>'.__('News & Announcements').'</h3>';
			$html .= '<p>'.__('Sign up for the %1s and visit the %2s for occasional tips and project updates.', '<a target="_blank" href="https://curatescape.us6.list-manage.com/subscribe?u=597554b8203b974d59fc51d5f&id=844240ad4f">Curatescape newsletter</a>', '<a target="_blank" href="https://curatescape.org/blog/">Curatescape blog</a>').' </p>';
			$html .= '<h3>'.__('Developers & Server Admins').'</h3>';
			$html .= '<p>'.__('Watch the %s and configure custom notifications for new releases. Pull requests are welcome.', '<a target="_blank" href="https://github.com/CPHDH/Curatescape">Curatescape Github repository</a>').' </p>';
		$html .= '</section>';
		return $html;
	}
	private function generateDashboardWarnings($html = null, $listIssues = array())
	{
		$itemType=get_record('ItemType', array('name'=>_CURATESCAPE_ITEM_TYPE_NAME_));
		if(!$itemType) return null;
		$items = get_records('Item', array('public'=>true,'type'=>$itemType->id), 0);
		if(!$items) return null;
		// missing file meta?
		$missingFileMeta = array_filter(array_map(function($item){
			foreach($item->getFiles() as $file){
				if(dc($file,'Title', array('no_filter'=>true)) == null){ // does the file have at least a title?
					return $item->id;
				}
			}
		}, $items));
		if(count($missingFileMeta)){
			$listIssues[] = $this->formatIssueText(
				$missingFileMeta, 
				array('range'=>''.implode(',', $missingFileMeta)),
				__('%1s %2s with missing File Metadata',
					count($missingFileMeta),
					__(plural('item', 'items', count($missingFileMeta)))
				),
				__('Each file should have a title and other metadata to meet accessibility standards and provide important context for end users. File metadata is used to generate captions for all media files, as well as alt text for images.')
			);
		}
		// no images?
		$noImages = array_filter(array_map(function($item){
			if(!$item->hasThumbnail()){
				return $item->id;
			}else{
				try { 
					$img = record_image($item); 
				} catch (InvalidArgumentException $e) { 
					// record_image() can throw in CLI/background context when resolving fallback paths
					$img = 'fallback'; 
				}
				if(strpos($img, 'fallback') !== false){
					// has thumb but first file is a non-image, check if others exist
					if(!preferredItemImageUrl($item)){
						return $item->id;
					}
				}
			}
		}, $items));
		if(count($noImages)){
			$listIssues[] = $this->formatIssueText(
				$noImages, 
				array('range'=>''.implode(',', $noImages)),
				__('%1s %2s with no Thumbnail Image',
					count($noImages),
					__(plural('item', 'items', count($noImages)))
				),
				__('Each item should have at least one image file to help attract user interest and improve the overall user experience.')
			);
		}
		// no map location?
		$noMap = array_filter(array_map(function($item){
			if(!hasLocation($item)){
				return $item->id;
			}
		}, $items));
		if(count($noMap)){
			$listIssues[] = $this->formatIssueText(
				$noMap, 
				array('geolocation-mapped'=>'0'),
				__('%1s %2s with no Map Location',
					count($noMap),
					__(plural('item', 'items', count($noMap)))
				),
				__('Items without a map location will not be included in Curatescape mobile apps.')
			);
		}
		// no tags?
		$noTags = array_filter(array_map(function($item){
			if(!$item->getTags()){
				return $item->id;
			}
		}, $items));
		if(count($noTags)){
			$listIssues[] = $this->formatIssueText(
				$noTags,
				array('range'=>''.implode(',', $noTags)),
				__(
					'%1s %2s with no Tags',
					count($noTags),
					__(plural('item', 'items', count($noTags)))
				),
				__('Tags help users to discover related content and filter content by topic, and may be a featured aspect of your theme design.')
			);
		}
		// no subjects?
		$noSubjects = array_filter(array_map(function($item){
			if(dc($item,'Subject', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noSubjects)){
			$listIssues[] = $this->formatIssueText(
				$noSubjects,
				array('range'=>''.implode(',', $noSubjects)),
				__(
					'%1s %2s with no Subject term',
					count($noSubjects),
					__(plural('item', 'items', count($noSubjects)))
				),
				__('Subjects help users to discover related content and filter content by topic, and may be a featured aspect of your theme design. Using a controlled vocabulary is strongly recommended and can be managed using the Simple Vocab plugin.')
			);
		}
		// no creator?
		$noCreator = array_filter(array_map(function($item){
			if(dc($item,'Creator', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noCreator)){
			$listIssues[] = $this->formatIssueText(
				$noCreator,
				array('range'=>''.implode(',', $noCreator)),
				__(
					'%1s %2s with no Creator',
					count($noCreator),
					__(plural('item', 'items', count($noCreator)))
				),
				__('Items that are not attributed to an author may not feel trustworthy to end users.')
			);
		}
		// no story?
		$noStory = array_filter(array_map(function($item){
			if(itm($item,'Story', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noStory)){
			$listIssues[] = $this->formatIssueText(
				$noStory,
				array('range'=>''.implode(',', $noStory)),
				__(
					'%1s %2s with no Story text',
					count($noStory),
					__(plural('item', 'items', count($noStory)))
				),
				__('The story is the most fundamental element of a %s item and should be used for every item.', _CURATESCAPE_ITEM_TYPE_NAME_)
			);
		}
		// no subtitle?
		$noSubtitles = array_filter(array_map(function($item){
			if(itm($item,'Subtitle', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noSubtitles)){
			$listIssues[] = $this->formatIssueText(
				$noSubtitles,
				array('range'=>''.implode(',', $noSubtitles)),
				__(
					'%1s %2s with no Subtitle',
					count($noSubtitles),
					__(plural('item', 'items', count($noSubtitles)))
				),
				__('The Subtitle is recommended to add additional interest, context, and detail to the item title.')
			);
		}
		// no lede?
		$noLede = array_filter(array_map(function($item){
			if(itm($item,'Lede', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noLede)){
			$listIssues[] = $this->formatIssueText(
				$noLede,
				array('range'=>''.implode(',', $noLede)),
				__(
					'%1s %2s with no Lede',
					count($noLede),
					__(plural('item', 'items', count($noLede)))
				),
				__('The Lede is recommended as an effective way to draw in the reader and may also be used as preview text in certain contexts such as tours.')
			);
		}
		// no address?
		$noAddress = array_filter(array_map(function($item){
			if(hasLocation($item) && itm($item,'Street Address', array('no_filter'=>true)) == null){
				return $item->id;
			}
		}, $items));
		if(count($noAddress)){
			$listIssues[] = $this->formatIssueText(
				$noAddress,
				array('range'=>''.implode(',', $noAddress)),
				__(
					'%1s %2s with no Street Address',
					count($noAddress),
					__(plural('item', 'items', count($noAddress)))
				),
				__('If an item has a map location, it should also have a street address. The street address provides text-equivalent content for visually impaired users and may also be used to enhance the marker info window in the maps provided by your theme and/or in the Curatescape mobile apps.')
			);
		}
		if(count($listIssues)){
			$messageTitle = __('Potential Issues Detected');
			$messageText = __('Use the links to view items with potential content issues. Changes are recommended but not required.');
			$messageIcon = svg('warning');
			$messageClass = 'warning';
			sort($listIssues);
		}else{
			$messageTitle = __('No Issues Detected');
			$messageText = __('Great job. No recommended changes at this time.');
			$messageIcon = svg('ribbon');
			$messageClass = 'success';
		}
		$html .= '<section class="panel five columns curatescape-panel">';
			$html .= '<h2>'.__('Content Audit').'</h2>';
			$html .= '<h3>'.__('Results').'</h3>';
			$html .= '<p>'.__('Content audit results apply only to published items that use the %s item type.', '<em>'._CURATESCAPE_ITEM_TYPE_NAME_.'</em>').'</p>';
			$html .= '<span class="highlight '.$messageClass.'">';
				$html .= '<h3>'.$messageIcon.$messageTitle.'</h3>';
				$html .= '<p>'.$messageText.'</p>';
				$html .= count($listIssues) ? '<ul>'.implode('', $listIssues).'</ul>' : null;
			$html .= '</span>';
		$html .= '</section>';
		return $html;
	}
	private function generateDashboardFilesStats($html = null, $totalFiles = 0, $images = 0, $audio = 0, $video = 0, $docs = 0, $other = 0){
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
		$totalItems = total_records('Item');
		$html .= '<p>'.__('Your files directory contains <strong>%1s original files</strong> for an average of about <strong>%2s files per item</strong>. Below is a break down of files by type:', number_format($totalFiles), $totalItems ? round($totalFiles/$totalItems, 1) : 0).'</p>';
		$html .= '<ul>';
			$html .= $images ? '<li>'.__('Images').': '.number_format($images).'</li>' : null;
			$html .= $audio ? '<li>'.__('Audio').': '.number_format($audio).'</li>' : null;
			$html .= $video ? '<li>'.__('Video').': '.number_format($video).'</li>' : null;
			$html .= $docs ? '<li>'.__('Documents').': '.number_format($docs).'</li>' : null;
			$html .= $other ? '<li>'.__('Other').': '.number_format($other).'</li>' : null;
		$html .= '</ul>';
		return $html;
	}
	private function formatFilesSummary($fileStats, $html = null)
	{
		$html .= '<section class="panel five columns curatescape-panel">';
			$html .= '<h2>'.__('File Information').'</h2>';
			$html .= '<span>';
				$html .= '<h3>'.__('Recommended Formats').'</h3>';
				$html .= '<p>'.__('To maximize compatibility with all app platforms and web browsers, the following file formats are recommended:').'</p>';
				$html .= '<ul>';
					$html .= '<li>'.__('Images').': JPG or PNG</li>';
					$html .= '<li>'.__('Audio').': MP3</li>';
					$html .= '<li>'.__('Video').': MP4 (H.264)</li>';
					$html .= '<li>'.__('Documents').': PDF</li>';
				$html .= '</ul>';
			$html .= '</span>';
			$html .= '<span class="highlight info">';
				$html .= '<h3>'.svg('bookmark').__('Statistics').'</h3>';
				$html .= $fileStats;
			$html .= '</span>';
		$html .= '</section>';
		return $html;
	}
	private function formatIssueText($recordIds, $queryParams, $string, $context = null){
		$url = admin_url('items/browse?' . http_build_query($queryParams));
		$title = __('View affected %s', __(plural('item', 'items', count($recordIds))));
		return '<li data-count="'.sprintf('%05d',count($recordIds)).'"><a title="'.$title.'" href="'.$url.'">'.$string.'</a><span title="'.$context.'">'.svg('information-circle').'</span></li>';
	}
}