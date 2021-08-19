<?php
function eblueinfo_page_admin() {
    global $country_service_url;
    $config = get_option('eblueinfo_config');

    $site_language = strtolower(get_bloginfo('language'));
    $lang = substr($site_language,0,2);

    $ctest = array(
        'pt' => 'Macau',
        'es' => 'Macao',
        'en' => 'Macao'
    );

    $response = @file_get_contents($country_service_url);
    if ($response){
        $response_json = json_decode($response);
        $cc_list = wp_list_pluck( $response_json, 'code', 'id' );
        $countries = normalize_country_object($response_json, $lang);

        if ( !EBLUEINFO_CTEST ) {
            $countries = array_diff($countries, $ctest);
        }
    }
?>
    <div class="eblueinfo-lower">
        <div class="eblueinfo-card">
            <div class="eblueinfo-section-header">
                <div class="eblueinfo-section-header__label">
                    <h2><?php _e('e-BlueInfo settings', 'e-blueinfo'); ?></h2>
                </div>
            </div>
            <div class="inside">
                <form method="post" action="options.php">

                    <?php settings_fields('e-blueinfo-settings-group'); ?>

                    <table cellspacing="0" class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row"><?php _e('Plugin page', 'e-blueinfo'); ?>:</th>
                                <td><input type="text" name="eblueinfo_config[plugin_slug]" value="<?php echo (!empty($config['plugin_slug']) ? $config['plugin_slug'] : 'e-blueinfo'); ?>" class="regular-text code"></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Filter query', 'e-blueinfo'); ?>:</th>
                                <td><input type="text" name="eblueinfo_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('AddThis profile ID', 'e-blueinfo'); ?>:</th>
                                <td><input type="text" name="eblueinfo_config[addthis_profile_id]" value="<?php echo $config['addthis_profile_id'] ?>" class="regular-text code"></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Google Analytics code', 'e-blueinfo'); ?>:</th>
                                <td><input type="text" name="eblueinfo_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Fulltext', 'e-blueinfo'); ?>:</th>
                                <td>
                                    <label for="present_alternative_links">
                                        <input type="checkbox" name="eblueinfo_config[alternative_links]" value="true" id="present_alternative_links" <?php echo (isset($config['alternative_links']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Present alternative fulltext links', 'e-blueinfo'); ?>
                                    </label>
                                </td>
                            </tr>

                            <?php
                                if ( function_exists( 'pll_the_languages' ) ) {
                                    $available_languages = pll_languages_list();
                                    $available_languages_name = pll_languages_list(array('fields' => 'name'));
                                    $count = 0;
                                    foreach ($available_languages as $lang) {
                                        $key_name = 'plugin_title_' . $lang;
                                        $home_url = 'home_url_' . $lang;

                                        echo '<tr valign="top">';
                                        echo '    <th scope="row"> ' . __("Home URL", "e-blueinfo") . ' (' . $available_languages_name[$count] . '):</th>';
                                        echo '    <td><input type="text" name="eblueinfo_config[' . $home_url . ']" value="' . $config[$home_url] . '" class="regular-text code"></td>';
                                        echo '</tr>';

                                        echo '<tr valign="top">';
                                        echo '    <th scope="row"> ' . __("Page title", "e-blueinfo") . ' (' . $available_languages_name[$count] . '):</th>';
                                        echo '    <td><input type="text" name="eblueinfo_config[' . $key_name . ']" value="' . $config[$key_name] . '" class="regular-text code"></td>';
                                        echo '</tr>';
                                        $count++;
                                    }
                                }else{
                                    echo '<tr valign="top">';
                                    echo '   <th scope="row">' . __("Page title", "e-blueinfo") . ':</th>';
                                    echo '   <td><input type="text" name="eblueinfo_config[plugin_title]" value="' . $config["plugin_title"] . '" class="regular-text code"></td>';
                                    echo '</tr>';
                                }
                            ?>

                            <tr valign="top">
                                <th scope="row"><?php _e('Redirect', 'e-blueinfo'); ?>:</th>
                                <td><input type="text" name="eblueinfo_config[redirect]" value="<?php echo (!empty($config['redirect']) ? $config['redirect'] : 'https://e-blueinfo.bvsalud.org/'); ?>" class="regular-text code"></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <?php _e('Display filters', 'e-blueinfo'); ?>:
                                </th>
                                <td>
                                    <fieldset>
                                        <label for="available_filter_descriptor">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="descriptor" id="available_filter_descriptor" <?php echo (!isset($config['available_filter']) || in_array('descriptor', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Subject', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        <label for="available_filter_act_type">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="act_type" id="available_filter_act_type" <?php echo (!isset($config['available_filter']) || in_array('act_type', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Act type', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        <label for="available_filter_scope_region">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="scope_region" id="available_scope_region" <?php echo (!isset($config['available_filter']) || in_array('scope_region', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Country/region', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        <!--
                                        <label for="available_filter_database">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="database" id="available_filter_database" <?php echo (!isset($config['available_filter']) || in_array('database', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Database', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        -->
                                        <label for="available_filter_collection">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="collection" id="available_filter_collection" <?php echo (!isset($config['available_filter']) ||  in_array('collection', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Collection', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        <label for="available_filter_language">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="language" id="available_filter_language" <?php echo (!isset($config['available_filter']) ||  in_array('language', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Language', 'e-blueinfo'); ?>
                                        </label>
                                        <br/>
                                        <label for="available_filter_year">
                                            <input type="checkbox" name="eblueinfo_config[available_filter][]" value="year" id="available_filter_year" <?php echo (!isset($config['available_filter']) ||  in_array('year', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php _e('Year', 'e-blueinfo'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <?php _e('Display countries', 'e-blueinfo'); ?>:
                                </th>
                                <td>
                                    <fieldset>
                                        <?php foreach ($countries as $id => $name) : ?>
                                        <label for="available_country_<?php echo strtolower($cc_list[$id]); ?>">
                                            <input type="checkbox" name="eblueinfo_config[available_country][]" value="<?php echo $cc_list[$id]; ?>" id="available_country_<?php echo strtolower($cc_list[$id]); ?>" <?php echo (!isset($config['available_country']) || in_array($cc_list[$id], $config['available_country']) ?  " checked='true'" : '') ;?> ></input>
                                            <?php echo $name; ?>
                                        </label>
                                        <br/>
                                        <?php endforeach; ?>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="eblueinfo-card-actions">
                        <div id="publishing-action">
                            <input type="submit" class="eblueinfo-button" value="<?php _e('Save changes'); ?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>
