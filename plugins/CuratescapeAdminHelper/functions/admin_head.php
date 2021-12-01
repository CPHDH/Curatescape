<?php 
$story_container='div#element-'.cah_get_element_id('Item Type Metadata','Story');
?>
<style>
	<?php echo $story_container;?> textarea{
		height:25em;
	}
	<?php if (get_option('cah_hide_html_checkbox_where_unsupported')=='1'): ?>
		/* prevent "add input" from adding back */	
		#element-49 label.use-html,
		#element-39 label.use-html{
			display:none !important;
		}
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
</style>