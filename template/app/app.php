<?php
    global $memoria_azul_plugin_slug, $country_service_url;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);

	$response = @file_get_contents($country_service_url);
	if ($response){
	    $countries = json_decode($response);
	    // echo "<pre>"; var_dump($countries); echo "</pre>";
	}

    $home_url = isset($memoria_azul_config['home_url_' . $lang]) ? $memoria_azul_config['home_url_' . $lang] : real_site_url();
?>
<?php require_once('header.php'); ?>

    <div class="container">
    	<div class="row">
    		<h2 class="app-title"><?php _e('Memória Azul', 'memoria-azul'); ?></h2>
            <hr />
    		<div class="col-md-12">
                <div class="input-group" id="adv-search">
                    <div class="input-group-btn">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default app-text"><?php _e('Please choose a country', 'memoria-azul'); ?></button>
                          <button type="button" class="btn btn-default" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                              <?php foreach ($countries as $country) : ?>
                              <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug) . '?country=' . $country->id; ?>"><?php echo get_country_name($country->name, $lang); ?></a></li>
                              <?php endforeach; ?>
                          </ul>
                        </div>
                    </div>
                </div>
              </div>
            </div>
    	</div>
    </div>

<?php require_once('footer.php'); ?>
