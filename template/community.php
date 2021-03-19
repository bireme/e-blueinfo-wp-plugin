<?php
/*
Template Name: e-BlueInfo Community Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $solr_service_url;

require_once(EBLUEINFO_PLUGIN_PATH . '/lib/Paginator.php');

$order = array(
        'RELEVANCE' => 'score desc',
        'YEAR_ASC'  => 'publication_year asc',
        'YEAR_DESC' => 'publication_year desc'
    );

$eblueinfo_config         = get_option('eblueinfo_config');
$eblueinfo_initial_filter = $eblueinfo_config['initial_filter'];
$eblueinfo_addthis_id     = $eblueinfo_config['addthis_profile_id'];

// set lang
$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

if ( $_COOKIE['e-blueinfo-lang'] ) {
    $lang = $_COOKIE['e-blueinfo-lang'];
}

// set query using default param q (query) or s (wordpress search) or newexpr (metaiah)
$query = $_GET['s'] . $_GET['q'];
$query = stripslashes( trim($query) );

// set country
$country = ( !empty($_GET['country']) ? $_GET['country'] : '' );
$country = ( !empty($_COOKIE['e-blueinfo-country']) ? $_COOKIE['e-blueinfo-country'] : $country );

$user_filter = stripslashes($_GET['filter']);
$community_id = '';
$collection_id = '';
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'created_date desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 10 );
$total  = 0;
$filter = '';

if ($eblueinfo_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $eblueinfo_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $eblueinfo_initial_filter;
    }
}else{
    $filter = $user_filter;
}

$start = ($page * $count) - $count;

if ( $query ) {
    $eblueinfo_service_request = $eblueinfo_service_url . 'api/bibliographic/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&count=' . $count . '&sort=' . urlencode($sort) . '&lang=' . $lang;
} else {
    $eblueinfo_service_request = $eblueinfo_service_url . 'api/community/?country=' . $country . '&format=' . $format . '&lang=' . $lang;
}

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $uf){
        preg_match('/([a-z_]+):(.+)/',$uf, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

// print $eblueinfo_service_request;

$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>";
    $total = $response_json->meta->total_count;
    $start = $response_json->meta->offset;
    $next  = $response_json->meta->next;
    $community_list = $response_json->objects;
    $community_ids = wp_list_pluck($community_list, 'id');
    $community_id = implode(',', $community_ids);
}

$community_cluster_request = $solr_service_url . 'query?q=*:*&facet=true&facet.field=com&rows=0';
$response = @file_get_contents($community_cluster_request);
if ($response){
    $response_json = json_decode($response);
    $facet_fields = $response_json->facet_counts->facet_fields->com;
    $community_cluster = get_cluster($facet_fields);
}

$home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();

?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<section class="container">
    <div class="row">
        <?php require_once('menu.php'); ?>
        <div class="col s10 m11" id="barSearch">
            <nav>
                <div class="nav-wrapper">
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Community','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
                        <div class="input-field">
                            <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                            <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                            <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">

                            <a href="#!" id="speakBtn"><i class="material-icons">settings_voice</i></a>
                            <input type="search" name="q" value="" id="searchBarInput">
                            <label class="label-icon" for="searchBarInput"><i class="material-icons">search</i></label>
                            <i class="material-icons">close</i>
                        </div>
                    </form>
                </div>
            </nav>
        </div>
    </div>
</section>
<!-- ./Header -->

<!-- Template -->
<?php if ( 'oc' == $country ) : ?>
<section class="container containerAos">
    <div class="row">
        <article class="col s12" data-aos="fade-up" data-aos-delay="300">
            <div class="card-panel blue darken-1 center-align">
                <span class="white-text"><?php _e('There are no communities available as your country has not joined e-BlueInfo. But you still have access to the documents in the Other Evidence section.','e-blueinfo'); ?></span>
            </div>
        </article>
    </div>
</section>
<?php elseif ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No results found. Please choose a country in the main menu.','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row">
        <?php foreach ( $community_list as $index => $community ) : $index++; ?>
        <article class="col s12 m6 l4" data-aos="fade-up" data-aos-delay="300">
            <div class="card">
                <div class="card-image">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>collection/?community=<?php echo $community->id; ?>" onclick="__gaTracker('send','event','Community','View','<?php echo $countries[$country].'|'.$community->name; ?>');">
                        <img src="<?php echo $community->image; ?>" onerror="this.src='<?php echo EBLUEINFO_PLUGIN_URL . "template/images/default.jpg"; ?>'">
                    </a>
                    <a href="#modal-community-<?php echo $community->id; ?>" class="btn-floating halfway-fab waves-effect waves-light red modal-trigger"><i class="fas fa-info"></i></a>
                </div>
                <div class="card-content">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>collection/?community=<?php echo $community->id; ?>" onclick="__gaTracker('send','event','Community','View','<?php echo $countries[$country].'|'.$community->name; ?>');">
                        <h5><b><?php echo $community->name; ?></b></h5>
                        <div><?php _e('Click here for selected content','e-blueinfo'); ?><div>
                        <hr />
                        <small><?php _e('Documents','e-blueinfo'); ?>: <?php echo $community_cluster['_'.$community->id]['total']; ?></small>
                        <?php if ( is_timestamp($community->updated_time) ) : ?>
                            <small><?php _e('Last Update','e-blueinfo'); ?>: <?php echo $community->updated_time; ?></small>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="container containerAos">
    <div class="row">
        <div class="col s12">
            <h5  style="padding: 10px 20px; color: #fff; background-color: #0d47a1;"><b><?php _e('Other Contents', 'e-blueinfo'); ?></b></h5>
        </div>
        <?php $cc = $country_code[$_COOKIE['e-blueinfo-country']]; ?>
        <?php if ( 'US' != $cc ) : ?>
        <article class="col s12 m6 l4" data-aos="fade-up" data-aos-delay="500">
            <div class="card">
                <div class="card-image">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>collection/?community=60">
                        <img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/logo_opas.png'; ?>">
                    </a>
                    <a href="#modal-guidelines" class="btn-floating halfway-fab waves-effect waves-light red modal-trigger"><i class="fas fa-info"></i></a>
                </div>
                <div class="card-content">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>collection/?community=60">
                        <h5><b><?php _e('PAHO/WHO Guidelines', 'e-blueinfo'); ?></b></h5>
                    </a>
                </div>
            </div>
        </article>
        <?php endif; ?>
        <article class="col s12 m6 l4" data-aos="fade-up" data-aos-delay="500">
            <div class="card">
                <div class="card-image">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>infobutton">
                        <img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/infobutton.jpg'; ?>">
                    </a>
                    <a href="#modal-infobutton" class="btn-floating halfway-fab waves-effect waves-light red modal-trigger"><i class="fas fa-info"></i></a>
                </div>
                <div class="card-content">
                    <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>infobutton">
                        <h5><b><?php _e('Search for scientific evidence in the Virtual Health Library', 'e-blueinfo'); ?></b></h5>
                    </a>
                </div>
            </div>
        </article>
    </div>
</section>

<!-- Community Modal Trigger -->
<?php if ( isset($total) && strval($total) > 0 ) : ?>
    <?php foreach ( $community_list as $index => $community ) : $index++; ?>
    <div id="modal-community-<?php echo $community->id; ?>" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4><?php echo $community->name; ?></h4>
            <p><?php echo $community->description; ?></p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat"><?php _e('Close','e-blueinfo'); ?></a>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Guidelines Modal Trigger -->
<div id="modal-guidelines" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4><?php _e('PAHO/WHO Guidelines', 'e-blueinfo'); ?></h4>
        <p><?php _e('Collection of guidelines published by the Panamerican Health Organization (PAHO) and World Health Organization (WHO).', 'e-blueinfo'); ?></p>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat"><?php _e('Close','e-blueinfo'); ?></a>
    </div>
</div>

<!-- InfoButton Modal Trigger -->
<div id="modal-infobutton" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4><?php _e('Contextualized Search', 'e-blueinfo'); ?></h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis, suscipit, in quaerat accusantium eligendi necessitatibus laudantium nemo. Laudantium ducimus, placeat aspernatur, exercitationem doloribus alias nobis rem, assumenda veniam tenetur delectus in error deleniti fugit ipsum dolorum dolores quia magni adipisci, voluptatem ratione sed a excepturi sint. Nemo minus, consequatur maiores magnam doloribus tempora, veritatis mollitia qui eaque? Atque repellendus rem, expedita perspiciatis itaque. Corrupti animi tempora porro adipisci explicabo perferendis repellat, inventore corporis dolore minima, quia modi eligendi earum tempore qui quibusdam! Sed non quos quo nobis est, voluptatem possimus accusantium hic pariatur quisquam tempora quod, iusto et repellendus earum nam ex, enim libero dolores ducimus dolor sit. Quia, nesciunt, fuga! Qui enim a officia repellat adipisci, atque cum. Officia ipsam, laborum exercitationem. Itaque eaque in quos maiores. Impedit soluta dolore molestiae doloribus, voluptate dignissimos quos id quo amet, consequuntur, aliquid repudiandae eveniet quam illum tempore! Saepe dicta, inventore beatae?</p>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat"><?php _e('Close','e-blueinfo'); ?></a>
    </div>
</div>
<!-- ./Template -->

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
