<?php 
$story_container='div#element-'.cah_get_element_id('Item Type Metadata','Story');
?>
<style>
	.fa{
		font-family:"FontAwesome"
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
		content:"\f071";
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
		background: #bbb;
		color: #fff;
		padding: .25em .5em .25em .25em;
		float: right;	
		display: inline-block;	
	}	
	<?php echo $story_container;?> textarea{
		height:25em;
	}
	p.cah-helper{
		font-style: italic;
		padding:1em;
		background:#fafafa;
		margin-bottom: 1.5em;
		margin-top: 0;
		border-style: solid;
		border-color: #bbb;
		border-width: 7px 1px 1px;
	}
	.element-set-description{
		display: none !important;
	}
	#dc-reveal {
	    clear: both;
	    cursor: pointer;
	    text-align: right;
	    color: #FFF;
	    background: #BBB none repeat scroll 0% 0%;
	    display: inline-block;
	    float: right;
	    padding: 0.25em 0.5em;
	    font-style: italic;
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