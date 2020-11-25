<footer id="footer" class="container">
	<div class="divider"></div>
	<div class="row white padding2">
		<?php if ( is_active_sidebar( 'e-blueinfo-footer-sidebar-'.$country ) ) : ?>
	    <div id="footer-sidebar" class="col s12">
	        <?php dynamic_sidebar( 'e-blueinfo-footer-sidebar-'.$country ); ?>
	    </div>
	    <?php endif; ?>
	    <div class="col s6 offset-s3 center-align" id="logoFooter">
			<img src="http://logos.bireme.org/img/<?php echo $lang; ?>/v_bir_color.svg" class="responsive-img" alt="">
		</div>
	</div>
</footer>