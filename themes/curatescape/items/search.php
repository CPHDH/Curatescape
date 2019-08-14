<?php 
$query = (isset($_GET['query']) ? htmlspecialchars($_GET['query']) : null);
$searchRecordTypes = get_search_record_types();
$title = __('Search %s', mh_item_label('plural'));
$bodyclass ='browse advanced-search'.(current_user() ? ' logged-in' : null);
$maptype='none';


echo head(array('maptype'=>$maptype,'title'=>$title,'bodyid'=>'search','bodyclass'=>$bodyclass)); 
?>


<div id="content">

<article class="search browse">	
	<h2 class="query-header"><?php echo $title; ?></h2>
		


	<div id="primary" class="browse">
	<section id="results">
		<h2 hidden class="hidden"><?php echo __('Search Results');?></h2>
			
		<nav class="secondary-nav" id="item-browse"> 
			<?php echo mh_item_browse_subnav();?>
		</nav>
		
		<!-- Search form via application/views/scripts/items/search-form.php  -->

		<?php
		if (!empty($formActionUri)):
		    $formAttributes['action'] = $formActionUri;
		else:
		    $formAttributes['action'] = url(array('controller' => 'items',
		                                          'action' => 'browse'));
		endif;
		$formAttributes['method'] = 'GET';
		?>
		
		<form <?php echo tag_attributes($formAttributes); ?>>
		    <div id="search-keywords" class="field">
		        <?php echo $this->formLabel('keyword-search', __('Search for Keywords')); ?>
		        <div class="inputs">
		        <?php
		            echo $this->formText(
		                'search',
		                @$_REQUEST['search'],
		                array('id' => 'keyword-search', 'size' => '40')
		            );
		        ?>
		        </div>
		    </div>
		    <div id="search-narrow-by-fields" class="field">
		        <div class="label"><?php echo __('Narrow by Specific Fields'); ?></div>
		        <div class="inputs">
		        <?php
		        // If the form has been submitted, retain the number of search
		        // fields used and rebuild the form
		        if (!empty($_GET['advanced'])) {
		            $search = $_GET['advanced'];
		        } else {
		            $search = array(array('field' => '', 'type' => '', 'value' => ''));
		        }
		
		        //Here is where we actually build the search form
		        foreach ($search as $i => $rows): ?>
		            <div class="search-entry">
		                <?php
		                //The POST looks like =>
		                // advanced[0] =>
		                //[field] = 'description'
		                //[type] = 'contains'
		                //[terms] = 'foobar'
		                //etc
		                echo $this->formSelect(
		                    "advanced[$i][joiner]",
		                    @$rows['joiner'],
		                    array(
		                        'title' => __("Search Joiner"),
		                        'id' => 'id-advanced-search-joiner',
		                        'class' => 'advanced-search-joiner'
		                    ),
		                    array(
		                        'and' => __('AND'),
		                        'or' => __('OR'),
		                    )
		                );
		                echo $this->formSelect(
		                    "advanced[$i][element_id]",
		                    @$rows['element_id'],
		                    array(
		                        'title' => __("Search Field"),
		                        'id' => 'id-advanced-search-element',
		                        'class' => 'advanced-search-element'
		                    ),
		                    get_table_options('Element', null, array(
		                        'record_types' => array('Item', 'All'),
		                        'sort' => 'orderBySet')
		                    )
		                );
		                echo $this->formSelect(
		                    "advanced[$i][type]",
		                    @$rows['type'],
		                    array(
		                        'title' => __("Search Type"),
		                        'id' => 'id-advanced-search-type',
		                        'class' => 'advanced-search-type'
		                    ),
		                    label_table_options(array(
		                        'contains' => __('contains'),
		                        'does not contain' => __('does not contain'),
		                        'is exactly' => __('is exactly'),
		                        'is empty' => __('is empty'),
		                        'is not empty' => __('is not empty'),
		                        'starts with' => __('starts with'),
		                        'ends with' => __('ends with'))
		                    )
		                );
		                echo $this->formText(
		                    "advanced[$i][terms]",
		                    @$rows['terms'],
		                    array(
		                        'size' => '20',
		                        'title' => __("Search Terms"),
		                        'id' => 'id-advanced-search-terms',
		                        'class' => 'advanced-search-terms'
		                    )
		                );
		                ?>
		                <button type="button" class="remove_search" disabled="disabled" style="display: none;"><?php echo __('Remove field'); ?></button>
		            </div>
		        <?php endforeach; ?>
		        </div>
		        <button type="button" class="add_search"><?php echo __('Add a Field'); ?></button>
		    </div>
		
		    <?php if (is_allowed('Users', 'browse')): ?>
		    <div class="field">
		    <?php
		        echo $this->formLabel('user-search', __('Search By User'));?>
		        <div class="inputs">
		        <?php
		            echo $this->formSelect(
		                'user',
		                @$_REQUEST['user'],
		                array('id' => 'user-search'),
		                get_table_options('User')
		            );
		        ?>
		        </div>
		    </div>
		    <?php endif; ?>
		
		    <div class="field">
		        <?php echo $this->formLabel('tag-search', __('Search By Tags')); ?>
		        <div class="inputs">
		        <?php
		            echo $this->formText('tags', @$_REQUEST['tags'],
		                array('size' => '40', 'id' => 'tag-search')
		            );
		        ?>
		        </div>
		    </div>
		
		
		    <div class="field">
		        <?php echo $this->formLabel('featured', __('Featured/Non-Featured')); ?>
		        <div class="inputs">
		        <?php
		            echo $this->formSelect(
		                'featured',
		                @$_REQUEST['featured'],
		                array(),
		                label_table_options(array(
		                    '1' => __('Only Featured Items'),
		                    '0' => __('Only Non-Featured Items')
		                ))
		            );
		        ?>
		        </div>
		    </div>
		
		    <div>
		        <?php if (!isset($buttonText)) {
		            $buttonText = __('Search for items');
		        } ?>
		        <input type="submit" class="submit button button-primary" name="submit_search" id="submit_search_advanced" value="<?php echo $buttonText ?>">
		    </div>
		</form>
		
		<?php echo js_tag('items-search'); ?>
		<script>
		    jQuery(document).ready(function () {
		        Omeka.Search.activateSearchButtons();
		    });
		</script>

        
		<!-- End search form  -->
	</section>	
	</div><!-- end primary -->

	<?php echo mh_share_this();?>
</article>
</div> <!-- end content -->



<?php echo foot(); ?>