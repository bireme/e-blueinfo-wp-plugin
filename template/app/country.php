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
	    // echo "<pre>"; print_r($countries); echo "</pre>";
	}

    $country_id = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : false;
    $country_name = array_map( function ($arr) use ($country_id) { if ( $arr->id == $country_id ) return $arr->name; }, $countries );
?>
<?php require_once('header.php'); ?>

    <div class="container">
    	<div class="row">
    		<h2 class="app-title"><?php _e('e-BlueInfo', 'e-blueinfo'); ?></h2>
            <hr />
            <p class="h4"><?php _e('Please choose a country', 'e-blueinfo'); ?></p>
    		<div class="col-md-12 desktop">
                <div class="input-group adv-search">
                    <div class="input-group-btn">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default app-text app-country"><?php echo get_country_name($country_name[0], $lang); ?></button>
                            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-country" role="menu">
                                <?php foreach ($countries as $country) : ?>
                                <li><a href="<?php echo get_site_url() . '/' . $lang . '/' . $eblueinfo_plugin_slug . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, $lang); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mobile">
                <select class="countries-list" onchange="location=this.value;">
                    <?php foreach ($countries as $country) : $selected = ( $country_id == $country->id ) ? 'selected' : ''; ?>
                    <option value="<?php echo get_site_url() . '/' . $lang . '/' . $eblueinfo_plugin_slug . '?country=' . $country->id; ?>" <?php echo $selected; ?>><?php echo get_country_name($country->name, $lang); ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
    	</div>
    </div>

<?php require_once('footer.php'); ?>
