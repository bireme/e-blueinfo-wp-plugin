<?php
    global $eblueinfo_plugin_slug;

	$site_language = strtolower(get_bloginfo('language'));
	$lang = substr($site_language,0,2);
    $languages = array();

    if ( $_COOKIE['e-blueinfo-lang'] ) {
        $lang = $_COOKIE['e-blueinfo-lang'];
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
            <p class="h4"><?php _e('Please choose a language', 'e-blueinfo'); ?></p>
            <div class="col-md-12 mobile">
                <select class="languages-list" onchange="location=this.value;">
                    <option></option>
                    <?php foreach ($languages as $slug => $name) : $selected = ( $lang == $slug ) ? 'selected' : ''; ?>
                    <option data-lang="<?php echo $slug; ?>" value="<?php echo get_site_url() . '/' . $slug . '/' . $eblueinfo_plugin_slug; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
    	</div>
    </div>

<?php require_once('footer.php'); ?>
