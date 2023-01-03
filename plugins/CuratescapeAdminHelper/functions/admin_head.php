<?php 
$story_container='div#element-'.cah_get_element_id('Item Type Metadata','Story');
$element_info = cah_item_form_helper_text_array(false);
$add_input_supported = $element_info['add_input_supported'];
$use_html_supported = $element_info['use_html_supported'];
?>
<style>
	<?php echo $story_container;?> textarea{
		height:25em;
	}
	<?php if (get_option('cah_hide_html_checkbox_where_unsupported')=='1'): ?>
		/* selective "use html" */	
		label.use-html{
			display: none !important;
		}
		<?php 
			$selectors=[];
			foreach($use_html_supported as $id){
				$selectors[] = 'label[id^=Elements-'.$id.'].use-html';
			}
			echo implode(',', $selectors).'{
				display:block !important;
			}';
		?>
	<?php endif;?>
	<?php if (get_option('cah_hide_add_input_where_unsupported')=='1'): ?>
		/* selective "add input" */	
		button.add-element{
			display: none !important;
		}
		<?php 
			$selectors=[];
			foreach($add_input_supported as $id){
				$selectors[] = 'button#add_element_'.$id.'.add-element';
			}
			echo implode(',', $selectors).'{
				display:block !important;
			}';
		?>
	<?php endif;?>
	span.cah-warning{
		display: block;
		background: lightyellow;
		margin:0;
		padding: 3px 7px;
		line-height:normal;
	}
	p.cah-helper span.divider{
		display: block;
		margin: 15px 0;
		border-top: 1px dashed #ccc;
	}
	.fa{
		font-family:"Font Awesome 5 Free"
	}
	.fa-exclamation-triangle:after,	 
	.fa-check-circle:after{
		font-style: normal;
		font-weight: lighter;
		font-size:1.35em;
		line-height:0em;
		vertical-align: middle;
		padding-left: .25em;
		text-shadow:0 0 2px #fff;		
	}
	.fa-exclamation-triangle:after{
		content:"\f256";
		color:#AD6345;
	}	
	.fa-check-circle:after{
		content: "\f058";
		color: #A4C637;
	}
	.fa-question-circle:after{
		content:"\f059";
		color:#fff;
		padding: .25em;
		font-style: normal;
	}	
	.tab-info{
		background: #777;
		color: #fff;
		padding: .25em .5em .25em .25em;
		float: right;	
		display: inline-block;	
		border-radius: 0 0 0 7px;
	}	
	p.cah-helper{
		padding:1em;
		background:lightyellow;
		margin-bottom: 1.5em;
		margin-top: 0;
		border-style: solid;
		border-color: #777;
		color: #222;
		border-width: 7px 1px 1px;
		border-radius: 3px;
	}
	.element-set-description{
		display: none !important;
	}
	#dc-reveal {
	    clear: both;
	    cursor: pointer;
	    text-align: center;
	    color: #FFF;
	    background: #777;
	    display: block;
	    padding: 0.75em 0.5em;
	    font-style: italic;
	    border-radius:7px;
	    margin-bottom: 1em;
	}
	a:link.cah-file-edit{
		text-align: center;
		display: block;
		padding-bottom: 3px;
	}
	.ui-widget-content p.explanation a{
		text-decoration: underline;
	}
	.theme-file img{
		background: #eaeaea;
	}
</style>