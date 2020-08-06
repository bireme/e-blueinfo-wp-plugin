<?php
    global $eblueinfo_plugin_slug, $country_service_url;

    $site_language = strtolower(get_bloginfo('language'));
    $lang = substr($site_language,0,2);

    if ( $_COOKIE['e-blueinfo-lang'] ) {
        $lang = $_COOKIE['e-blueinfo-lang'];
    }

    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();

    $ctest = array(
        'pt' => 'Macau',
        'es' => 'Macao',
        'en' => 'Macao'
    );

    $response = @file_get_contents($country_service_url);
    if ($response){
        $countries = json_decode($response);
        $countries = normalize_country_object($countries, $lang);

        if ( !EBLUEINFO_CTEST ) {
            $countries = array_diff($countries, $ctest);
        }
    }

    $country_id = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : false;
    // $country_name = array_map( function ($arr) use ($country_id) { if ( $arr->id == $country_id ) return $arr->name; }, $countries );
?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<!-- ./Header -->

<!-- Template -->
<section id="containerCenter">
    <form action="">
        <div class="row">
            <div class="input-field col s10 offset-s1 m6 offset-m3 l4 offset-l4 countries-list">
                <h6 class="center-align"><b><?php _e('Please choose a country', 'e-blueinfo'); ?></b></h6>
                <select>
                    <option disabled selected></option>
                    <?php foreach ($countries as $id => $name) : $selected = ( $country_id == $id ) ? 'selected' : ''; ?>
                    <option data-country="<?php echo $id; ?>" value="<?php echo get_site_url() . '/' . $lang . '/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col s10 offset-s1 m6 offset-m3 l4 offset-l4 center-align">
                <a id="btn-submit" class="btn waves-effect waves-light blue darken-4 bt100"><?php echo strtoupper(__('Select', 'e-blueinfo')); ?></a>
            </div>
        </div>
    </form>
</section>
<!-- ./Template -->

<!-- Footer -->
<?php get_footer(); ?>
<!-- ./Footer -->
