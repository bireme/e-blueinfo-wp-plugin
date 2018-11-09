<?php
    global $memoria_azul_plugin_slug, $country_service_url;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);
    $languages= array();

	$response = @file_get_contents($country_service_url);
	if ($response){
	    $countries = json_decode($response);
	    // echo "<pre>"; print_r($countries); echo "</pre>";
	}

    if ( defined( 'POLYLANG_VERSION' ) ) {
        $pll_languages = pll_the_languages( array( 'raw' => 1 ) );

        foreach ($pll_languages as $slug => $language) {
            $languages[$slug] = $language['name'];
        }
    }

    $home_url = isset($memoria_azul_config['home_url_' . $lang]) ? $memoria_azul_config['home_url_' . $lang] : real_site_url();
?>
<?php require_once('header.php'); ?>

    <div class="container">
    	<div class="row">
    		<h2 class="app-title"><?php _e('Memória Azul', 'memoria-azul'); ?></h2>
            <hr />
            <p class="h4">Please choose a language</p>
            <p class="h4">Por favor, escolha um idioma</p>
            <p class="h4">Por favor, elija un idioma</p>
    		<div class="col-md-12 desktop">
                <?php if ( $languages ) : ?>
                <div class="input-group adv-search">
                    <div class="input-group-btn">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default app-text app-lang">&nbsp;</button>
                            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-language" role="menu">
                                <?php foreach ($languages as $slug => $name) : ?>
                                <li data-lang="<?php echo $slug; ?>"><a><?php echo $name; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr />
                <?php endif; ?>
                <div class="input-group adv-search adv-lang en">
                    <div class="input-group-btn">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default app-text app-country">Please choose a country</button>
                            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-country" role="menu">
                                <?php foreach ($countries as $country) : ?>
                                <li><a href="<?php echo get_site_url() . '/en/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'en'); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="input-group adv-search adv-lang pt">
                    <div class="input-group-btn">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default app-text app-country">Por favor, escolha um país</button>
                            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-country" role="menu">
                                <?php foreach ($countries as $country) : ?>
                                <li><a href="<?php echo get_site_url() . '/pt/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'pt'); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="input-group adv-search adv-lang es">
                    <div class="input-group-btn">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default app-text app-country">Por favor, elija un país</button>
                            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-country" role="menu">
                                <?php foreach ($countries as $country) : ?>
                                <li><a href="<?php echo get_site_url() . '/es/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'es'); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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
                <select class="countries en" onchange="location=this.value;">
                    <option>Please choose a country</option>
                    <?php foreach ($countries as $country) : ?>
                    <option value="<?php echo get_site_url() . '/en/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'en'); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="countries pt" onchange="location=this.value;">
                    <option>Por favor, escolha um país</option>
                    <?php foreach ($countries as $country) : ?>
                    <option value="<?php echo get_site_url() . '/pt/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'pt'); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="countries es" onchange="location=this.value;">
                    <option>Por favor, elija un país</option>
                    <?php foreach ($countries as $country) : ?>
                    <option value="<?php echo get_site_url() . '/es/' . $memoria_azul_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, 'es'); ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
    	</div>
    </div>

    <script src="<?php echo MEMORIA_AZUL_PLUGIN_URL . 'app/js/en/main.menu.js'; ?>"></script>

<?php require_once('footer.php'); ?>
