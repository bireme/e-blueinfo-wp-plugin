<?php
    global $eblueinfo_plugin_slug, $country_service_url;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);
    $languages = array();

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

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<!-- ./Header -->

<!-- Template -->
<?php if ( ! $response ) : ?>
    <section id="containerCenter">
        <div class="row">
            <div class="col s10 offset-s1 m6 offset-m3 l4 offset-l4">
                <p class="warning center-align">SYSTEM UNAVAILABLE<br />TRY TO ACCESS LATER</p>
                <p class="warning center-align">SISTEMA INDISPONÍVEL<br />TENTE ACESSAR MAIS TARDE</p>
                <p class="warning center-align">SISTEMA NO DISPONIBLE<br />INTENTE ACCEDER MÁS TARDE</p>
            </div>
        </div>
    </section>
<?php else : ?>
    <section id="containerCenter">
        <form action="">
            <div class="row">
                <?php if ( $languages ) : ?>
                <div class="input-field col s10 offset-s1 m6 offset-m3 l4 offset-l4">
                    <h6 class="center-align"><b>Please choose a language</b></h6>
                    <h6 class="center-align"><b>Por favor, escolha um idioma</b></h6>
                    <h6 class="center-align"><b>Por favor, elija un idioma</b></h6>
                    <select class="languages">
                        <option disabled selected></option>
                        <?php foreach ($languages as $slug => $name) : ?>
                        <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="input-field col s10 offset-s1 m6 offset-m3 l4 offset-l4 countries en">
                    <?php $countries_en = normalize_country_object($countries, 'en'); ?>
                    <h6 class="center-align"><b>Please choose a country</b></h6>
                    <select onchange="location=this.value;">
                        <option disabled selected></option>
                        <?php foreach ($countries_en as $id => $name) : ?>
                        <option data-country="<?php echo $id; ?>" value="<?php echo get_site_url() . '/en/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s10 offset-s1 m6 offset-m3 l4 offset-l4 countries pt">
                    <?php $countries_pt = normalize_country_object($countries, 'pt'); ?>
                    <h6 class="center-align"><b>Por favor, escolha um país</b></h6>
                    <select onchange="location=this.value;">
                        <option disabled selected></option>
                        <?php foreach ($countries_pt as $id => $name) : ?>
                        <option data-country="<?php echo $id; ?>" value="<?php echo get_site_url() . '/pt/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s10 offset-s1 m6 offset-m3 l4 offset-l4 countries es">
                    <?php $countries_es = normalize_country_object($countries, 'es'); ?>
                    <h6 class="center-align"><b>Por favor, elija un país</b></h6>
                    <select onchange="location=this.value;">
                        <option disabled selected></option>
                        <?php foreach ($countries_es as $id => $name) : ?>
                        <option data-country="<?php echo $id; ?>" value="<?php echo get_site_url() . '/es/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </section>
<?php endif; ?>
<!-- ./Template -->

<script src="<?php echo EBLUEINFO_PLUGIN_URL . 'app/js/en/main.menu.js'; ?>"></script>

<!-- Footer -->
<?php get_footer(); ?>
<!-- ./Footer -->
