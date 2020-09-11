<footer id="footer" class="container">
	<div class="divider"></div>
	<div class="row white padding2">
		<br />
		<div class="col s12" id="logoFooter">
			<img src="http://logos.bireme.org/img/<?php echo $lang; ?>/v_bir_color.svg" class="responsive-img" alt="">
		</div>
		<div class="col s2">
			<br />
			<img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/flag-' . slugify($countries[$country]) . '.jpg'; ?>" class="responsive-img" alt="" id="footerFlag">
		</div>
		<?php if ( is_active_sidebar( 'e-blueinfo-footer-sidebar-'.$country ) ) : ?>
	    <div id="footer-sidebar" class="col s10 right-align">
	        <?php dynamic_sidebar( 'e-blueinfo-footer-sidebar-'.$country ); ?>
	    </div>
	    <?php endif; ?>
	</div>
</footer>