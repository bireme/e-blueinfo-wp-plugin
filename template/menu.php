<?php
    global $services_platform_url;
    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
?>

<div class="col s2 m1">
	<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons" style="font-size: 30px">menu</i></a>
	<ul class="sidenav sidenav-menu" id="mobile-demo">
		<div id="brand">
			<a href="community.php?"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/logoPB.png'; ?>" alt="" class="responsive-img"></a>
		</div>
		<?php if ( $_COOKIE['userData'] ) : ?>
		<li><a href="<?php echo $services_platform_url.'/client/controller/logout/control/business/origin/'.base64_encode($home_url); ?>"><?php _e('Logout', 'e-blueinfo'); ?></a></li>
		<?php else : ?>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'auth/'; ?>"><?php _e('Login', 'e-blueinfo'); ?></a></li>
		<?php endif; ?>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'country/'; ?>"><?php _e('Country', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>"><?php _e('Communities', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'favorites/'; ?>"><?php _e('Favorites', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'downloads/'; ?>"><?php _e('Downloads', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>"><?php _e('About', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>"><?php _e('Help', 'e-blueinfo'); ?></a></li>
		<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'tutorial/'; ?>"><?php _e('Tutorial', 'e-blueinfo'); ?></a></li>
		<li><a href="#" data-target="slide-out" class="sidenav-trigger"><?php _e('Settings', 'e-blueinfo'); ?></a></li>
		<!-- Start nested content -->
		<li>
		    <ul class="collapsible collapsible-accordion">
		        <li>
		            <a class="collapsible-header" tabindex="0"><?php _e('Language', 'e-blueinfo'); ?></a>
		            <div class="collapsible-body">
		                <ul>
							<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'pt/'; ?>">Português</a></li>
							<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'es/'; ?>">Español</a></li>
							<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>">English</a></li>
		                </ul>
		            </div>
		        </li>
		    </ul>
		</li>
	</ul>
</div>

<ul id="slide-out" class="sidenav white-text">
	<li class="row">
		<div class="col s10 offset-s1">
			<h5><?php _e('Setting Colors', 'e-blueinfo'); ?></h5>
		</div>
		<div id="color0" class="col s10 offset-s1 white blue-text text-lighten-1">
			<h5><?php _e('Color', 'e-blueinfo'); ?> 1</h5>
		</div>
		<div id="color1" class="col s10 offset-s1 blue darken-1 accent-4">
			<h5><?php _e('Color', 'e-blueinfo'); ?> 2</h5>
		</div>
		<div id="color2" class="col s10 offset-s1 blue lighten-1">
			<h5><?php _e('Color', 'e-blueinfo'); ?> 3</h5>
		</div>
	</li>
</ul>