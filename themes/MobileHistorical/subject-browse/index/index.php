<?php head(array('title'=>'Browse Items by Subject','bodyid'=>'subject-browse','bodyclass' => 'subject-browse current')); ?>
<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function(){
        var path = location.pathname;
        var search = location.search;
        var hash = location.hash;
        var pat = path + search + hash;

                jQuery(".navigation a").parent().removeClass("current");
                jQuery(".navigation a[href=" + pat  + "]").parent().addClass("current");
        });
</script>
<?php
  $db = get_db();
  $select = "SELECT DISTINCT et.text
             FROM " . $db->ElementTexts ." et
             JOIN ". $db->Elements . " e
             ON et.element_id = e.id
             WHERE e.name = 'Subject'
             AND e.element_set_id =
                 (SELECT id
                  FROM " . $db->ElementSets . " es
                  WHERE es.name = 'Dublin Core')
             ORDER BY et.text";
             $result = $db->fetchAll($select);
?>


<div id="content">
			
		    <div id="header">
			<div id="primary-nav">
    			<ul class="navigation">
    			    <?php echo mh_global_nav('desktop'); ?>
    			</ul>
    		</div>
    		<div id="search-wrap">
				    <?php echo simple_search(); ?>
    			</div>
    			<div style="clear:both;"></div>
   		</div>
	




<!-- -->
<div id="page-col-left">

<div id="lv-logo"><a href="<?php echo WEB_ROOT;?>/"><img src="<?php echo mh_med_logo_url(); ?>" border="0" alt="<?php echo settings('site_title');?>" title="<?php echo settings('site_title');?>" /></a></div>

<!--
<h3>Tags:</h3>
<?php
$tags = get_tags(array('sort' => 'alpha'), 20); 
echo tag_cloud($tags, uri('items/browse'));
?>
-->

</div>


	<div id="primary-browse" class="browse">

	
      <h1>Browse By Subject: (<?php echo count($result); ?> Headings)</h1>
  		<ul class="items-nav navigation" id="secondary-nav">
    <?php echo nav(array('Browse All' => uri('items/browse'), 'Browse by Tag' => uri('items/tags'), 'Browse by Subject' => uri('items/subject-browse'))); ?>
		</ul>
                <div class="pagination sb-pagination" id="pagination-top"><ul class="pagination_list">
                      <!-- Alphabetical Helpers -->
                      <?php echo '<li class="pagination_range"><a href="#number">#0-9</a></li>';
                            foreach(range('A','Z') as $i) {echo "<li class='pagination_range' style='float:none;'><a href='#$i'>$i</a></li>";}?>                      
                                              </ul>
                                              </div>
                      <div id="sb-subject-headings">
                        <?php
                        $current_header = '';
                        foreach ($result as $row) {
                          $first_char = substr($row['text'],0,1);
                          if (preg_match('/\W|\d/',$first_char )){
                            $first_char = '#0-9';
                          }
                          if ($current_header !== $first_char){
                            $current_header = $first_char;
                            if ($current_header === '#0-9'){
                              echo "<h3 class='sb-subject-heading' id='number'>$current_header</h3>";
                            }
                            else {
                              echo "<h3 class='sb-subject-heading' id='$current_header'>$current_header</h3>";
                            }
                          }
                          
                          echo '<p class="sb-subject"><a href="' . uri('items/browse?term='.urlencode($row['text']).'&search=&advanced[0][element_id]=' . SUBJECT_BROWSE_DC_ID . '&advanced[0][type]=contains&advanced[0][terms]='. urlencode($row['text']) .'&submit_search=Search') . '">' . $row['text'] . '</a></p>';
                        }
                        ?>
                        
                        </div>
                        
                      <div class="pagination sb-pagination" id="pagination-bottom"><ul class="pagination_list">
                      <!-- Alphabetical Helpers -->
                      <?php echo '<li class="pagination_range"><a href="#number">#0-9</a></li>';
                            foreach(range('A','Z') as $i) {echo "<li class='pagination_range' style='float:none;'><a href='#$i'>$i</a></li>";}?>
                      
                      
                                              </ul>
                                              </div>
		
			
	</div><!-- end primary -->
	

<!-- -->



<?php foot(); ?>