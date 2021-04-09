<?php
    global $services_platform_url;
    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
    $site = 'https://e-blueinfo.bvsalud.org';
    
    $cc = $country_code[$_COOKIE['e-blueinfo-country']];
    $c_name = array(
        "BR" => __('Brazil', 'e-blueinfo'),
        "SV" => __('El Salvador', 'e-blueinfo'),
        "GT" => __('Guatemala', 'e-blueinfo'),
        "PE" => __('Peru', 'e-blueinfo'),
        "MO" => __('Macao', 'e-blueinfo')
    );
    $c_pages = array(
        "BR" => array(
            "pt" => $site . '/dados-do-brasil/',
            "es" => $site . '/es/datos-de-brasil/',
            "en" => $site . '/en/data-from-brazil/'
        ),
        "SV" => array(
            "pt" => $site . '/dados-de-el-salvador/',
            "es" => $site . '/es/datos-de-el-salvador/',
            "en" => $site . '/en/data-from-el-salvador/'
        ),
        "GT" => array(
            "pt" => $site . '/dados-da-guatemala/',
            "es" => $site . '/es/datos-de-guatemala/',
            "en" => $site . '/en/data-from-guatemala/'
        ),
        "PE" => array(
            "pt" => $site . '/dados-do-peru/',
            "es" => $site . '/es/datos-de-peru/',
            "en" => $site . '/en/data-from-peru/'
        )
    );

    $about_pages = array(
        "about" => array(
            "pt" => $site . '/por-que-e-blueinfo/',
            "es" => $site . '/es/por-que-e-blueinfo-es/',
            "en" => $site . '/en/why-e-blueinfo/'
        ),
        "supporters" => array(
            "pt" => $site . '/apoiadores-institucionais/',
            "es" => $site . '/es/apoyadores-institucionales/',
            "en" => $site . '/en/institutional-supporters/'
        )
    );

    $help_pages = array(
        "tutorial" => array(
            "pt" => $site . '/tutoriais/',
            "es" => $site . '/es/tutoriales/',
            "en" => $site . '/en/tutorials/'
        )
    );
?>

<div class="col s2 m1" id="menu">
    <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons" style="font-size: 30px">menu</i></a>
    <ul class="sidenav sidenav-menu" id="mobile-demo">
        <div id="brand">
            <a href="community.php?"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/logoPB.png'; ?>" alt="" class="responsive-img"></a>
        </div>
        <?php if ( 'oc' != $country ) : ?>
            <?php if ( $_COOKIE['userData'] ) : ?>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'logout/'; ?>"><?php _e('Logout', 'e-blueinfo'); ?></a></li>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'favorites/?community=' . $community_id . '&collection=' . $collection_id; ?>"><?php _e('Favorites', 'e-blueinfo'); ?></a></li>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'visited/?community=' . $community_id . '&collection=' . $collection_id; ?>"><?php _e('Visited', 'e-blueinfo'); ?></a></li>
            <?php else : ?>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'auth/'; ?>"><?php _e('Login', 'e-blueinfo'); ?></a></li>
            <?php endif; ?>
            
            <?php if ( 'US' == $cc ) : ?>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'country/'; ?>"><?php _e('Change country', 'e-blueinfo'); ?></a></li>
            <?php else : ?>
            <!-- Start nested content -->
            <li>
                <ul class="collapsible collapsible-accordion">
                    <li>
                        <a class="collapsible-header" tabindex="0"><?php _e('Country', 'e-blueinfo'); ?> <?php echo '('.$c_name[$cc].')'; ?></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="<?php echo $c_pages[$cc][$lang]; ?>" target="_blank"><?php _e('See more', 'e-blueinfo'); ?></a></li>
                                <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'country/'; ?>"><?php _e('Change country', 'e-blueinfo'); ?></a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
        <?php else : ?>
            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'country/'; ?>"><?php _e('Country', 'e-blueinfo'); ?></a></li>
        <?php endif; ?>
        <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>"><?php _e('Contents', 'e-blueinfo'); ?></a></li>
        <!-- Start nested content -->
        <li>
            <ul class="collapsible collapsible-accordion">
                <li>
                    <a class="collapsible-header" tabindex="0"><?php _e('About', 'e-blueinfo'); ?></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="<?php echo $about_pages['about'][$lang]; ?>"><?php _e('Why e-BlueInfo?', 'e-blueinfo'); ?></a></li>
                            <li><a href="<?php echo $about_pages['supporters'][$lang]; ?>"><?php _e('Supporters', 'e-blueinfo'); ?></a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
        <!-- Start nested content -->
        <li>
            <ul class="collapsible collapsible-accordion">
                <li>
                    <a class="collapsible-header" tabindex="0"><?php _e('Help', 'e-blueinfo'); ?></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'contact/'; ?>"><?php _e('Contact', 'e-blueinfo'); ?></a></li>
                            <!-- <li><a href="<?php echo $help_pages['tutorial'][$lang]; ?>"><?php _e('Tutorial', 'e-blueinfo'); ?></a></li> -->
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
        <!-- Start nested content -->
        <li>
            <ul class="collapsible collapsible-accordion">
                <li>
                    <a class="collapsible-header" tabindex="0"><?php _e('Language', 'e-blueinfo'); ?></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="<?php echo pll_home_url('pt').'?fcl=true'; ?>">Português</a></li>
                            <li><a href="<?php echo pll_home_url('es').'?fcl=true'; ?>">Español</a></li>
                            <li><a href="<?php echo pll_home_url('en').'?fcl=true'; ?>">English</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
        <!-- <li><a href="#" data-target="slide-out" class="sidenav-trigger"><?php _e('Settings', 'e-blueinfo'); ?></a></li> -->
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