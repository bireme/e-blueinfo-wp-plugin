		<div class="col s2 m1">
			<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons" style="font-size: 30px">menu</i></a>
			<ul class="sidenav sidenav-menu" id="mobile-demo">
				<div id="brand">
					<a href="community.php?"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/logoPB.png'; ?>" alt="" class="responsive-img"></a>
				</div>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'country/'; ?>">Country</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>">Community</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'favorites/'; ?>">My Favorites</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'downloads/'; ?>">My Downloads</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>">About</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>">Help</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'login/'; ?>">Login</a></li>
				<li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'tutorial/'; ?>">Tutorial</a></li>
				<li><a href="#" data-target="slide-out" class="sidenav-trigger">Settings</a></li>
				<!-- Start nested content -->
				<li>
				    <ul class="collapsible collapsible-accordion">
				        <li>
				            <a class="collapsible-header" tabindex="0">Language</a>
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
					<h5>Setting Colors</h5>
				</div>
				<div id="color0" class="col s10 offset-s1 white blue-text text-lighten-1">
					<h5>Colors 1</h5>
				</div>
				<div id="color1" class="col s10 offset-s1 blue darken-1 accent-4">
					<h5>Colors 2</h5>
				</div>
				<div id="color2" class="col s10 offset-s1 blue lighten-1">
					<h5>Colors 3</h5>
				</div>
			</li>
		</ul>