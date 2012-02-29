<?php
if (mobile_device_detect()==true){
	echo common('m-footer-nav');
	}
else{	
?>

<div id="footer">
<p>Powered by <a href="http://omeka.org/">Omeka</a> + MobileHistorical
<br/>
&copy; <?php echo date('Y').' '.settings('author');?> 
</p>
</div>

</div><!-- end content -->

</div><!--end wrap-->

</body>

</html>
<?php }?>