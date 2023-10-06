<?php
/*
** Translations
*/
add_translation_source(dirname(__FILE__) . '/languages');
if(get_theme_option('stealth_mode')=='1' && get_html_lang() !== 'en_US'){
  // disable the translations cache while in stealth mode
  try{
    $cache = Zend_Registry::get('Zend_Translate');
    $cache::clearCache();
  }catch(exception $e){}
}

/*
** Fallback images
*/
add_file_fallback_image('audio', 'ionicons/headset-sharp.svg');
add_file_fallback_image('video', 'ionicons/film-sharp.svg');
add_file_fallback_image('application', 'ionicons/document-text-sharp.svg');
add_file_fallback_image('default', 'ionicons/document-sharp.svg');

/*
** Relabel Search Record Types
*/
add_filter('search_record_types', 'rl_search_record_types');
function rl_search_record_types($recordTypes)
{
   if (plugin_is_active('SimplePages')) {
      $recordTypes['SimplePagesPage'] = __('Page');
   }
   $recordTypes['Item'] = rl_item_label('singular');
   if (plugin_is_active('TourBuilder', '1.6', '>=')) {
      $recordTypes['Tour'] = rl_tour_label('singular');
   }
   return $recordTypes;
}

/*
** Admin Messages
** Set $roles to limit visibility
*/
function rl_admin_message($which=null, $roles=array('admin','super','contributor','researcher','editor','author'))
{
  $icon = rl_icon('warning');
  $title = '<span class="t"><strong>'.__('Admin Notice').'</strong></span>';
  $ps = '<span class="ps">'.__('This message is visible only to site administrators, and only while Stealth Mode is active.').'</span>';
  if (($user=current_user()) && (get_theme_option('stealth_mode')==1)) {
    if (in_array($user['role'], $roles)) {
      switch ($which) {
        case 'items-browse':
          if (intval(option('per_page_public')) % 6 > 0) {
            $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('To ensure the optimal user experience at all screen sizes, please <a href="%s">update your site settings</a> so that the value of <em>Results Per Page (Public)</em> is a number divisible by both 2 and 3 (for example, 12 or 18).', admin_url('appearance/edit-settings')).'</span> '.$ps.'</div></div>';
          }else{
            $html = null;
          }
          break;
        case 'home-featured':
          $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('This section is reserved for Featured Items. <a href="%s">Publish some now</a>.', admin_url('items/browse')).'</span> '.$ps.'</div></div>';
          break;
        
        case 'home-tours':
          $tours_scope = get_theme_option('homepage_tours_scope');
          $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('This section is reserved for %1$s Tours. <a href="%2$s">Publish some now</a>.', ucfirst($tours_scope), admin_url('tours/browse')).'</span> '.$ps.'</div></div>';
          break;
        
        case 'home-recent-random':
          $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('This section is reserved for Recent/Random Items. <a href="%s">Publish some now</a>. Note that Featured Items will be omitted in this section.', admin_url('items/browse')).'</span> '.$ps.'</div></div>';
          break;
        
        case 'home-tags':
          $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('This section is reserved for Tags. <a href="%s">Add some tags to each of your items now</a>.', admin_url('items/browse')).'</span> '.$ps.'</div></div>';   
          break;       
        
        case 'home-cta':
          $html = '<div class="warning message">'.$icon.'<div>'.$title.': <span>'.__('This section is reserved for the Call to Action. Create a Call to Action in <a href="%s">theme settings</a>.', admin_url('themes')).'</span> '.$ps.'</div></div>';   
          break;  
        
        default:
        $html = null;
      }
      return $html;
    }else{
      return null;
    }
  }
}

/*
** Set Default Search Record Types
*/
add_filter('search_form_default_record_types', 'rl_search_form_default_record_types');
function rl_search_form_default_record_types()
{
   $recordTypes=array();
   $recordTypes[]='Item';
   if (plugin_is_active('TourBuilder', '1.6', '>=') && get_theme_option('default_tour_search')) {
      $recordTypes[]='Tour';
   }
   if (plugin_is_active('SimplePages') && get_theme_option('default_page_search')) {
      $recordTypes[]='SimplePagesPage';
   }
   if (get_theme_option('default_file_search')) {
      $recordTypes[]='File';
   }
   return $recordTypes;
}


/*
** Sitewide Search Results
*/
function rl_search_results($records=array(), $html=null)
{
   $filter = new Zend_Filter_Word_CamelCaseToDash();
   $html .= '<div id="result-cards">';
   foreach ($records as $searchText) {
      $type=$searchText['record_type'];
      $class=strtolower($filter->filter($searchText['record_type']));
      $html .= rl_search_result_card($searchText, $type, $class);
   }
   $html .= '</div>';
   return $html;
}
/*
** Sitewide Search Result Card
*/
function rl_search_result_card($searchText=null, $type=null, $class=null)
{
   $record = get_record_by_id($type, $searchText['record_id']);
   $mime= ($type=="File") ? metadata($record, 'MIME Type') : null;
   $mime_label = ($mime) ? ' / '.rl_clean_mime($mime) : null;
   set_current_record($type, $record);
   $html = '<article class="result-card '.$class.'">';
   $icon = rl_icon_name_by_type($type, $mime);
   $html .= '<div class="card-inner">';
   $html .= '<span class="card-label" aria-label="'.__('Result Type').'">'.rl_icon($icon).rl_relabel_type($type).$mime_label.'</span>';
   $html .= '<div class="card-detail">';
   $html .= '<a class="permalink" href="'.record_url($record, 'show').'"><h3 class="title">'.($searchText['title'] ? $searchText['title'] : '[Untitled]').'</h3></a>';
   $description = snippet(rl_search_text($type, $record), 0, 120, '&hellip;');
   $sub = rl_subhead_by_type($type, $record);
   $html .= '<div class="card-preview">';
   $html .= '<span class="search-sub">'.$sub.'</span><p class="search-snip">'.($description ? $description : __('Preview text unavailable.')).'</p>';
   $html .= '</div>';
   $html .= '</div>';
   $html .= '</div>';
   if ($type == 'Item') {
      if ($src = rl_get_first_image_src($record)) {
         $itemimg = '<img src="'.$src.'"/>';
      } elseif (metadata($record, 'has thumbnail') && (!stripos($img, 'ionicons') && !stripos($img, 'fallback'))) {
         $itemimg = item_image('square_thumbnail');
      } else {
         $itemimg = '<img src="'.img('ionicons/custom/blank.svg').'"/>';
      }
      $html .= link_to($record, 'show', $itemimg, array('class' => 'result-image'));
   } elseif ($type == 'Tour') {
      $tourimg = $record->getItems() && $record->getItems()[0]
      ? '<img src="'.rl_get_first_image_src($record->getItems()[0]).'"/>'
      : '<img src="'.img('ionicons/compass-sharp.svg').'"/>';
      $html .= link_to($record, 'show', $tourimg, array('class' => 'result-image'));
   } elseif ($recordImage = record_image($type)) {
      $html .= link_to($record, 'show', $recordImage, array('class' => 'result-image'));
   }
   $html .= '</article>';
   return $html;
}
/*
** Result subhead by type
*/
function rl_subhead_by_type($type=null, $record=null)
{
   switch ($type) {
      case 'Item':
         return strip_tags(rl_the_byline($record, false), '<a>');
      case 'File':
         $parent=get_record_by_id('Item', $record->item_id);
         $title=metadata($parent, array('Dublin Core','Title'));
         return __('This file appears in: %s', link_to($parent, 'show', strip_tags($title)));
      case 'Tour':
         return __('%s Locations', rl_tour_total_items($record));
      case 'Collection':
         return __('%1s %2s', metadata($record, 'total_items'), rl_item_label('plural'));
      default:
         return null;
   }
}
/*
** Icons names by content type
*/
function rl_icon_name_by_type($type=null, $mime=null)
{
   switch ($type) {
      case 'Item':
         $i = 'location';
      break;
      case 'File':
         if ($mime) {
            switch ($mime) {
               case substr($mime, 0, 5) === 'audio':
                  $i = 'headset';
                  break;
               case substr($mime, 0, 5) === 'video':
                  $i = 'film';
                  break;
               case substr($mime, 0, 5) === 'image':
                  $i = 'image';
                  break;
               default:
                  $i = 'document-text';
                  break;
            }
            break;
         }
         $i = 'document-text';
         break;
      case 'Tour':
         $i = 'compass';
         break;
      case 'Collection':
         $i = 'folder';
         break;
      default:
         $i = 'globe';
         break;
   }
   return $i;
}
/*
** Sitewide Search Result Text
*/
function rl_search_text($type=null, $record=null)
{
   switch ($type) {
      case 'Item':
         return rl_the_text($record);
      case 'File':
         return metadata($record, array('Dublin Core', 'Description'), array('no_escape' => true));
      case 'Tour':
         $tour = $record;
         return tour('Description');
      case 'SimplePagesPage':
         return strip_tags(metadata($record, 'text', array('no_escape' => true)));
      case 'Collection':
         return metadata($record, array('Dublin Core', 'Description'), array('no_escape' => true));;
      case 'Exhibit':
         return metadata($record, 'description', array('no_escape' => true));
      case 'ExhibitPage':
         return null;
      default:
         return null;
   }
}
/*
** Get Basic MIME type
*/
function rl_clean_mime($mime=null)
{
   $array = explode('/', $mime);
   return $array[0] !== 'application' ? $array[0] : $array[1];
}
/*
** Normalize Record Type Names
*/
function rl_relabel_type($type=null)
{
   switch ($type) {
      case 'Item':
         return rl_item_label('singular');
      case 'SimplePagesPage':
         return __('Page');
      case 'ExhibitPage':
         return __('Exhibit Page');
      default:
         return $type;
   }
}

/*
** Determine when to load jQuery
** usage: head_js(rl_jquery_whitelist(current_url()))
*/
function rl_jquery_whitelist($current_url=null){
    if(!$current_url) return;
    $whitelist = array(
        '/items/search',
        '/guest-user/',
        '/contribution/',
        '/exhibits/',
        '/neatline/',
        '/users/login',
    );
    foreach($whitelist as $allowed){
        if(0 === strpos($current_url, $allowed)) return true;
    }
    return false;
}

/*
** Remove select plugin/core assets from queue
** view: $this
** paths: array('/plugins/Geolocation','admin-bar','family=Arvo:400')
*/
function rl_assets_blacklist($view=null, $paths=array())
{
   if ($view) {
      $scripts = $view->headScript();
      foreach ($scripts as $key=>$file) {
         foreach ($paths as $path) {
            if(0 === strpos(current_url(), '/exhibits/show') && $path == '/plugins/Geolocation'){
               // do nothing if this is an exhibit (allow map)
            }elseif(0 === strpos(current_url(), '/guest-user/') && $path == '/plugins/GuestUser/views/public/javascripts'){
              // do nothing if this is a guest user page
            }elseif (isset($file->attributes['src']) && strpos($file->attributes['src'], $path) !== false) {
                 $scripts[$key]->type = null;
                 $scripts[$key]->attributes['src'] = null;
                 $scripts[$key]->attributes['source'] = null;
            }
         }
      }
      $styles = $view->headLink();
      foreach ($styles as $key=>$file) {
         foreach ($paths as $path) {
            if(0 === strpos(current_url(), '/exhibits/show') && $path == '/plugins/Geolocation'){
               // do nothing if this is an exhibit (allow map)
            }elseif ($file->href && strpos($file->href, $path) !== false) {
               $styles[$key]->href = null;
               $styles[$key]->type = null;
               $styles[$key]->rel = null;
               $styles[$key]->media = null;
               $styles[$key]->conditionalStylesheet = null;
            }
         }
      }
   }
}

/*
** SEO Page Description
*/
function rl_seo_pagedesc($item=null, $tour=null, $file=null)
{
   if ($item != null) {
      $itemdesc=snippet(rl_the_text($item), 0, 500, "...");
      return htmlspecialchars(strip_tags($itemdesc));
   } elseif ($tour != null) {
      $tourdesc=snippet(tour('Description'), 0, 500, "...");
      return htmlspecialchars(strip_tags($tourdesc));
   } elseif ($file != null) {
      $filedesc=snippet(metadata('file', array('Dublin Core', 'Description')), 0, 500, "...");
      return htmlspecialchars(strip_tags($filedesc));
   } else {
      return rl_seo_sitedesc();
   }
}

/*
** SEO Site Description
*/
function rl_seo_sitedesc()
{
   return strip_tags(option('description')) ? strip_tags(option('description')) : (rl_about() ? strip_tags(rl_about()) : null);
}

/*
** SEO Page Title
*/
function rl_seo_pagetitle($title, $item)
{
   $subtitle=$item ? (rl_the_subtitle($item) ? ' - '.rl_the_subtitle($item) : null) : null;
   $pt = $title ? $title.$subtitle.' | '.option('site_title') : option('site_title');
   return strip_tags($pt);
}

/*
** SEO Page Image
*/
function rl_seo_pageimg($item=null, $file=null, $tour = null)
{
    if ($item) {
        if (metadata($item, 'has thumbnail')) {
            $itemimg=item_image('fullsize') ;
            preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $itemimg, $result);
            $itemimg=array_pop($result);
        }
    } elseif ($file) {
        if ($itemimg=file_image('fullsize')) {
            preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $itemimg, $result);
            $itemimg=array_pop($result);
        }
    } elseif ($tour) {
        if ($touritems = $tour->getItems()) {
          $itemimg = rl_get_first_image_src($touritems[0], 'fullsize');
        }
    }
    return isset($itemimg) ? $itemimg : rl_seo_pageimg_custom();
}

/*
** SEO Site Image
*/
function rl_seo_pageimg_custom()
{
    $custom_img = get_theme_option('custom_meta_img');
    $custom_img_url = $custom_img ? WEB_ROOT.'/files/theme_uploads/'.$custom_img : rl_the_logo_url();
    return $custom_img_url;
}

/*
** Get theme CSS link with version number
*/
function rl_theme_css($media='all')
{
    $themeName = Theme::getCurrentThemeName();
    $theme = Theme::getTheme($themeName);
    echo '<link href="'.WEB_PUBLIC_THEME.'/'.$themeName.'/css/screen.css?v='.$theme->version.'" media="'.$media.'" rel="stylesheet">';
}


/*
** Custom Label for Items/Stories
*/
function rl_item_label($which=null)
{
    if ($which=='singular') {
        return ($singular=get_theme_option('item_label_singular')) ? $singular : __('Story');
    } elseif ($which=='plural') {
        return ($plural=get_theme_option('item_label_plural')) ? $plural : __('Stories');
    } else {
        return __('Story');
    }
}

/*
** Custom Label for Tours
*/
function rl_tour_label($which=null)
{
    if ($which=='singular') {
        return ($singular=get_theme_option('tour_label_singular')) ? $singular : __('Tour');
    } elseif ($which=='plural') {
        return ($plural=get_theme_option('tour_label_plural')) ? $plural : __('Tours');
    } else {
        return __('Tour');
    }
}


/*
** Global navigation
*/
function rl_global_nav($nested=false)
{
    $curatenav=get_theme_option('default_nav');
    if ($curatenav==1 || !isset($curatenav)) {
      $navArray = array();
      $navArray[] = array('label'=>__('Home'),'uri' => url('/'));
      $navArray[] = array('label'=>rl_item_label('plural'),'uri' => rl_stories_url());
      if(plugin_is_active('TourBuilder')){
        $navArray[] = array('label'=>rl_tour_label('plural'),'uri' => url('tours/browse/'));
      }
      if(plugin_is_active('Geolocation')){
        $navArray[] = array('label'=>__('Map'),'uri' => url('items/map/'));
      }
      $navArray[] = array('label'=>__('About'),'uri' => url('about/'));
      return nav($navArray);
    } elseif ($nested) {
      return '<div class="custom nested">'.public_nav_main()->setMaxDepth(1).'</div>';
    } else {
      return '<div class="custom">'.public_nav_main()->setMaxDepth(0).'</div>';
    }
}

/*
** Subnavigation for items/browse
*/
function rl_item_browse_subnav()
{
  $nav = array(
    array('label'=>__('All') ,'uri'=> rl_stories_url()),
    array('label'=>__('Featured') ,'uri'=> url('items/browse?featured=1')),
    array('label'=>__('Tags'), 'uri'=> url('items/tags')),
  );
  if(plugin_is_active('SubjectsBrowse')){
    array_push($nav,array('label'=>__('Subjects'), 'uri'=> url('items/subjects')));
  }
  echo nav($nav);
}

/*
** Subnavigation for search and items/search
*/
function rl_search_subnav()
{
   echo nav(array(
      array('label'=>__('%s Search', rl_item_label('singular')), 'uri'=> url('items/search')),
      array('label'=>__('Sitewide Search'), 'uri'=> url('search')),
   ));
}


/*
** Subnavigation for collections/browse
*/

function rl_collection_browse_subnav()
{
   echo nav(array(
      array('label'=>__('All') ,'uri'=> url('collections/browse')),
      array('label'=>__('Featured') ,'uri'=> url('collections/browse?featured=1')),
   ));
}

/*
** Subnavigation for tours/browse
*/ 
function rl_tour_browse_subnav()
{
   echo nav(array(
      array('label'=>__('All') ,'uri'=> url('tours/browse')),
      array('label'=>__('Featured') ,'uri'=> url('tours/browse?featured=1')),
      array('label'=>__('Tags'), 'uri'=> url('tours/tags')),
   ));
}

/*
** filter browse_sort_links for tours/browse
** prevents omission of /browse in urls
** hacks in default active sorting class for 'added' as needed
** makes reverse sort work on first click
*/ 
function rl_tours_browse_sort_links($sort=array(null,null)){
  // add /browse to path
  $nav = str_replace('tours?','tours/browse?',browse_sort_links(array(__('Title')=>'title',__('Date Added')=>'id')));
  
  if($sort[0]==null && $sort[1]==null){
    $dom = new DOMDocument;
    $dom->loadHTML($nav);
    $li = $dom->getElementsByTagName('li')->item(1);
    // add sorting class
    $li->setAttribute('class', 'sorting asc');
    // sort direction toggle fix
    $href=$li->firstChild->getAttribute('href');
    if($href){
      $li->firstChild->setAttribute('href', $href.'&sort_dir=d');
    }
    return $dom->saveHTML();
  }else{
    return $nav;
  }
}

/*
** used on tours/browse
*/ 
function rl_sort_objects_array(&$array, $prop='id', $ascending=true) {
    usort($array, function($a, $b) use ($prop, $ascending) {
        return ($ascending) ? strcmp($a->$prop, $b->$prop) : -strcmp($a->$prop, $b->$prop);
    });
}

/*
** Logo URL
*/
function rl_the_logo_url()
{
   $logo = get_theme_option('lg_logo');
   return $logo ? WEB_ROOT.'/files/theme_uploads/'.$logo : img('logo.png');
}

/*
** Logo IMG Tag
*/
function rl_the_logo()
{
   return '<img src="'.rl_the_logo_url().'" alt="'.option('site_title').' '.__('Logo').'"/>';
}

/*
** Link to Random Item
*/
function random_item_link($text=null, $class='show', $hasImage=true)
{
    if (!$text) {
      $text= __('View a Random %s', rl_item_label('singular'));
    }
    $randitems = get_records('Item', array( 'sort_field' => 'random', 'hasImage' => $hasImage), 1);
    if (count($randitems) > 0) {
      $link = link_to($randitems[0], 'show', $text, array( 'class' => 'random-story-link ' . $class ));
    } else {
      $link = link_to('/','show',__('Publish some items to activate this link'),array( 'class' => 'random-story-link ' . $class ));
    }
    return $link;
}

/*
** Ionicons
** https://ionic.io/ionicons
*/
function rl_icon($name=null, $variant="-sharp")
{
   try {
      $file = physical_path_to('images/ionicons/'.$name.$variant.'.svg');
      $svg = file_get_contents($file);
   } catch (exception $e) {
      $svg = null;
   }
   return $svg ? '<span class="icon '.$name.'">'.$svg.'</span>' : null;
}

/*
** Stories link
** theme option: default story sort by date modified
*/
function rl_stories_url(){
  return get_theme_option('stories_modified') ? url('items/browse?sort_field=modified&sort_dir=d') : url('items/browse');
}

/*
** Global header
*/
function rl_global_header($html=null)
{
    ?>
    <nav id="top-navigation" class="" aria-label="<?php echo __('Main Navigation'); ?>">

        <!-- Home / Logo -->
        <?php echo link_to_home_page(rl_the_logo(), array('id'=>'home-logo', 'aria-label'=>'Home')); ?>
        <div id="nav-desktop">
            <?php echo get_theme_option('quicklink_story') ? '<a class="button transparent '.((is_current_url('/items/browse')) ? 'active' : null).'" href="'.rl_stories_url().'">'.rl_icon("location").rl_item_label('plural').'</a>' : null; ?>
            <?php echo get_theme_option('quicklink_tour') && plugin_is_active('TourBuilder') ? '<a class="button transparent '.((is_current_url('/tours/browse')) ? 'active' : null).'" href="'.url('tours/browse').'">'.rl_icon("compass").rl_tour_label('plural').'</a>' : null; ?>
            <?php echo get_theme_option('quicklink_map') && plugin_is_active('Geolocation') ? '<a class="button transparent '.((is_current_url('/items/map')) ? 'active' : null).'" href="'.url('items/map').'">'.rl_icon("map").__('Map').'</a>' : null; ?>
        </div>
        <div id="nav-interactive">
            <!-- Search -->
            <a role="button" tabindex="0" title="<?php echo __('Search'); ?>" id="search-button" href="#footer-search-form" class="button transparent"><?php echo rl_icon("search"); ?><span><?php echo __('Search'); ?></span></a>
            <!-- Menu Button -->
            <a role="button" tabindex="0" title="<?php echo __('Menu'); ?>" id="menu-button" href="#footer-nav" class="button transparent"><?php echo rl_icon("menu"); ?><span><?php echo __('Menu'); ?></span></a>
        </div>

    </nav>
    <div id="header-search-container">
        <div id="header-search-inner" class="inner-padding">
            <?php echo rl_simple_search('header-search', array('id'=>'header-search-form','class'=>'capsule'), __('Search')); ?>
            <div class="search-options">
                <?php echo '<a href="'.url('items/search').'">'.__('Advanced %s Search', rl_item_label()).' &#9656;</a>'; ?>
                <br>
                <?php echo '<a href="'.url('search').'">'.__('Sitewide Search').' &#9656;</a>'; ?>
            </div>
        </div>
        <div class="overlay" onclick="overlayClick()"></div>
    </div>

    <div id="header-menu-container">
        <div id="header-menu-inner">
            <?php echo rl_find_us('transparent-on-dark'); ?>
            <nav>
                <?php echo rl_global_nav(true); ?>
            </nav>
            <div class="menu-random-container"><?php echo random_item_link(rl_icon('dice').__("View a Random %s", rl_item_label('singular')), $class='button transparent', $hasImage=true); ?></div>
            <div class="menu-appstore-container"><?php echo rl_appstore_downloads(); ?></div>
            <div class="menu-darkmode-container">
              <?php
              $dm_allowed = isset($_COOKIE['neverdarkmode']) && $_COOKIE['neverdarkmode']==1 ? false : true;
              ?>
              <label for="dm">
                <span>Allow Dark Mode?</span>
              </label>
              <input type="checkbox" <?php echo $dm_allowed ? 'checked' : null;?> id="dm">
            </div>
        </div>
        <div class="overlay" onclick="overlayClick()"></div>
    </div>
    <?php
}
/*
** Get Subject Terms
*/
function rl_get_subjects(){
  $db = get_db();
  $prefix=$db->prefix;
  $select = "
  SELECT TRIM(et.text) as text, count(*) as total, LOWER(LEFT(text, 1)) as letter
  FROM {$prefix}items as i
  INNER JOIN {$prefix}element_texts as et ON i.id = et.record_id
  WHERE public = 1 AND element_id = 49
  GROUP BY text
  ORDER BY text ASC
  ";
  $sql = $select;
  $q = $db->query($sql);
  $subjects = $q->fetchAll();
  return $subjects;
}

/*
** Get Subjects Select
*/
function rl_subjects_select($subjects,$num){
  if($subjects){
    $html = '<select hidden>';
    $html .= '<option value="">'.__('All %s',rl_item_label('plural')).': '.$num.'</option>';
    foreach($subjects as $subject){
      $html .= '<option value="'.strip_tags(urlencode($subject['text'])).'">'.strip_tags($subject['text']).': '.$subject['total'].'</option>';
    }
    $html .= '</select>';
    return $html;
  }
}

/*
** Story Map - Single
*/
function rl_story_map_single($title=null, $location=null, $address=null, $hero_img=null, $hero_orientation=null) 
{ 
if(plugin_is_active('Geolocation')):
?>
  <nav aria-label="<?php echo __('Skip Interactive Map');?>"><a id="skip-map" href="#map-actions"><?php echo __('Skip Interactive Map');?></a></nav>
  <figure id="story-map" data-default-layer="<?php echo get_theme_option('map_style') ? get_theme_option('map_style') : 'CARTO_VOYAGER';?>" data-lat="<?php echo $location[ 'latitude' ];?>" data-lon="<?php echo $location[ 'longitude' ];?>" data-zoom="<?php echo $location['zoom_level'];?>" data-title="<?php echo $title ? strip_tags($title) : null;?>" data-image="<?php echo $hero_img;?>" data-orientation="<?php echo $hero_orientation;?>" data-address="<?php echo $address ? strip_tags($address) : null;?>" data-color="<?php echo get_theme_option('marker_color');?>" data-root-url="<?php echo WEB_ROOT;?>" data-maki-js="<?php echo src('maki/maki.min.js', 'javascripts');?>" data-providers="<?php echo src('providers.js', 'javascripts');?>" data-leaflet-js="<?php echo src('theme-leaflet/leaflet.js', 'javascripts');?>" data-leaflet-css="<?php echo src('theme-leaflet/leaflet.css', 'javascripts');?>">
      <div class="curatescape-map">
          <div id="curatescape-map-canvas"></div>
      </div>
      <figcaption><?php echo rl_map_caption();?></figcaption>
  </figure>
  <div id="map-actions">
    <a class="button directions" target="_blank" rel="noopener" href="https://maps.google.com/maps?location&daddr=<?php echo $address ? urlencode(strip_tags($address)) : $location[ 'latitude' ].','.$location[ 'longitude' ];?>">
      <?php echo rl_icon("logo-google", null);?>
      <span class="label">
          <?php echo __('Open in Google Maps');?></span>
    </a>
  </div>
<?php 
endif;
}

/*
** Story Map - Multi
*/
function rl_story_map_multi($tour=false)
{
  if(plugin_is_active('Geolocation') && plugin_is_active('CuratescapeJSON')):
    $pluginlat=(get_option('geolocation_default_latitude')) ? get_option('geolocation_default_latitude') : null;
    $pluginlon=(get_option('geolocation_default_longitude')) ? get_option('geolocation_default_longitude') : null;
    $zoom=(get_option('geolocation_default_zoom_level')) ? get_option('geolocation_default_zoom_level') : 12; ?>
    <figure id="multi-map" data-json-source="?output=mobile-json" data-lat="<?php echo $pluginlat; ?>" data-lon="<?php echo $pluginlon; ?>" data-zoom="<?php echo $zoom; ?>" data-default-layer="<?php echo get_theme_option('map_style') ? get_theme_option('map_style') : 'CARTO_VOYAGER'; ?>" data-color="<?php echo get_theme_option('marker_color'); ?>" data-featured-color="<?php echo get_theme_option('featured_marker_color'); ?>" data-featured-star="<?php echo get_theme_option('featured_marker_star'); ?>" data-root-url="<?php echo WEB_ROOT; ?>" data-maki-js="<?php echo src('maki/maki.min.js', 'javascripts'); ?>" data-providers="<?php echo src('providers.js', 'javascripts'); ?>" data-leaflet-js="<?php echo src('theme-leaflet/leaflet.js', 'javascripts'); ?>" data-leaflet-css="<?php echo src('theme-leaflet/leaflet.css', 'javascripts'); ?>" data-cluster-css="<?php echo src('leaflet.markercluster/leaflet.markercluster.min.css', 'javascripts'); ?>" data-cluster-js="<?php echo src('leaflet.markercluster/leaflet.markercluster.js', 'javascripts'); ?>" data-cluster="<?php echo $tour && get_theme_option('tour_clustering') ? '1' : get_theme_option('clustering'); ?>" data-fitbounds-label="<?php echo __('Zoom to fit all locations'); ?>">
        <div class="curatescape-map">
            <div id="curatescape-map-canvas"></div>
        </div>
    </figure>
    <?php
  endif;
}

/*
** Story Map - HOME
*/
function rl_homepage_map($ishome=true,$totalItems=null)
{
  if(plugin_is_active('Geolocation') && plugin_is_active('CuratescapeJSON')):

    if(!isset($totalItems)){
      $db = get_db();
      $table = $db->getTable('Location');
      $select = $table->getSelect();
      $q = $select->query();
      $results = $q->fetchAll();
      $totalItems = count($results);    
    }

    $pluginlat=(get_option('geolocation_default_latitude')) ? get_option('geolocation_default_latitude') : null;
    $pluginlon=(get_option('geolocation_default_longitude')) ? get_option('geolocation_default_longitude') : null;
    $zoom=(get_option('geolocation_default_zoom_level')) ? get_option('geolocation_default_zoom_level') : 12; ?>
    
    <section id="home-map" class="inner-padding browse">
      <h2 class="query-header"><?php echo __('%s Map',rl_item_label());?></h2>
      <div id="home-map-container" data-label="<?php echo __('All %s',rl_item_label('plural')).': '.$totalItems;?>">
        <figure id="multi-map" data-json-source="/items/browse?output=mobile-json" data-lat="<?php echo $pluginlat; ?>" data-lon="<?php echo $pluginlon; ?>" data-zoom="<?php echo $zoom; ?>" data-default-layer="<?php echo get_theme_option('map_style') ? get_theme_option('map_style') : 'CARTO_VOYAGER'; ?>" data-color="<?php echo get_theme_option('marker_color'); ?>" data-featured-color="<?php echo get_theme_option('featured_marker_color'); ?>" data-featured-star="<?php echo get_theme_option('featured_marker_star'); ?>" data-root-url="<?php echo WEB_ROOT; ?>" data-maki-js="<?php echo src('maki/maki.min.js', 'javascripts'); ?>" data-providers="<?php echo src('providers.js', 'javascripts'); ?>" data-leaflet-js="<?php echo src('theme-leaflet/leaflet.js', 'javascripts'); ?>" data-leaflet-css="<?php echo src('theme-leaflet/leaflet.css', 'javascripts'); ?>" data-cluster-css="<?php echo src('leaflet.markercluster/leaflet.markercluster.min.css', 'javascripts'); ?>" data-cluster-js="<?php echo src('leaflet.markercluster/leaflet.markercluster.js', 'javascripts'); ?>" data-cluster="<?php echo isset($tour) && get_theme_option('tour_clustering') ? '1' : get_theme_option('clustering'); ?>" data-fitbounds-label="<?php echo __('Zoom to fit all locations'); ?>">
          <div class="curatescape-map">
            <?php echo (get_theme_option('map_subjects') == 1) 
            ? rl_subjects_select(rl_get_subjects(),$totalItems) 
            : null;?>
            <div id="curatescape-map-canvas"></div>
          </div>
        </figure>
      </div>
      <?php if($ishome):?>
      <div class="view-more-link"><a class="button" href=<?php echo url('items/map').'>'.__('View Map Page');?></a></div>
      <?php endif;?>
    </section>
    <?php
  endif;
}

/*
** Outputs UI and hidden markup for multi-map
*/
function multimap_markup($tour=false, $map_label=null, $button_label=null)
{
  if(plugin_is_active('Geolocation') && plugin_is_active('CuratescapeJSON')):
    if (!$button_label) {
        $button_label = __('Show Results on Map');
    }
    if (!$map_label) {
        $map_label = __('Map');
    } ?>
    <div id="multi-map-container" data-label="<?php echo htmlentities(strip_tags($map_label)); ?>">
        <?php echo rl_story_map_multi($tour); ?>
    </div>
    <div id="multi-map-overlay"></div>
    <a role="button" title="<?php echo htmlentities(strip_tags($button_label));?>" id="show-multi-map" class="pulse shadow-big" tabindex="0" aria-label="<?php echo htmlentities(strip_tags($button_label)); ?>" data-close="<?php echo __('Close Map or Press ESC Key');?>">
        <span id="show-multi-map-inner"></span>
    </a>
    <noscript><?php echo rl_nojs_map();?></noscript>
    <?php
  endif;
}


/*
** Modified search form
** Adds HTML "placeholder" attribute
** Adds HTML "type" attribute
** Includes settings for simple and advanced search via theme options
*/

function rl_simple_search($inputID='search', $formProperties=array(), $ariaLabel="Search")
{
    $sitewide = (get_theme_option('use_sitewide_search') == 1) ? 1 : 0;
    $qname = ($sitewide==1) ? 'query' : 'search';
    $searchUri = ($sitewide==1) ? url('search') : url('items/browse?sort_field=relevance');
    $placeholder =  __('Search for %s', strtolower(rl_item_label('plural')));
    $default_record_types = rl_search_form_default_record_types();


    $searchQuery = array_key_exists($qname, $_GET) ? $_GET[$qname] : '';
    $formProperties['action'] = $searchUri;
    $formProperties['method'] = 'get';
    $html = '<form ' . tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= get_view()->formText('search', $searchQuery, array('aria-label'=>$ariaLabel,'name'=>$qname,'id'=>$inputID,'class'=>'textinput search','placeholder'=>$placeholder));
    $html .= '</fieldset>' . "\n\n";

    // add hidden fields for the get parameters passed in uri
    $parsedUri = parse_url($searchUri);
    if (array_key_exists('query', $parsedUri)) {
        parse_str($parsedUri['query'], $getParams);
        foreach ($getParams as $getParamName => $getParamValue) {
            $html .= get_view()->formHidden($getParamName, $getParamValue, array('id'=>$inputID.'-'.$getParamValue));
        }
    }
    if ($sitewide==1 && count($default_record_types)) {
        foreach ($default_record_types as $drt) {
            $html .= get_view()->formHidden('record_types[]', $drt, array('id'=>$inputID.'-'.$drt));
        }
    }

    $html .= '<button aria-label="'.__("Submit").'" type="submit" class="submit button" name="submit_'.$inputID.'" id="submit_search_advanced_'.$inputID.'">'.rl_icon('search').'</button>';

    $html .= '</form>';
    return $html;
}


/*
** App Store links on homepage
*/
function rl_appstore_downloads()
{
    if (get_theme_option('enable_app_links')) {
        $apps=array();
        $ios_app_id = get_theme_option('ios_app_id');
        if ($ios_app_id) {
            $href='https://itunes.apple.com/us/app/'.$ios_app_id;
            $apps[]='<a class="button appstore ios" href="'.$href.'" target="_blank" rel="noopener">'.
                     rl_icon('logo-apple-appstore', null).__('App Store').'</a>';
        }

        $android_app_id = get_theme_option('android_app_id');
        if ($android_app_id) {
            $href='http://play.google.com/store/apps/details?id='.$android_app_id;
            $apps[]='<a class="button appstore android" href="'.$href.'" target="_blank" rel="noopener">'.
                     rl_icon('logo-google-playstore', null).__('Google Play').'</a>';
        }


        if (count($apps) > 1) {
            return implode(' ', $apps);
        }
    }
}


/*
** Replace BR tags, wrapping text in P tags instead
*/
function replace_br($data)
{
    $data = preg_replace('#(?:<br\s*/?>\s*?){2,}#', '</p>
<p>', $data);
    return "
<p>$data</p>";
}

/*
** primary item text
*/

function rl_the_text($item='item', $options=array())
{
    $dc_desc = metadata($item, array('Dublin Core', 'Description'), $options);
    $primary_text = element_exists('Item Type Metadata', 'Story') ? metadata($item, array('Item Type Metadata', 'Story'), $options) : null;

    return $primary_text ? replace_br($primary_text) : ($dc_desc ? replace_br($dc_desc) : null);
}

/*
** Title
*/
function rl_the_title($item='item')
{
    return '<h1 class="title">'.strip_tags(metadata($item, array('Dublin Core', 'Title')), array('index'=>0)).'</h1>';
}


/*
** Subtitle
*/

function rl_the_subtitle($item='item')
{
    $dc_title2 = metadata($item, array('Dublin Core', 'Title'), array('index'=>1));
    $subtitle=element_exists('Item Type Metadata', 'Subtitle') ? metadata($item, array('Item Type Metadata', 'Subtitle')) : null;

    return $subtitle ? '<p class="subtitle">'.$subtitle.'</p>' : ($dc_title2!=='[Untitled]' ? '<p class="subtitle">'.$dc_title2.'</p>' : null);
}

/*
** Lede
*/
function rl_the_lede($item='item')
{
    if (element_exists('Item Type Metadata', 'Lede')) {
        $lede=metadata($item, array('Item Type Metadata', 'Lede'));
        return $lede ? '<p class="lede">'.strip_tags($lede, '<a><em><i><u><b><strong><strike>').'</p>' : null;
    }
}

/*
** Title + Subtitle (for search/browse/home)
*/
function rl_the_title_expanded($item='item')
{
    $title='<h3 class="title">'.strip_tags(metadata($item, array('Dublin Core', 'Title'))).'</h3>';
    if (element_exists('Item Type Metadata', 'Subtitle')) {
        if ($s=metadata($item, array('Item Type Metadata','Subtitle'))) {
            $subtitle = '<p class="subtitle">'.strip_tags($s).'</p>';
            $title = $title.$subtitle;
        }
    }
    return link_to($item, 'show', $title, array('class'=>'permalink'));
}

/*
** Snippet: Lede + Story (for search/browse/home)
*/
function rl_snippet_expanded($item='item')
{
    $story=element_exists('Item Type Metadata', 'Story') ? metadata($item, array('Item Type Metadata', 'Story'), array('snippet'=>250)) : null;
    if (get_theme_option('lede_on_browse') && element_exists('Item Type Metadata', 'Lede')) {
        $lede = strip_tags(metadata($item, array('Item Type Metadata','Lede'))).' ';
        $story = $lede.$story;
    }
    return snippet($story, 0, 250, '&hellip;');
}


/*
** sponsor for use in item byline
*/
function rl_the_sponsor($item='item')
{
    if (element_exists('Item Type Metadata', 'Sponsor')) {
        $sponsor=metadata($item, array('Item Type Metadata','Sponsor'));
        return $sponsor ? '<span class="sponsor"> '.__('with research support from %s', $sponsor).'</span>' : null;
    }
}

/*
** Filed Under
** returns link to: (public) collection for item, or first subject, or first tag
** configurable in theme settings
*/
function rl_filed_under($item = null, $maxlength = 35)
{
    $useCollection = get_theme_option('item_topic_collection') !== null ?
      get_theme_option('item_topic_collection') :
      true;
    $useSubject = get_theme_option('item_topic_subject') !== null ?
      get_theme_option('item_topic_subject') :
      true;
    $useTag = get_theme_option('item_topic_tag') !== null ?
      get_theme_option('item_topic_tag') :
      true;
          
    if ($useCollection && ($collection = get_collection_for_item()) && $collection->public) {
        $label = metadata($collection,array('Dublin Core','Title'));
        $node = link_to_collection_for_item(snippet($label,0,$maxlength), array('title'=>__('Collection: %s', $label), 'class'=>'tag tag-alt'), 'show');
    } elseif ($useSubject && $subject = metadata('item', array('Dublin Core', 'Subject'), 0)) {
        $link = WEB_ROOT;
        $link .= htmlentities('/items/browse?term=');
        $link .= rawurlencode($subject);
        $link .= htmlentities('&search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=');
        $link .= urlencode(str_replace('&amp;', '&', $subject));
        $label = trim($subject);
        $node = '<a title="'.__('Subject: %s', $label).'" class="tag tag-alt" href="'.w3_valid_url($link).'">'.snippet($label,0,$maxlength).'</a>';
    } elseif ($useTag && metadata($item, 'has tags') && $tag = $item->Tags[0]) {
        $link = WEB_ROOT;
        $link .= htmlentities('/items/browse?tags=');
        $link .= rawurlencode($tag);
        $label = trim($tag);
        $node = '<a title="'.__('Tag: %s', $label).'" class="tag tag-alt" href="'.$link.'">'.snippet($label,0,$maxlength).'</a>';
    } else {
        $label = trim(rl_item_label('singular'));
        $node = link_to('items', 'browse', snippet($label,0,$maxlength), array('title'=>__('Type: %s', $label), 'class'=>'tag tag-alt'));
    }
    return '<div class="title-card-subject '.text_to_id($label,'subject').'"><span class="screen-reader">'.__('Filed Under').'</span> '.$node.'</div>';
}

/*
** Subjects for item
** Raw = output as <a>
** !Raw = output as div-h3-ul-li-a 
*/
function rl_subjects($raw=false, $rawfirst=false)
{
    $subjects = metadata('item', array('Dublin Core', 'Subject'), 'all');
    $array=array();
    $html = null;
    if (count($subjects) > 0) {
        foreach ($subjects as $subject) {
            $link = WEB_ROOT;
            $link .= htmlentities('/items/browse?term=');
            $link .= rawurlencode($subject);
            $link .= htmlentities('&search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=');
            $link .= urlencode(str_replace('&amp;', '&', $subject));
            $node = '
<li><a title="'.__('Subject').': '.$subject.'" class="tag tag-alt" href="'.w3_valid_url($link).'">'.$subject.'</a></li>';
            array_push($array, $node);
        }
        $html .= '<div id="subjects">';
        $html .= '<ul>';
        $html .= implode('', $array);
        $html .= '</ul>';
        $html .= '</div>';
    }
    return $html;
}

/*
** Display the item tags
items/browse?tags=Cleveland+Metroparks
*/
function rl_tags($item)
{
  $html = null;
  if (metadata($item, 'has tags')) {
      $array=array();
      foreach ($item->Tags as $tag) {
          $link = WEB_ROOT;
          $link .= htmlentities('/items/browse?tags=');
          $link .= urlencode($tag);
          $node = '<li><a title="'.__('Tag').': '.$tag.'" class="tag" href="'.$link.'">'.$tag.'</a></li>';
          array_push($array, $node);
      }
      $html .= '<div id="tags">';
      $html .= '<ul>';
      $html .= implode('', $array);
      $html .= '</ul>';
      $html .= '</div>';
  }
  return $html;
}

/*
** Display the item collection
*/
function rl_collection($item)
{
  if ($collection = get_collection_for_item() && isset($collection) && $collection->public) {
      return '<div id="collection">'.link_to_collection_for_item(null, array('class'=>'tag tag-alt','title'=>__('Collection')), 'show').'</div>';
  }
}

/* get a list of related tour links for a given item, for use on items/show template */
function rl_tours_for_item($item_id=null, $html=null)
{
  if (plugin_is_active('TourBuilder')) {
      if (is_int($item_id)) {
          $db = get_db();
          $prefix=$db->prefix;
          $select = $db->select()
          ->from(array('ti' => $prefix.'tour_items')) // SELECT * FROM omeka_tour_items as ti
          ->join(array('t' => $prefix.'tours'), // INNER JOIN omeka_tours as t
          'ti.tour_id = t.id') // ON ti.tour_id = t.id
          ->where("item_id=$item_id AND public=1"); // WHERE item_id=$item_id
          $q = $select->query();
          $results = $q->fetchAll();

          if ($results) {
              $html.='<div id="tour-for-item"><ul>';
              foreach ($results as $result) {
                  $html.='<li><a class="tag tag-alt" href="'.url('tours/show/').$result['id'].'">';
                  $html.=$result['title'];
                  $html.='</a></li>';
              }
              $html.='</ul></div>';
          }
          return $html;
      }
  }
}

/*
** Return SRC for an item's first image (excluding video thumbs, etc)
*/
function rl_get_first_image_src($item, $size='fullsize')
{
   if ($item && $item->id) {
      $db = get_db();
      $table = $db->getTable('Files');
      $select = $table->getSelect();
      $select->where('item_id = '.$item->id);
      $select->where('has_derivative_image = 1');
      $select->where('mime_type LIKE "image%"');
      $select->order('order ASC');
      $q = $select->query();
      $results = $q->fetchAll();
      if ($results) {
         // first image file
         $sanitized_filename = str_ireplace(array('.JPG','.jpeg','.JPEG','.png','.PNG','.gif','.GIF', '.bmp','.BMP'), '.jpg', $results[0]['filename']);
         return WEB_ROOT.'/files/'.$size.'/'.$sanitized_filename;
      } else {
         return null;
      }
   } else {
      return null;
   }
}

/*
** Return formatted meta links
*/
function rl_meta_style($heading=null, $array=array())
{
   $html = null;
   if ($heading && count($array)) {
      foreach ($array as $node) {
          $html .= $node;
      }
   }
   if ($html) {
      return '<div class="meta-'.str_replace(' ', '-', strtolower($heading)).'">
      <h3 class="metadata-label">'.$heading.'</h3>
      <div class="meta-style">'.$html.'</div>
      </div>';
   } else {
      return null;
   }
}

/*
** Display the official website
*/
function rl_official_website($item='item')
{
   $html = null;
   if (element_exists('Item Type Metadata', 'Official Website')) {
      $website=metadata($item, array('Item Type Metadata','Official Website'));
      $html .= $website ? '<div class="break">'.$website.'</div>' : null;
   }
   return $html;
}

/*
** Display the street address
*/
function rl_street_address($item='item')
{
   if (element_exists('Item Type Metadata', 'Street Address') && $address=metadata($item, array('Item Type Metadata','Street Address'))) {
      $map_link='<a target="_blank" rel="noopener" href="https://maps.google.com/maps?saddr=current+location&daddr='.urlencode(strip_tags($address)).'">map</a>';
      return $address ? $address : null;
   } else {
      return null;
   }
}

/*
** Display the access info
*/
function rl_access_information($item='item', $formatted=true)
{
   if (element_exists('Item Type Metadata', 'Access Information')) {
      $access_info=metadata($item, array('Item Type Metadata', 'Access Information'));
      return $access_info ? ($formatted ? '<div class="access-information">
      <h3>'.__('Access Information').'</h3>
      <div>'.$access_info.'</div>
      </div>' : $access_info) : null;
   } else {
      return null;
   }
}

/*
** Display the map caption
*/

function rl_map_caption($item='item')
{
   $caption=array();
   if ($addr=rl_street_address($item)) {
      $caption[]=strip_tags($addr, '<a>');
   }
   if ($accs=rl_access_information($item, false)) {
      $caption[]=strip_tags($accs, '<a>');
   }
   return implode(' | ', $caption);
}

/*
** Display the factoid
*/
function rl_factoid($item='item', $html=null)
{
   if (element_exists('Item Type Metadata', 'Factoid')) {
      $factoids=metadata($item, array('Item Type Metadata','Factoid'), array('all'=>true));
      if ($factoids) {
         $html .= '<div class="separator"></div>';
         foreach ($factoids as $factoid) {
            $html.='<div class="factoid caption">'.rl_icon('information-circle').'<span>'.$factoid.'</span></div>';
         }
         if ($html) {
            return '<aside id="factoid" artia-label="'.__('Factoids').'">'.$html.'</aside>';
         }
      }
   }
}

/*
** Display related links
*/
function rl_related_links()
{
    $dc_relations_field = metadata('item', array('Dublin Core', 'Relation'), array('all' => true));

    $related_resources = element_exists('Item Type Metadata', 'Related Resources') ? metadata('item', array('Item Type Metadata', 'Related Resources'), array('all' => true)) : null;

    $relations = $related_resources ? $related_resources : $dc_relations_field;

    if ($relations) {
        $html= '<div class="related-resources"><ol>';
        $i=1;
        foreach ($relations as $relation) {
            $html.= '<li id="footnote-'.$i.'">'.strip_tags($relation, '<a><i><cite><em><b><strong>').'</li>';
            $i++;
        }
        $html.= '</ol></div>';
        return $html;
    }
}

/*
** w3_valid_url
** For when url() is not the right tool
** Just escaping common chars that trigger the validator.
*/
function w3_valid_url($string){
  $string = str_replace('[', '%5B', $string);
  $string = str_replace(']', '%5D', $string);
  $string = str_replace('|', '%7C', $string);
  $string = str_replace(' ', '%20', $string);
  $string = str_replace('&quot;', '%22', $string);
  return $string;
}

/*
** Author Byline
*/
function rl_the_byline($itemObj='item', $include_sponsor=false)
{
    $html='<div class="byline">'.__('By').' ';
    if (metadata($itemObj, array('Dublin Core', 'Creator'))) {
        $authors=metadata($itemObj, array('Dublin Core', 'Creator'), array('all'=>true));
        $total=count($authors);
        $index=1;
        $authlink=get_theme_option('author_links');
        foreach ($authors as $author) {
            if ($authlink > 0) {
                $href=w3_valid_url('/items/browse?search=&advanced[0][element_id]=39&advanced[0][type]=is+exactly&advanced[0][terms]='.strip_tags($author));
            }else{
              $href='javascript:void(0)';
            }
            $author='<a href="'.$href.'">'.$author.'</a>';
            switch ($index) {
               case ($total):
               $delim ='';
               break;
               case ($total-1):
               $delim =' <span class="amp">&amp;</span> ';
               break;
               
               default:
               $delim =', ';
               break;
            }
            $html .= $author.$delim;
            $index++;
        }
    } else {
        $html .= option('site_title');
    }
    $html .= (($include_sponsor) && (rl_the_sponsor($itemObj)!==null)) ? ''.rl_the_sponsor($itemObj) : null;
    $html .='</div>';
    return $html;
}


/*
** Custom item citation
*/
function rl_item_citation()
{
    return '<div class="item-citation"><div>'.html_entity_decode(metadata('item', 'citation')).'</div></div>';
}

/*
** Post Added/Modified String
*/
function rl_post_date()
{
   if (get_theme_option('show_datestamp') > 0) {
      $a=format_date(metadata('item', 'added'));
      $m=format_date(metadata('item', 'modified'));
      return '<div class="item-post-date">'.__('Published %s.', $a).(($a!==$m) ? ' '.__('Last updated %s.', $m) : null).'</div>';
   }
}
function rl_post_date_header()
{
   if (get_theme_option('show_datestamp_header') > 0) {
      $a=format_date(metadata('item', 'added'));
      $m=format_date(metadata('item', 'modified'));
      return '<div class="item-post-date byline">'.__('Published %s.', $a).(($a!==$m) ? ' '.__('Last updated %s.', $m) : null).'</div>';
   }
}

/*
** Build caption from description, source, creator, date
*/
function rl_file_caption($file, $includeTitle=true)
{
    $caption=array();

    $title = metadata($file, array( 'Dublin Core', 'Title' ));
    $caption[] = '<span class="file-title" itemprop="name"><cite><a itemprop="contentUrl" title="'.__('View File Record').'" href="'.url('files/show/').$file->id.'">'.($title ? $title : __('Untitled')).'</a></cite></span>';

    if ($description = metadata($file, array( 'Dublin Core', 'Description' ))) {
        $caption[]= '<span class="file-description">'.strip_tags($description, '<a><u><strong><em><i><cite>').'</span>';
    }

    if ($source = metadata($file, array( 'Dublin Core', 'Source' ))) {
        $caption[]= '<span class="file-source"><span>'.__('Source').'</span>: '.$source.'</span>';
    }

    if ($creator = metadata($file, array( 'Dublin Core', 'Creator' ))) {
        $caption[]= '<span class="file-creator"><span>'.__('Creator').'</span>: '.$creator.'</span>';
    }

    if ($date = metadata($file, array( 'Dublin Core', 'Date' ))) {
        $caption[]= '<span class="file-date"><span>'.__('Date').'</span>: '.$date.'</span>';
    }

    if (count($caption)) {
        return implode(' ', $caption);
    }
}

/*
** Loop through and display audio/video files
*/
function rl_streaming_files($filesArray=null, $type=null, $openFirst=false)
{
   $html=null;
   $index=0;
   $videoTypes = array('video/mp4','video/mpeg','video/quicktime'); // @todo: in_array($file['mime'],$videoTypes)
   $audioTypes = array('audio/mp3'); // @todo: in_array($file['mime'],$videoTypes)
   foreach ($filesArray as $file) {
      $index++;
      $html.='<div itemscope itemtype="http://schema.org/'.ucfirst($type).'Object">';
      $html.='<div class="media-player '.$type.' '.($openFirst && $index==1 ? 'active' : '').'" data-type="'.$type.'" data-index="'.$index.'" data-src="'.WEB_ROOT.'/files/original/'.$file['src'].'">';
      if ($type == 'audio') {
        $thumb = WEB_PUBLIC_THEME.'/'.Theme::getCurrentThemeName().'/images/ionicons/headset-sharp.svg';
        $html.='<audio itemprop="associatedMedia" controls preload="auto">
            <source src="'.WEB_ROOT.'/files/original/'.$file['src'].'" type="audio/mp3">
            <p class="media-no-support">'.__('Your web browser does not support HTML5 audio').'</p>
        </audio>';
      } elseif ($type="video") {
        $thumb = WEB_PUBLIC_THEME.'/'.Theme::getCurrentThemeName().'/images/ionicons/film-sharp.svg';
        $html.='<video itemprop="associatedMedia" playsinline controls preload="auto">
            <source src="'.WEB_ROOT.'/files/original/'.$file['src'].'" type="video/mp4">
            <p class="media-no-support">'.__('Your web browser does not support HTML5 video').'</p>
        </video>';
      }
      $html .='</div>';
      $html.='<div class="media-select">';
      $html.='<div class="media-thumb"><a tabindex="0" data-type="'.$type.'" data-index="'.$index.'" title="play" class="button icon-round media-button"></a></div>';
      $html.='<div class="media-caption" itemprop="description">'.$file['caption'].'</div>';
      $html.='<meta itemprop="uploadDate" content="'.$file['date'].'">';
      $html.='<meta itemprop="thumbnailUrl" content="'.$thumb.'">';
      $html.='</div>';
      $html.='</div>';
   };
    if ($html): ?>
   <figure class="item-media <?php echo $type; ?>">
       <div class="media-container">
           <div class="media-list">
               <?php echo $html; ?>
           </div>
       </div>
   </figure>
   <?php endif;
}

/*
** loop through and display DOCUMENT files other than the supported audio, video, and image types
*/
function rl_document_files($files=array())
{
    $html=null;
    foreach ($files as $file) {
        $src=WEB_ROOT.'/files/original/'.$file['src'];
        $extension=pathinfo($src, PATHINFO_EXTENSION);
        $size=formatSizeUnits($file['size']);
        $title = $file['title'] ? $file['title'] : $file['filename'];

        $html .= '<tr>';
        $html .= '<td class="title"><a title="'.__('View File Details').'" href="/files/show/'.$file['id'].'">'.$title.'</a></td>';
        $html .= '<td class="info"><span>'.$extension.'</span> / '.$size.'</td>';
        $html .= '<td class="download"><a class="button" target="_blank" href="'.$src.'"><i class="fa fa-download" aria-hidden="true"></i><span>Download</span></a></td>';
        $html .= '</tr>';
    }
    if ($html) {
        echo '<figure id="item-documents">';
        echo '<table><tbody><tr><th>Name</th><th>Info</th><th>Actions</th></tr>'.$html.'</tbody></table>';
        echo '</figure>';
    }
}
/*
** display single file in FILE TEMPLATE
*/

function rl_single_file_show($file=null)
{
    $html=null;
    $mime = metadata($file, 'MIME Type');
    $img = array('image/jpeg','image/jpg','image/png','image/jpeg','image/gif');
    $audioTypes = array('audio/mpeg');
    $videoTypes = array('video/mp4','video/mpeg','video/quicktime');


    // SINGLE AUDIO FILE
    if (array_search($mime, $audioTypes) !== false) {
      ?>
      <figure id="item-audio">
       <div class="media-container audio">
           <audio src="<?php echo file_display_url($file, 'original'); ?>" id="curatescape-player-audio" class="video-js" controls preload="auto">
               <p class="media-no-js">To listen to this audio please consider upgrading to a web browser that supports HTML5 audio</p>
           </audio>
       </div>
      </figure>
      <?php


    // SINGLE VIDEO FILE
    } elseif (array_search($mime, $videoTypes) !== false) {
        $videoTypes = array('video/mp4','video/mpeg','video/quicktime');
        $videoFile = file_display_url($file, 'original');
        $videoTitle = metadata($file, array('Dublin Core', 'Title'));
        $videoDesc = rl_file_caption($file, false);
        $videoTitle = metadata($file, array('Dublin Core','Title'));
        $embeddable=embeddableVersion($file, $videoTitle, $videoDesc, array('Dublin Core','Relation'), false);
        if ($embeddable) {
            // If a video has an embeddable streaming version, use it.
            $html.= $embeddable;
        } else {
            $html .= '<div class="item-file-container">';
              $html .= '<video width="725" height="410" controls preload="auto" data-setup="{}">';
                $html .= '<source src="'.$videoFile.'" type="video/mp4">';
                $html .= '<p class="media-no-js">To listen to this audio please consider upgrading to a web browser that supports HTML5 video</p>';
              $html .= '</video>';
            $html .= '</div>';
        }

        return $html;

    // SINGLE IMAGE OR OTHER FILE
    } else {
        return file_markup($file, array('imageSize'=>'fullsize'));
    }
}

/*
** display additional (non-core) file metadata in FILE TEMPLATE
*/
function rl_file_metadata_additional($file='file', $html=null)
{
    $fields = all_element_texts($file, array('return_type'=>'array','show_element_sets'=>'Dublin Core'));

    if ($fields['Dublin Core']) {

        // Omit Primary DC Fields
        $dc = array_filter($fields['Dublin Core'], function ($key) {
            $omit=array('Description','Title');
            return !(in_array($key, $omit));
        }, ARRAY_FILTER_USE_KEY);

        // Output
        foreach ($dc as $dcname=>$values) {
            $html.='<div class="additional-element">';
            $html.='<h3 class="h4 additional-element-name">'.$dcname.'</h3>';
            $html.='<div class="additional-element-value-container">';
            foreach ($values as $value) {
                $html.='<div class="additional-element-value">'.$value.'</div>';
            }
            $html.='</div>';
            $html.='</div>';
        }
    }
   return $html ? '<div class="additional-elements">'.$html.'</div>' : null;
}

/*
** Checks file metadata record for embeddable version of video file
** Because YouTube and Vimeo have better compression, etc.
** returns string $html | false
*/
function embeddableVersion($file, $title=null, $desc=null, $field=array('Dublin Core','Relation'), $caption=true)
{
    $youtube= (strpos(metadata($file, $field), 'youtube.com')) ? metadata($file, $field) : false;
    $youtube_shortlink= (strpos(metadata($file, $field), 'youtu.be')) ? metadata($file, $field) : false;
    $vimeo= (strpos(metadata($file, $field), 'vimeo.com')) ? metadata($file, $field) : false;

    if ($youtube) {
        // assumes YouTube links look like https://www.youtube.com/watch?v=NW03FB274jg where the v query contains the video identifier
        $url=parse_url($youtube);
        $id=str_replace('v=', '', $url['query']);
        $html= '<div class="embed-container youtube" id="v-streaming" style="position: relative;padding-bottom: 56.25%;height: 0; overflow: hidden;"><iframe style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;" src="//www.youtube.com/embed/'.$id.'" frameborder="0" width="725" height="410" allowfullscreen></iframe></div>';
        if ($caption==true) {
            $html .= ($title) ? '<h4 class="title video-title sib">'.$title.' <span class="icon-info-sign" aria-hidden="true"></span></h4>' : '';
            $html .= ($desc) ? '<p class="description video-description sib">'.$desc.link_to($file, 'show', '<span class="view-file-link"><span class="icon-file" aria-hidden="true"></span> '.__('View File Details Page').'</span>', array('class'=>'view-file-record','rel'=>'nofollow')).'</p>' : '';
        }
        return '<div class="item-file-container">'.$html.'</div>';
    } elseif ($youtube_shortlink) {
        // assumes YouTube links look like https://www.youtu.be/NW03FB274jg where the path string contains the video identifier
        $url=parse_url($youtube_shortlink);
        $id=$url['path'];
        $html= '<div class="embed-container youtube" id="v-streaming" style="position: relative;padding-bottom: 56.25%;height: 0; overflow: hidden;"><iframe style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;" src="//www.youtube.com/embed/'.$id.'" frameborder="0" width="725" height="410" allowfullscreen></iframe></div>';
        if ($caption==true) {
            $html .= ($title) ? '<h4 class="title video-title sib">'.$title.' <span class="icon-info-sign" aria-hidden="true"></span></h4>' : '';
            $html .= ($desc) ? '<p class="description video-description sib">'.$desc.link_to($file, 'show', '<span class="view-file-link"><span class="icon-file" aria-hidden="true"></span> '.__('View File Details Page').'</span>', array('class'=>'view-file-record','rel'=>'nofollow')).'</p>' : '';
        }
        return '<div class="item-file-container">'.$html.'</div>';
    } elseif ($vimeo) {
        // assumes the Vimeo links look like http://vimeo.com/78254514 where the path string contains the video identifier
        $url=parse_url($vimeo);
        $id=$url['path'];
        $html= '<div class="embed-container vimeo" id="v-streaming" style="padding-top:0; height: 0; padding-top: 25px; padding-bottom: 67.5%; margin-bottom: 10px; position: relative; overflow: hidden;"><iframe style=" top: 0; left: 0; width: 100%; height: 100%; position: absolute;" src="//player.vimeo.com/video'.$id.'?color=222" width="725" height="410" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
        if ($caption==true) {
            $html .= ($title) ? '<h4 class="title video-title sib">'.$title.' <span class="icon-info-sign" aria-hidden="true"></span></h4>' : '';
            $html .= ($desc) ? '<p class="description video-description sib">'.$desc.link_to($file, 'show', '<span class="view-file-link"><span class="icon-file" aria-hidden="true"></span> '.__('View File Details Page').'</span>', array('class'=>'view-file-record','rel'=>'nofollow')).'</p>' : '';
        }
        return '<div class="item-file-container">'.$html.'</div>';
    } else {
        return false;
    }
}


/*
** DISQUS COMMENTS
** disqus.com
*/
function rl_disquss_comments($shortname)
{
   if ($shortname) {
      ?>
      <div id="disqus_thread" class="inner-padding max-content-width">
       <a class="load-comments button" title="Click to load the comments section" href="javascript:void(0)" onclick="disqus();return false;"><?php echo rl_icon('chatbubbles')?> Show Comments</a>
      </div>
      <script>
      var disqus_shortname = "<?php echo $shortname; ?>";
      var disqus_loaded = false;
      
      function disqus() {
       if (!disqus_loaded) {
           disqus_loaded = true;
           var e = document.createElement("script");
           e.type = "text/javascript";
           e.async = true;
           e.src = "//" + disqus_shortname + ".disqus.com/embed.js";
           (document.getElementsByTagName("head")[0] ||
               document.getElementsByTagName("body")[0])
           .appendChild(e);
       }
      }
      </script>
   <?php
   }
}

/*
** DISPLAY COMMENTS
*/
function rl_display_comments()
{
    if (get_theme_option('comments_id')) {
        return rl_disquss_comments(get_theme_option('comments_id'));
    } else {
        return null;
    }
}



/*
** Get total tour items, omitting unpublished items unless logged in
*/
function rl_tour_total_items($tour)
{
    $i=0;
    foreach ($tour->Items as $ti) {
        if ($ti->public || current_user()) {
            $i++;
        }
    }
    return $i;
}

/*
** Display the Tours search results
*/
function rl_tour_preview($s)
{
    $html=null;
    $record=get_record_by_id($s['record_type'], $s['record_id']);
    set_current_record('tour', $record);
    $html.=  '<article>';
    $html.=  '<h3 class="tour-result-title"><a href="'.record_url($record, 'show').'">'.($s['title'] ? $s['title'] : '[Unknown]').'</a></h3>';
    $html.=  '<div class="tour-meta-browse browse-meta-top byline">';
    $html.= '<span class="total">'.rl_tour_total_items($record).' '.__('Locations').'</span> ~ ';
    if (tour('Credits')) {
        $html.=  __('%1s curated by %2s', rl_tour_label('singular'), tour('Credits'));
    } else {
        $html.=  __('%1s curated by %2s', rl_tour_label('singular'), option('site_title'));
    }
    $html.=  '</div>';
    $html.=  ($text=strip_tags(html_entity_decode(tour('Description')))) ? '<span class="tour-result-snippet">'.snippet($text, 0, 300).'</span>' : null;
    if (get_theme_option('show_tour_item_thumbs') == true) {
        $html.=  '<span class="tour-thumbs-container">';
        foreach ($record->Items as $mini_thumb) {
            $html.=  metadata($mini_thumb, 'has thumbnail') ?
              '<div class="mini-thumb">'.item_image('square_thumbnail', array('height'=>'40','width'=>'40'), null, $mini_thumb).'</div>' :
              null;
        }
        $html.=  '</span>';
    }
    $html.= '</article>';
    return $html;
}

/*
** Homepage Featured Items
*/ 
function rl_homepage_featured($num=4,$html=null,$index=1)
{
  if(get_theme_option("homepage_featured_order") !== "none"){
    $orderby = get_theme_option("homepage_featured_order") ? get_theme_option("homepage_featured_order") : "modified";
    $items=get_records('Item', array('featured'=>true,'hasImage'=>true,'sort_field' => $orderby, 'sort_dir' => 'd','public'=>true), $num);
    if(count($items)){
      $html = '<h2 class="query-header">'.__('Featured %s',rl_item_label('plural')).'</h2>';
      $html .= '<div class="featured-card-container">';
        $primary=null;
        $secondary=null;
        foreach($items as $item){
          set_current_record('item', $item);
          if($index == 1){
            if ($item_image = rl_get_first_image_src($item)) {
              $size=getimagesize($item_image);
              $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
            } elseif ($hasImage && (!stripos($img, 'ionicons') && !stripos($img, 'fallback'))) {
              $img = item_image('fullsize');
              preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img, $result);
              $item_image = array_pop($result);
              $size=getimagesize($item_image);
              $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
            }else{
              $orientation=null;
              $item_image=null;
            }          
            $primary .= '<article class="featured-card featured-'.$index.'">';
              $primary .= '<div class="background-image '.$orientation.'" style="background-image:url('.$item_image.')"></div>';
              $primary .= '<div class="background-gradient"></div>';
              $primary .= '<div class="featured-card-inner inner-padding">';
                $primary .= '<div class="featured-card-image">';
                  $primary .= link_to_item('<span class="item-image '.$orientation.'" style="background-image:url('.$item_image.');" role="img" aria-label="Image: '.metadata($item, array('Dublin Core', 'Title')).'"></span>', array('title'=>metadata($item, array('Dublin Core','Title')),'class'=>'image-container'));
                $primary .= '</div>';
                $primary .= '<div class="featured-card-content">';
                  $primary .= rl_filed_under($item);
                  $primary .= '<div class="separator wide thin flush-top"></div>';
                  $primary .= rl_the_title_expanded($item).'<div class="separator"></div>';
                  $primary .= rl_the_byline($item, false);
                $primary .= '</div>';
              $primary .= '</div>';
            $primary .= '</article>';          
          }else{
            $secondary .= '<article class="featured-card featured-'.$index.'">';
                $secondary .= '<div class="featured-card-inner inner-padding">';
                  $secondary .= '<div class="featured-card-content">';
                    $secondary .= rl_filed_under($item);
                    $secondary .= rl_the_title_expanded($item);
                    $secondary .= rl_the_byline($item, false);
                  $secondary .= '</div>';
                $secondary .= '</div>';
            $secondary .= '</article>';          
          }
          $index++;
        }
      $html .= $primary.'<div class="secondary">'.$secondary.'</div>';
      $html .= '</div>';
      $html .= '<div class="view-more-link"><a class="button" href="'.url('items').'?featured=1">'.__('Browse All Featured %2s', rl_item_label('plural')).'</a></div>';
      return '<section id="home-featured" class="inner-padding browse">'.$html.'</section>';
    }else{
      return rl_admin_message('home-featured',array('admin','super'));
    }    
  }      
}

/*
** Homepage Recent/Random Items
*/ 
function rl_homepage_recent_random($num=3,$html=null,$index=1)
{
  if(get_theme_option("random_or_recent") !== "none"){
    $mode = get_theme_option("random_or_recent");
    switch ($mode) {
      case 'recent':
        $items=get_records('Item', array('featured'=>false,'hasImage'=>true,'sort_field' => 'added', 'sort_dir' => 'd','public'=>true), $num);
        $param=__("Recent");
        break;
      case 'random':
        $items=get_records('Item', array('featured'=>false,'hasImage'=>true,'sort_field' => 'random', 'sort_dir' => 'd','public'=>true), $num);;
        $param=__("Discover");
        break;
      case 'modified':
        $items=get_records('Item', array('featured'=>false,'hasImage'=>true,'sort_field' => 'modified', 'sort_dir' => 'd','public'=>true), $num);
        $param=__("Discover");
        break;
    }
    if(count($items)){
      $html = '<h2 class="query-header">'.$param.' '.rl_item_label('plural').'</h2>';
      $html .= '<div class="browse-items">';
        foreach($items as $item){
          set_current_record('item', $item);
          $tags=tag_string(get_current_record('item'), url('items/browse'));
          $hasImage=metadata($item, 'has thumbnail');
          if ($item_image = rl_get_first_image_src($item)) {
            $size=getimagesize($item_image);
            $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
          } elseif ($hasImage && (!stripos($img, 'ionicons') && !stripos($img, 'fallback'))) {
            $img = item_image('fullsize');
            preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img, $result);
            $item_image = array_pop($result);
            $size=getimagesize($item_image);
            $orientation = $size && ($size[0] > $size[1]) ? 'landscape' : 'portrait';
          }else{
            $orientation=null;
            $item_image=null;
          }
          $html .= '<article class="item-result '.($hasImage ? 'has-image' : 'no-image').'">';
          $html .= link_to_item('<span class="item-image '.$orientation.'" style="background-image:url('.$item_image.');" role="img" aria-label="Image: '.metadata($item, array('Dublin Core', 'Title')).'"></span>', array('title'=>metadata($item, array('Dublin Core','Title')),'class'=>'image-container')); 
          $html .= '<div class="result-details">';
          $html .= rl_filed_under($item);
          $html .= rl_the_title_expanded($item);
          $html .= rl_the_byline($item, false);
          //$html .= link_to_item(__('View %s', rl_item_label('singular')),array('class'=>'readmore'));
          $html .= '</div>';
          $html .= '</article>';
        }
      $html .= '</div>';
      $html .= '<div class="view-more-link"><a class="button" href="'.rl_stories_url().'">'.__('Browse All %2s', rl_item_label('plural')).'</a></div>';
      return '<section id="home-recent-random" class="browse inner-padding">'.$html.'</section>';
    }else{
      return rl_admin_message('home-recent-random',array('admin','super'));
    }
  }     
}

/*
** Homepage Tags
*/
function rl_homepage_tags($num=25)
{
  if(get_theme_option("homepage_tags") == "1"){
    $tags=get_records('Tag', array('sort_field' => 'count', 'sort_dir' => 'd','type'=>'item'), $num);
    if(count($tags)){
      $html = '<h2 class="query-header">'.__('Popular Tags').'</h2>';
      $html.=tag_cloud($tags, 'items/browse');
      $html.='<div class="view-more-link"><a class="button" href="'.url('items/tags').'">'.__('Browse All Tags').'</a></div>';
      return '<section id="home-tags" class="inner-padding">'.$html.'</section>';    
    }else{
      return rl_admin_message('home-tags',array('admin','super'));
    }
  }
}

/*
** Homepage Project Meta Text 
*/
function rl_homepage_projectmeta($html=null,$length=800)
{
  $heading = get_theme_option('homepage_meta_placement') == 'top' ? option('site_title') : __('Project Meta');
  $cta = rl_homepage_cta();
  $text = get_theme_option('about') 
    ? strip_tags(get_theme_option('about'), '<a><em><i><cite><strong><b><u>') 
    : __('%s is powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://curatescape.org/">Curatescape</a>, a humanities-centered web and mobile app framework available for both Android and iOS devices.', option('site_title'));
  $html .= '<h2 class="query-header">'.$heading.'</h2>';
  $html .= '<div class="home-project-meta">';
    $html .= '<div id="home-about-main" class="inner-padding">'; 
      $html .= '<h3 class="query-header">'.__('About').'</h3>';
      $html .= '<p>'.substr($text, 0, $length).(($length < strlen($text)) ? '&hellip;. ' : null).'</p>';
      $html .= '<div class="about-link"><a class="button" href="'.url('about').'">'.__('Read More About Us').'</a></div>';
    $html .= '</div>';
    $html .= $cta;
  $html .= '</div>';
  
  return '<section id="home-about" class="inner-padding">'.$html.'</section>';
}


/*
** Homepage Call to Action 
*/
function rl_homepage_cta($html=null,$length=800){
  $cta_title=get_theme_option('cta_title');
  $cta_text=strip_tags(get_theme_option('cta_text'),'<a><em><i><cite><strong><b><u>');
  $cta_button_label=get_theme_option('cta_button_label');
  $cta_button_url=get_theme_option('cta_button_url');
  $cta_button_url_target=get_theme_option('cta_button_url_target') ? ' target="_blank" rel="noreferrer noopener"' : null;
  if($cta_title && $cta_text && $cta_button_label && $cta_button_url){
    $html = '<h3 class="query-header">'.$cta_title.'</h3>';
    $html .= '<div class="cta-main">';
      $html .= '<div class="cta-text">'; 
        $html .= '<p>'.substr($cta_text, 0, $length).(($length < strlen($cta_text)) ? '&hellip; ' : null).'</p>';
      $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="cta-link"><a '.$cta_button_url_target.' class="button" href="'.$cta_button_url.'">'.$cta_button_label.'</a></div>';
    $display_class = null;
  }else{
    $display_class = 'empty-cta';
    $html .= rl_admin_message('home-cta',array('admin','super'));
  }
  return '<aside id="home-cta" class="inner-padding '.$display_class.'">'.$html.'</aside>';
}

/*
** Homepage Stealth Mode Message 
*/
function rl_homepage_stealthmode($html = null)
{
  $html .= link_to_home_page(rl_the_logo(), array('class'=>'wiggle', 'aria-label'=>'Logo'));
  $html .= '<h3 class="query-header">'.__("Check Back Soon").'</h3>';
  $html .= '<div class="stealth-main">';
    $html .= '<div class="stealth-text">'; 
      $html .= '<p>'.__('%s is temporarily unavailable.','<strong>'.option('site_title').'</strong>').'</p>';
    $html .= '</div>';
  $html .= '</div>';
  return '<section id="home-stealth" class="inner-padding">'.$html.'</section>';
}

/*
** Display the Tours list
*/
function rl_homepage_tours($html=null, $num=4, $scope='featured')
{
  if(plugin_is_active('TourBuilder') && (get_theme_option('homepage_tours_scope') !== "none")){
    // Build query
    $scope=get_theme_option('homepage_tours_scope') ? get_theme_option('homepage_tours_scope') : $scope;
    $db = get_db();
    $table = $db->getTable('Tour');
    $select = $table->getSelect();
    $select->where('public = 1');
    $public = $table->fetchObjects($select);
    switch ($scope) {
      case 'random':
        $select->from(array(), 'RAND() as rand');
        break;
      case 'featured':
        $select->where('featured = 1');
        break;
    }
    $tours = $table->fetchObjects($select);
    
    // section heading
    $customheader=get_theme_option('tour_header');
    if ($scope=='random') {
      shuffle($tours);
      $heading = $customheader ? $customheader : __('Take a').' '.rl_tour_label('singular');
    } else {
      $heading = $customheader ? $customheader : ucfirst($scope).' '.rl_tour_label('plural');
    }
    
    // output
    if ($tours) {
      $html .= '<h2 class="query-header">'.$heading.'</h2>';
      $html .= '<div class="home-tours-container">';
      for ($i = 0; $i < min(count($tours),$num); $i++) {
        set_current_record('tour', $tours[$i]);
        $tour=get_current_tour();
        $bg=array();
        if ($touritems = $tour->getItems()) {
            foreach ($touritems as $ti) {
                if (count($bg) == 4) {
                    break;
                }
                if ($src=rl_get_first_image_src($ti, 'square_thumbnails')) {
                    $bg[]='url('.$src.')';
                }
            }
        }
        $html .= '<article class="item-result tour">';
          $html .= '<a aria-label="'.tour('title').'" class="tour-image '.(count($bg) < 4 ? 'single' : 'multi').'" style="background-image:'.implode(',', $bg).'" href="'.WEB_ROOT.'/tours/show/'.tour('id').'"></a><div class="separator thin flush-bottom flush-top"></div>';
          $html .= '<div class="tour-inner">';
            $html .= '<a class="permalink" href="' . WEB_ROOT . '/tours/show/'. tour('id').'"><h3 class="title">' . tour('title').'</h3></a>'.
                '<span class="byline">'.rl_icon('compass').__('%s Locations', rl_tour_total_items($tours[$i])).'</span>';
            $html .= '<p class="tour-snip">'.snippet(strip_tags(htmlspecialchars_decode(tour('description'))), 0, 200).'</p>';
          $html .= '</div>';
        $html .= '</article>';
      }
      $html .= '</div>';
      $html .= '<div class="view-more-link"><a class="button" href="'.WEB_ROOT.'/tours/browse/">'.__('Browse All <span>%s</span>', rl_tour_label('plural')).'</a></div>';
      return '<section id="home-tours" class="browse inner-padding">'.$html.'</section>';
    } else {
      return rl_admin_message('home-tours',array('admin','super'));
    }
  }else{
    return null;
  }
}

// return story navigation and (when applicable) tour navigation
function rl_story_nav($has_images=0, $has_audio=0, $has_video=0, $has_other=0, $has_location=false, $tour=false, $tour_index=false)
{
    $totop = '<li class="foot"><a title="'.__('Return to Top').'" class="icon-capsule no-bg" href="#site-content">'.rl_icon("arrow-up").'<span class="label">'.__('Top').'</span></a></li>';

    // Media List HTML
    $media_list = null;
    if ($has_video) {
        $media_list .= '<li><a title="'.__('Skip to %s', __('Video')).'" class="icon-capsule" href="#video">'.rl_icon("film").'<span class="label">'.__('Video').' ('.$has_video.')</span></a></li>';
    }
    if ($has_audio) {
        $media_list .= '<li><a title="'.__('Skip to %s', __('Audio')).'" class="icon-capsule" href="#audio">'.rl_icon("headset").'<span class="label">'.__('Audio').' ('.$has_audio.')</span></a></li>';
    }
    if ($has_images) {
        $media_list .= '<li><a title="'.__('Skip to %s', __('Images')).'" class="icon-capsule" href="#images">'.rl_icon("images").'<span class="label">'.__('Images').' ('.$has_images.')</span></a></li>';
    }
    if ($has_other) {
        $media_list .= '<li><a title="'.__('Skip to %s', __('Documents')).'" class="icon-capsule" href="#documents">'.rl_icon("documents").'<span class="label">'.__('Documents').' ('.$has_other.')</span></a></li>';
    }

    $tournav = null;
    if ($tour && isset($tour_index)) {
        $index = $tour_index;
        $tour_id = $tour;
        $tour = get_record_by_id('tour', $tour_id);
        $prevIndex = $index -1;
        $nextIndex = $index +1;
        $tourTitle = metadata($tour, 'title');
        $tourURL = html_escape(public_url('tours/show/'.$tour_id));

        $current = tour_item_id($tour, $index);
        $next = tour_item_id($tour, $nextIndex);
        $prev = tour_item_id($tour, $prevIndex);

        $tournav .= '<ul class="tour-nav">';
        $tournav .= '<li class="head"><span title="'.__('%s Navigation', rl_tour_label('singular')).'" class="icon-capsule label">'.rl_icon("list").'<span class="label">'.__('%s Navigation', rl_tour_label('singular')).'</span></span></li>';
        $tournav .= $prev ? '<li><a title="'.__('Previous Loction').'" class="icon-capsule" href="'.public_url("items/show/$prev?tour=$tour_id&index=$prevIndex").'">'.rl_icon("arrow-back").'<span class="label">'.__('Previous').'</span></a></li>' : null;
        $tournav .= '<li class="info"><a title="'.__('%s Info', rl_tour_label('singular')).': '.$tourTitle.'" class="icon-capsule" href="'.$tourURL.'">'.rl_icon("compass").'<span class="label">'.__('%s Info', rl_tour_label('singular')).'</span></a></li>';
        $tournav .= $next ? '<li><a title="'.__('Next Location').'" class="icon-capsule" href="'.public_url("items/show/$next?tour=$tour_id&index=$nextIndex").'">'.rl_icon("arrow-forward").'<span class="label">'.__('Next').'</span></a></li>' : null;
        $tournav .= '</ul>';
    }

    // Location HTML
    $location = null;
    if ($has_location && plugin_is_active('Geolocation')) {
        $location .= '<li><a title="'.__('Skip to %s', __('Map Location')).'" class="icon-capsule" href="#map-section">'.rl_icon("location").'<span class="label">'.__('Location').'</span></a></li>';
    }

    // Output HTML
    $html = '<nav class="rl-toc"><ul>'.
      '<li class="head"><span title="'.__('%s Contents', rl_item_label('singular')).'" class="icon-capsule label">'.rl_icon("list").'<span class="label">'.__('%s Contents', rl_item_label('singular')).'</span></span></li>'.
      '<li><a title="'.__('Skip to Main Text').'" class="icon-capsule" href="#text-section">'.rl_icon("book").'<span class="label">'.__('Main Text').'</span></a></li>'.
      $media_list.
      $location.
      '<li><a title="'.__('Skip to %s', __('Metadata')).'" class="icon-capsule" href="#metadata-section">'.rl_icon("pricetags").'<span class="label">'.__('Metadata').'</span></a></li>'.
      $totop.
      '</ul>'.$tournav.'</nav>';

    return $html;
}

// an array of files for the item, sorted by type
function rl_item_files_by_type($item=null, $output=null)
{
    $output=array(
       'images'=>array(),
       'audio'=>array(),
       'video'=>array(),
       'other'=>array()
      );

    if (metadata($item, 'has files')) {
        foreach (loop('files', $item->Files) as $file) {
            $mime = $file->mime_type;
            switch ($mime) {
               case strpos($mime, 'image') !== false:
               $src=str_ireplace(array('.JPG','.jpeg','.JPEG','.png','.PNG','.gif','.GIF', '.bmp','.BMP'), '.jpg', $file->filename);
               $size=getimagesize(WEB_ROOT.'/files/fullsize/'.$src);
               $orientation = $size[0] > $size[1] ? 'landscape' : 'portrait';
               array_push(
                  $output['images'],
                  array(
                  'title'=>metadata($file, array('Dublin Core','Title')),
                  'id'=>$file->id,
                  'src'=>$src,
                  'caption'=>rl_file_caption($file),
                  'size'=>array($size[0],$size[1]),
                  'orientation'=>$orientation)
               );
               break;
               case strpos($mime, 'audio') !== false:
               array_push($output['audio'], array('id'=>$file->id, 'src'=>$file->filename,'caption'=>rl_file_caption($file), 'date'=>$file->added));
               break;
               case strpos($mime, 'video') !== false:
               array_push($output['video'], array('id'=>$file->id, 'src'=>$file->filename,'caption'=>rl_file_caption($file), 'date'=>$file->added));
               break;
               default:
               array_push($output['other'], array('id'=>$file->id, 'src'=>$file->filename,'size'=>$file->size,'title'=>metadata($file, array('Dublin Core','Title')),'filename'=>$file->original_filename));
            }
        }
    }
    return $output;
}

/*
These images load via js unless the $class is set to "featured" (i.e. in article header)
Should be used with rl_nojs_images() for users w/o js
*/
function rl_gallery_figure($image=null, $class=null, $hrefOverride=null)
{
    if (isset($image) && $image['src']) {
        $src = WEB_ROOT.'/files/fullsize/'.$image['src'];
        $url = WEB_ROOT.'/files/show/'.$image['id'];
        $data_or_style_attr = $class == 'featured' ? 'style' : 'data-style';
        $html = '<figure class="image-figure '.$class.'" itemscope itemtype="http://schema.org/ImageObject">';
          if($hrefOverride){
            $html .= '<div itemprop="associatedMedia" class="gallery-image '.$image['orientation'].' file-'.$image['id'].'" '.$data_or_style_attr.'="background-image:url('.$src.')" data-pswp-width="'.$image['size'][0].'" data-pswp-height="'.$image['size'][1].'"></div>';
          }else{
            $html .= '<a itemprop="associatedMedia" aria-label="Image: '.$image['title'].'" href="'.$src.'" class="gallery-image '.$image['orientation'].' file-'.$image['id'].'" '.$data_or_style_attr.'="background-image:url('.$src.')" data-pswp-width="'.$image['size'][0].'" data-pswp-height="'.$image['size'][1].'"></a>';
          }
          $html .= '<figcaption>'.$image['caption'].'</figcaption>';
        $html .= '</figure>';
        return $html;
    }else{
      return null;
    }
}

/*
These fallback styles load in a <noscript> tag
*/
function rl_nojs_images($images=array(), $css=null)
{
    foreach ($images as $img) {
        $css .= '.file-'.$img['id'].'{background-image:url('.WEB_ROOT.'/files/fullsize/'.$img['src'].');}';
    }
    return '<style>'.$css.'</style>';
}

function rl_nojs_map(){
   return '<style>a.showonmap,#show-multi-map,.sep-bar{display:none;}</style>';
}

function rl_hero_item($item)
{
    $itemTitle = rl_the_title_expanded($item);
    $itemDescription = rl_snippet_expanded($item);
    $class=get_theme_option('featured_tint')==1 ? 'tint' : 'no-tint';
    $html=null;

    if (metadata($item, 'has thumbnail')) {
        $img_markup=item_image('fullsize', array(), 0, $item);
        preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img_markup, $result);
        $img_url = array_pop($result);
        $html .= '<article class="featured-story-result '.$class.'" style="background-image:url('.$img_url.')">';
        $html .= '<div class="featured-decora-outer">' ;
        $html .= '<div class="featured-decora-bg" >' ;

        $html .= '<div class="featured-decora-text"><div class="featured-decora-text-inner">';
        $html .= '<header><h3>' . link_to_item($itemTitle, array(), 'show', $item) . '</h3><span class="featured-item-author">'.rl_the_byline($item, false).'</span></header>';
        if ($itemDescription) {
            $html .= '<div class="item-description">' . strip_tags($itemDescription) . '</div>';
        } else {
            $html .= '<div class="item-description">'.__('Preview text not available.').'</div>';
        }

        $html .= '</div></div>' ;

        $html .= '</div></div>' ;
        $html .= '</article>';
    }

    return $html;
}

/*
** Display random featured item(s)
*/
function rl_display_random_featured_item($withImage=false, $num=1)
{
    $featuredItems = get_random_featured_items($num, $withImage);
    $html = '<h3 class="result-type-header">'.__('Featured %s', rl_item_label('plural')).'</h3>';

    if ($featuredItems) {
        foreach ($featuredItems as $item):
                     $html .=rl_hero_item($item);
        endforeach;

        $html.='<p class="view-more-link"><a class="button" href="'.url('items').'?featured=1">'.__('Browse Featured %s', rl_item_label('plural')).'</a></p>';
    } else {
        $html .= '<article class="featured-story-result none">';
        $html .= '<p>'.__('No featured items are available. Publish some now.').'</p>';
        $html .= '</article>';
    }

    return $html;
}


function rl_footer_cta($html=null)
{
    $footer_cta_button_label=get_theme_option('footer_cta_button_label');
    $footer_cta_button_url=get_theme_option('footer_cta_button_url');
    $footer_cta_button_target=get_theme_option('footer_cta_button_target') ? 'target="_blank" rel="noreferrer noopener"' : null;
    if ($footer_cta_button_label && $footer_cta_button_url) {
        $html.= '<div class="footer_cta"><a class="button button-primary" href="'.$footer_cta_button_url.'" '.$footer_cta_button_target.'>'.$footer_cta_button_label.'</a></div>';
    }
    return $html;
}

/*
** Build an array of social media links (including icons) from theme settings
*/
function rl_social_array($max=5)
{
   $services=array();
   ($email=get_theme_option('contact_email') ? get_theme_option('contact_email') : get_option('administrator_email')) ? array_push($services, '<a target="_blank" rel="noopener" title="email" href="mailto:'.$email.'" class="button social icon-round email">'.rl_icon("mail").'</a>') : null;
   ($facebook=get_theme_option('facebook_link')) ? array_push($services, '<a target="_blank" rel="noopener" title="facebook" href="'.$facebook.'" class="button social icon-round facebook">'.rl_icon("logo-facebook", null).'</a>') : null;
   ($twitter=get_theme_option('twitter_username')) ? array_push($services, '<a target="_blank" rel="noopener" title="twitter" href="https://twitter.com/'.$twitter.'" class="button social icon-round twitter">'.rl_icon("logo-twitter", null).'</a>') : null;
   ($youtube=get_theme_option('youtube_username')) ? array_push($services, '<a target="_blank" rel="noopener" title="youtube" href="'.$youtube.'" class="button social icon-round youtube">'.rl_icon("logo-youtube", null).'</a>') : null;
   ($instagram=get_theme_option('instagram_username')) ? array_push($services, '<a target="_blank" rel="noopener" title="instagram" href="https://www.instagram.com/'.$instagram.'" class="button social icon-round instagram">'.rl_icon("logo-instagram", null).'</a>') : null;
   ($mastodon=get_theme_option('mastodon_link')) ? array_push($services, '<a target="_blank" rel="noopener" title="mastodon" href="'.$mastodon.'" class="button social icon-round mastodon">'.rl_icon("logo-mastodon", null).'</a>') : null;
   ($tiktok=get_theme_option('tiktok_link')) ? array_push($services, '<a target="_blank" rel="noopener" title="tiktok" href="'.$tiktok.'" class="button social icon-round tiktok">'.rl_icon("logo-tiktok", null).'</a>') : null;
   ($pinterest=get_theme_option('pinterest_username')) ? array_push($services, '<a target="_blank" rel="noopener" title="pinterest" href="https://www.pinterest.com/'.$pinterest.'" class="button social icon-round pinterest">'.rl_icon("logo-pinterest", null).'</a>') : null;
   ($tumblr=get_theme_option('tumblr_link')) ? array_push($services, '<a target="_blank" rel="noopener" title="tumblr" href="'.$tumblr.'" class="button social icon-round tumblr">'.rl_icon("logo-tumblr", null).'</a>') : null;
   ($reddit=get_theme_option('reddit_link')) ? array_push($services, '<a target="_blank" rel="noopener" title="reddit" href="'.$reddit.'" class="button social icon-round reddit">'.rl_icon("logo-reddit", null).'</a>') : null;
   
   if (($total=count($services)) > 0) {
      if ($total>$max) {
         for ($i=$total; $i>($max-1); $i--) {
            unset($services[$i]);
         }
      }
      return $services;
   } else {
      return false;
   }
}

/*
** Build a series of social media icon links for the footer
*/
function rl_find_us($class=null, $max=5)
{
   if ($services=rl_social_array($max)) {
      return '<div class="link-icons '.$class.'">'.implode(' ', $services).'</div>';
   }
}

/*
** Build a series of icon action buttons for the story (i.e. print/share)
** @todo: https://css-tricks.com/simple-social-sharing-links/
*/
function rl_story_actions($class=null, $title=null, $id=null)
{
   $url=WEB_ROOT.'/items/show/'.$id;
   $actions = array(
      '<a rel="noopener" title="print" href="javascript:void" onclick="window.print();" class="button social icon-round">'.rl_icon("print").'</a>',
      '<a target="_blank" rel="noopener" title="email" href="'.w3_valid_url('mailto:?subject='.$title.'&body='.$url).'" class="button social icon-round">'.rl_icon("mail").'</a>',
      '<a target="_blank" rel="noopener" title="facebook" href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($url).'" class="button social icon-round">'.rl_icon("logo-facebook", null).'</a>',
      '<a target="_blank" rel="noopener" title="twitter" href="https://twitter.com/intent/tweet?text='.urlencode($url).'" class="button social icon-round">'.rl_icon("logo-twitter", null).'</a>'
   );
   return '<div class="link-icons '.$class.'">'.implode(' ', $actions).'</div>';
}


/*
** Build a link for the footer copyright statement and credit line on homepage
*/
function rl_owner_link()
{
   $fallback=(option('author')) ? option('author') : option('site_title');
   $authname=(get_theme_option('sponsor_name')) ? get_theme_option('sponsor_name') : $fallback;
   return $authname;
}

/*
** Icon PNG file for mobile device bookmarks
*/
function rl_touch_icon_url()
{
   $touch_icon = get_theme_option('apple_icon_144');
   $url = $touch_icon ? WEB_ROOT.'/files/theme_uploads/'.$touch_icon : img('favicon.png');
   return $url;
}

/*
** Icon SVG file for modern browser tabs
*/
function rl_favicon_svg_url()
{
   $favicon_svg = get_theme_option('favicon_svg');
   $url = $favicon_svg ? WEB_ROOT.'/files/theme_uploads/'.$favicon_svg : img('favicon.svg');
   return $url;
}

/*
** Icon ICO file for older browser tabs
*/
function rl_favicon_ico_url()
{
   $favicon = get_theme_option('favicon');
   $url = $favicon ? WEB_ROOT.'/files/theme_uploads/'.$favicon : img('favicon.ico');
   return $url;
}


/*
** Custom/Configured CSS
*/
function rl_configured_css($vars=null, $output=null)
{
  $vars .= get_theme_option('link_color') ? '--link-text:'.get_theme_option('link_color').';' : null;
  $vars .= get_theme_option('link_color_hover') ? '--link-text-hover:'.get_theme_option('link_color_hover').';' : null;
  $vars .= get_theme_option('secondary_link_color') ? '--link-text-on-dark:'.get_theme_option('secondary_link_color').';' : null;
  $vars .= get_theme_option('secondary_link_color_hover') ? '--link-text-on-dark-hover:'.get_theme_option('secondary_link_color_hover').';' : null;
  $vars .= get_theme_option('header_footer_color') ? '--site-header-bg-color-1:'.get_theme_option('header_footer_color').';' : null;
  $vars .= get_theme_option('secondary_header_footer_color') ? '--site-header-bg-color-2:'.get_theme_option('secondary_header_footer_color').';': null;
  $vars .= get_theme_option('cluster_text_color') ? '--cluster-text-color:'.get_theme_option('cluster_text_color').';' : null;      
  $vars .= get_theme_option('cluster_large_color') ? '--cluster-large-color:'.get_theme_option('cluster_large_color').';' : null; 
  $vars .= get_theme_option('cluster_medium_color') ? '--cluster-medium-color:'.get_theme_option('cluster_medium_color').';' : null; 
  $vars .= get_theme_option('cluster_small_color') ? '--cluster-small-color:'.get_theme_option('cluster_small_color').';' : null;
  $vars .= get_theme_option('header_footer_color') ? '--featured-one: '.get_theme_option('header_footer_color').';' : null;
  $vars .= get_theme_option('logo_size_adjust') ? '--site-header-height: var(--site-header-height-tall);' : null;
  $vars .= get_theme_option('logo_background_color') ? '--site-header-bg-logo: '.get_theme_option('logo_background_color').';' : null;
  
  if ($vars) {
    $output .= ':root {'.$vars.'}';
  }
  if(get_theme_option('enable_dark_mode')){
    $output .= '@media screen and (prefers-color-scheme: dark) {
      :root.darkallowed {
      --link-text: var(--link-text-on-dark) !important;--link-text-hover: var(--link-text-on-dark-hover) !important;--bg-body: var(--bg-body-dark);--bg-article: var(--bg-article-dark);--text-base: var(--text-base-on-dark);--text-heading: var(--text-heading-on-dark);--text-subheading: var(--text-subheading-on-dark);--text-caption: var(--text-caption-on-dark);--deco-color: var(--dark-primary);--deco-color-subtle: var(--dark-tertiary);--deco-frame: 10px solid var(--dark-secondary);--deco-frame-small: 3px solid var(--dark-secondary);--light-secondary: var(--dark-tertiary);--light-primary-subtle: var(--dark-tertiary);--article-header-bg-gradient: linear-gradient(to top,rgba(34, 34, 34, 1) 50%,rgba(34, 34, 34, 0.95),rgba(34, 34, 34, 0.9),rgba(34, 34, 34, 0.75),rgba(34, 34, 34, 0.5));
      }
      .darkallowed .featured-card.featured-1 .separator,.darkallowed footer .separator {background: var(--link-text) !important;}
      .darkallowed article header .background-image {filter: grayscale(1) brightness(0.8);}
      .darkallowed .gallery-image,.darkallowed .curatescape-map,.darkallowed .browse .item-image,.darkallowed .item-result.tour .tour-image {filter: brightness(0.8);}
      .darkallowed form.capsule input.search,.darkallowed form.capsule input.search:focus::placeholder, .darkallowed form.capsule input.search:hover::placeholder,.darkallowed form.capsule input.search:hover, .darkallowed form.capsule input.search:focus{color:#000;}
    }';
  }
  if (get_theme_option('custom_css')) {
    $output .= get_theme_option('custom_css');
  }
  return $output;
}


/*
** Which fonts/service to use?
** Adobe/Typekit, FontDeck, Monotype, Google Fonts, or default (null)
*/
function rl_font_config()
{
    if ($tk=get_theme_option('typekit')) {
        $config="typekit: { id: '".$tk."' }";
    } elseif ($fd=get_theme_option('fontdeck')) {
        $config="fontdeck: { id: '".$fd."' }";
    } elseif ($fdc=get_theme_option('fonts_dot_com')) {
        $config="monotype: { projectId: '".$fdc."' }";
    } elseif ($gf=get_theme_option('google_fonts')) {
        $config="google: { families: [".$gf."] }";
    } else {
        $config=null;
    }
    return $config;
}


/*
** Load font service or default (see: fonts/fonts.css)
** Web Font Loader async script
** https://developers.google.com/fonts/docs/webfont_loader
*/
function rl_font_loader()
{
   if (rl_font_config()) { ?>
      <script>
      WebFontConfig = {
      <?php echo rl_font_config(); ?>
      };
      (function(d) {
      var wf = d.createElement('script'),
      s = d.scripts[0];
      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
      wf.async = true;
      s.parentNode.insertBefore(wf, s);
      })(document);
      </script>
   <?php } else { ?>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link href="<?php echo src('fonts.css', 'fonts');?>" media="all" rel="stylesheet">
   <?php }
}

/*
** Google Analytics
** Theme option: google_analytics
** Accepts G- and UA- measurement IDs
*/
function rl_google_analytics()
{
   $id=get_theme_option('google_analytics');
   if ($id):
      if (substr($id, 0, 2) == 'G-'): ?>
         <!-- GA -->
         <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $id; ?>"></script>
         <script>
         window.dataLayer = window.dataLayer || [];
         
         function gtag() {
            dataLayer.push(arguments);
         }
         gtag('js', new Date());
         gtag('config', '<?php echo $id; ?>', {
            cookie_flags: 'SameSite=None;Secure'
         });
         </script>
      
      <?php elseif (substr($id, 0, 3) == 'UA-'): ?>
         <!-- GA (Legacy) -->
         <script>
         var _gaq = _gaq || [];
         _gaq.push(['_setAccount', '<?php echo $id; ?>']);
         _gaq.push(['_trackPageview']);
         (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
         })();
         </script>
      <?php endif;
   endif;
}

/*
** About text
*/
function rl_about($text=null)
{
    if (!$text) {
      // If the 'About Text' option has a value, use it. Otherwise, use default text
      $text = get_theme_option('about') ?
      strip_tags(get_theme_option('about'), '<a><em><i><cite><strong><b><u><br><img><video><iframe>') :
      __('%s is powered by <a href="http://omeka.org/">Omeka</a> + <a href="http://curatescape.org/">Curatescape</a>, a humanities-centered web and mobile framework available for both Android and iOS devices.', option('site_title'));
    }
    return $text;
}

/*
**
*/
function rl_license()
{
    $cc_license=get_theme_option('cc_license');
    $cc_version=get_theme_option('cc_version');
    $cc_jurisdiction=get_theme_option('cc_jurisdiction');
    $cc_readable=array(
             '1'=>'1.0',
             '2'=>'2.0',
             '2-5'=>'2.5',
             '3'=>'3.0',
             '4'=>'4.0',
             'by'=>'Attribution',
             'by-sa'=>'Attribution-ShareAlike',
             'by-nd'=>'Attribution-NoDerivs',
             'by-nc'=>'Attribution-NonCommercial',
             'by-nc-sa'=>'Attribution-NonCommercial-ShareAlike',
             'by-nc-nd'=>'Attribution-NonCommercial-NoDerivs'
      );
    $cc_jurisdiction_readable=array(
             'intl'=>'International',
             'ca'=>'Canada',
             'au'=>'Australia',
             'uk'=>'United Kingdom (England and Whales)',
             'us'=>'United States'
      );
    if ($cc_license != 'none') {
        return __('This work is licensed by '.rl_owner_link().' under a <a rel="license" href="http://creativecommons.org/licenses/'.$cc_license.'/'.$cc_readable[$cc_version].'/'.($cc_jurisdiction !== 'intl' ? $cc_jurisdiction : null).'">Creative Commons '.$cc_readable[$cc_license].' '.$cc_readable[$cc_version].' '.$cc_jurisdiction_readable[$cc_jurisdiction].' License</a>.');
    } else {
        return __('&copy; %1$s %2$s', date('Y'), rl_owner_link());
    }
}

/*
** iOS Smart Banner
** Shown not more than once per day
*/
function rl_ios_smart_banner()
{
    // show the iOS Smart Banner once per day if the app ID is set
    $appID = (get_theme_option('ios_app_id')) ? get_theme_option('ios_app_id') : false;
    if ($appID != false) {
        $AppBanner = 'Curatescape_AppBanner_'.$appID;
        $numericID=str_replace('id', '', $appID);
        if (!isset($_COOKIE[$AppBanner])) {
            echo '<meta name="apple-itunes-app" content="app-id='.$numericID.'">';
            setcookie($AppBanner, true, time()+86400); // 1 day
        }
    }
}

/*
** https://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
*/
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' kB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

?>