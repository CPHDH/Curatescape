<?php
class Curatescape_View_Helper_FilterItemNextPrevious extends Zend_View_Helper_Abstract{
	public function FilterItemNextPrevious($nextItem, $previousItem)
	{
		$tourInfo = $this->getTourInfo();

		if(!$tourInfo) return false; // default

		if($nextItem !== null){
			$this->tourNavScript('next', $tourInfo);
			return $tourInfo['nextTourItem'];
		}

		if($previousItem !== null){
			$this->tourNavScript('previous', $tourInfo);
			return $tourInfo['previousTourItem'];
		}
	}

	private function getQueryParams()
	{
		$params = array();
		parse_str($_SERVER['QUERY_STRING'], $params);
		return array_map('intval', $params);
	}

	private function getTourInfo($tourInfo = array())
	{
		$params = $this->getQueryParams();

		if(!$params) return false;

		$tourId = isset($params['tour']) ? $params['tour'] : null;
		$index = isset($params['index']) ? $params['index'] : null;
		$tour = $tourId ? get_record_by_id( 'CuratescapeTour', $tourId ) : null;

		if(!$tour) return false;

		$tourInfo['nextIndex'] = $index +1;
		$tourInfo['previousIndex'] = $index -1;
		$tourInfo['tourId'] = $tourId;
		$tourInfo['tourTitle'] = $this->normalizeText($tour->title);
		$tourInfo['tourURL'] = public_url( 'tours/show/'.$tourId );
		$tourInfo['nextTourItem'] = $tour->getTourItemByIndex($tourInfo['nextIndex'] );
		if($tourInfo['nextTourItem']){
			$tourInfo['nextTourItemURL'] = '/items/show/'.$tourInfo['nextTourItem']->id;
			$tourInfo['nextTourItemTitle'] = $this->normalizeText( $tour->tourItemTitleString($tourInfo['nextTourItem']) );
			$tourInfo['nextTourItemThumb'] = preferredItemImageUrl($tourInfo['nextTourItem'], 'thumbnail');
		}
		$tourInfo['previousTourItem'] = $tour->getTourItemByIndex($tourInfo['previousIndex']);
		if($tourInfo['previousTourItem']){
			$tourInfo['previousTourItemURL'] = '/items/show/'.$tourInfo['previousTourItem']->id;
			$tourInfo['previousTourItemTitle'] = $this->normalizeText($tour->tourItemTitleString($tourInfo['previousTourItem']));
			$tourInfo['previousTourItemThumb'] = preferredItemImageUrl($tourInfo['previousTourItem'], 'thumbnail');
		}
		return $tourInfo;
	}

	private function normalizeText($text)
	{
		return addslashes(htmlspecialchars(strip_tags(trim($text)), ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401));
	}

	private function tourNavScript($nextOrPrevious, $tourInfo)
	{ 
		if($nextOrPrevious == 'next'){
			$match = isset($tourInfo['nextTourItemURL']) ? $tourInfo['nextTourItemURL'] : null;
			$appendIndex = $match ? $tourInfo['nextIndex'] : null;
			$appendTitle = __('%1s: Next %2s', tourLabelString(), storyLabelString());
			$adjacentItem = isset($tourInfo['nextTourItem']) ? $tourInfo['nextTourItem'] : null;
			$tourItemThumb = isset($tourInfo['nextTourItemThumb']) ? $tourInfo['nextTourItemThumb'] : null;
			$tourItemTitle = isset($tourInfo['nextTourItemTitle']) ? $tourInfo['nextTourItemTitle'] : null;
		}

		if($nextOrPrevious == 'previous'){
			$match = isset($tourInfo['previousTourItemURL']) ? $tourInfo['previousTourItemURL'] : null;
			$appendIndex = $match ? $tourInfo['previousIndex'] : null;
			$appendTitle = __(' %1s: Previous %2s', tourLabelString(), storyLabelString());
			$adjacentItem = isset($tourInfo['previousTourItem']) ? $tourInfo['previousTourItem'] : null;
			$tourItemThumb = isset($tourInfo['previousTourItemThumb']) ? $tourInfo['previousTourItemThumb'] : null;
			$tourItemTitle = isset($tourInfo['previousTourItemTitle']) ? $tourInfo['previousTourItemTitle'] : null;
		}

		if(!$match) return null;
	?>
	<script>
	// This will generally be inlined twice due to the way the filter works
	// This is the "<?php echo $nextOrPrevious;?>" version (see FilterItemNextPrevious)
	document.addEventListener('DOMContentLoaded', function() {
		let appendIndex = <?php echo $appendIndex;?>;
		// APPEND PARAMS TO ITEM <?php echo strtoupper($nextOrPrevious);?> LINK
		let containsMatch = document.querySelectorAll("a[href*='<?php echo $match;?>']");
		containsMatch.forEach((link)=>{
			if(appendIndex !== null){
				let withParams = new URL(link.href);
				withParams.searchParams.append('tour', <?php echo $tourInfo['tourId'];?>);
				withParams.searchParams.append('index', appendIndex);
				link.href = withParams;
				link.innerText = '<?php echo $appendTitle;?>';
			}
		});
		// PREPARE <CURATESCAPE-TOUR-NAV> COMPONENT (DEFINED IN CURATESCAPE-TOUR-NAV.JS)
		let body = document.querySelector('body');
		let tourNav = document.querySelector('curatescape-tour-nav') || document.createElement('curatescape-tour-nav');
		if(!tourNav.hasAttribute('tour-nav-container-label')){
			tourNav.setAttribute('tour-nav-container-label', '<?php echo __('Navigation for %s', tourLabelString());?>');
		}
		if(!tourNav.hasAttribute('tour-id')){
			tourNav.setAttribute('tour-id', '<?php echo $tourInfo['tourId'];?>');
		}
		if(!tourNav.hasAttribute('tour-title')){
			tourNav.setAttribute('tour-title', '<?php echo $tourInfo['tourTitle'];?>');
		}
		if(!tourNav.hasAttribute('tour-nav-info-label')){
			tourNav.setAttribute('tour-nav-info-label', '<?php echo __('%s Info', tourLabelString());?>');
		}
		<?php if($nextOrPrevious == 'previous' && $adjacentItem):?>
			tourNav.setAttribute('previous-index', appendIndex);
			tourNav.setAttribute('previous-link-title', '<?php echo $appendTitle;?>');
			tourNav.setAttribute('previous-item-thumb', '<?php echo $tourItemThumb;?>');
			tourNav.setAttribute('previous-item-url', '<?php echo $match;?>');
			tourNav.setAttribute('previous-item-title', '<?php echo $tourItemTitle;?>');
		<?php endif;?>
		<?php if($nextOrPrevious == 'next' && $adjacentItem):?>
			tourNav.setAttribute('next-index', appendIndex);
			tourNav.setAttribute('next-link-title', '<?php echo $appendTitle;?>');
			tourNav.setAttribute('next-item-thumb', '<?php echo $tourItemThumb;?>');
			tourNav.setAttribute('next-item-url', '<?php echo $match;?>');
			tourNav.setAttribute('next-item-title', '<?php echo $tourItemTitle;?>');
		<?php endif;?>
		if(!document.querySelector('curatescape-tour-nav')){
			body.appendChild(tourNav)
		}
	});
	</script>
	<?php }
}