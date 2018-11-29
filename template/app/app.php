<?php
    global $eblueinfo_plugin_slug, $country_service_url;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);
    $languages= array();

	$response = @file_get_contents($country_service_url);
	if ($response){
	    $countries = json_decode($response);
	    // echo "<pre>"; print_r($countries); echo "</pre>"; die();
	}

    if ( defined( 'POLYLANG_VERSION' ) ) {
        $pll_languages = pll_the_languages( array( 'raw' => 1 ) );

        foreach ($pll_languages as $slug => $language) {
            $languages[$slug] = $language['name'];
        }
    }

    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
?>
<?php require_once('header.php'); ?>

    <div class="container">
    	<div class="row">
    		<h2 class="app-title"><?php _e('e-BlueInfo', 'e-blueinfo'); ?></h2>
            <hr />
            <p class="h4">Please choose a language</p>
            <p class="h4">Por favor, escolha um idioma</p>
            <p class="h4">Por favor, elija un idioma</p>
            <div class="col-md-12 mobile">
                <?php if ( $languages ) : ?>
                <select class="languages">
                    <option></option>
                    <?php foreach ($languages as $slug => $name) : ?>
                    <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <hr />
                <?php endif; ?>
                <?php $countries_en = normalize_country_object($countries, 'en'); ?>
                <select class="countries en" onchange="location=this.value;">
                    <option>Please choose a country</option>
                    <?php foreach ($countries_en as $id => $name) : ?>
                    <option value="<?php echo get_site_url() . '/en/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php $countries_pt = normalize_country_object($countries, 'en'); ?>
                <select class="countries pt" onchange="location=this.value;">
                    <option>Por favor, escolha um país</option>
                    <?php foreach ($countries_pt as $id => $name) : ?>
                    <option value="<?php echo get_site_url() . '/pt/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php $countries_es = normalize_country_object($countries, 'es'); ?>
                <select class="countries es" onchange="location=this.value;">
                    <option>Por favor, elija un país</option>
                    <?php foreach ($countries_es as $id => $name) : ?>
                    <option value="<?php echo get_site_url() . '/es/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
    	</div>
    </div>

    <script src="<?php echo EBLUEINFO_PLUGIN_URL . 'app/js/en/main.menu.js'; ?>"></script>

<?php require_once('footer.php'); ?>
