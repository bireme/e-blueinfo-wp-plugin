<?php
    global $eblueinfo_plugin_slug, $country_service_url;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);

    if ( $_COOKIE['e-blueinfo-lang'] ) {
        $lang = $_COOKIE['e-blueinfo-lang'];
    }

    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();

	$response = @file_get_contents($country_service_url);
	if ($response){
	    $countries = json_decode($response);
        $countries = normalize_country_object($countries, $lang);
        // echo "<pre>"; print_r($countries); echo "</pre>"; die();
	}

    $country_id = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : false;
    // $country_name = array_map( function ($arr) use ($country_id) { if ( $arr->id == $country_id ) return $arr->name; }, $countries );
?>
<?php require_once('header.php'); ?>

    <div class="container">
    	<div class="row">
    		<h2 class="app-title"><?php _e('e-BlueInfo', 'e-blueinfo'); ?></h2>
            <hr />
            <p class="h4"><?php _e('Please choose a country', 'e-blueinfo'); ?></p>
            <div class="col-md-12 mobile">
                <select class="countries-list" onchange="location=this.value;">
                    <?php foreach ($countries as $id => $name) : $selected = ( $country_id == $id ) ? 'selected' : ''; ?>
                    <option data-country="<?php echo $id; ?>" value="<?php echo get_site_url() . '/' . $lang . '/' . $eblueinfo_plugin_slug . '?country=' . $id; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
    	</div>
    </div>

<?php require_once('footer.php'); ?>
