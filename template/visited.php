<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $thumb_service_url, $pdf_service_url, $services_platform_url;

if ( $_COOKIE['userData'] ) {
    $userData = json_decode(base64_decode($_COOKIE['userData']), true);
    $hash = md5($userData['email']);
}

$order = array(
        'RELEVANCE' => 'score desc',
        'YEAR_ASC'  => 'publication_year asc',
        'YEAR_DESC' => 'publication_year desc'
    );

$eblueinfo_config         = get_option('eblueinfo_config');
$eblueinfo_initial_filter = $eblueinfo_config['initial_filter'];
$eblueinfo_addthis_id     = $eblueinfo_config['addthis_profile_id'];

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
$country = ( !empty($_COOKIE['e-blueinfo-country']) ? $_COOKIE['e-blueinfo-country'] : '' );

$user_filter   = stripslashes($_GET['filter']);
$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : '' );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : '' );
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'score desc,da desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : -1 );
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

// Visited Cookie
if ( $_COOKIE['visited_'.$hash] ) {
    $query = '';
    $count = 10;
    $docs  = explode(',', $_COOKIE['visited_'.$hash]);
    $docs  = preg_filter('/^/', 'id:', $docs);
    $docs  = implode(' ', $docs);
    $query = $docs;
    unset($docs);
}

$eblueinfo_service_request = $pdf_service_url . '&q=' . urlencode($query) . '&start=' . $offset . '&rows=' . $count . '&sort=' . urlencode($sort) . '&lang=' . $lang;
$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $total = $response_json->response->numFound;
    $start = $response_json->response->start;
    $snippets = $response_json->highlighting;
    $docs  = $response_json->response->docs;
    $docs = array_reverse($docs);
}

$home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();

?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<section class="container">
    <div class="row">
        <?php require_once('menu.php'); ?>
        <div class="col s10 m11">
            <nav>
                <div class="nav-wrapper">
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Visited Documents','Search',document.getElementById('searchBarInput').value);">
                        <div class="input-field">
                            <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                            <input type="hidden" name="collection" id="collection" value="<?php echo $collection_id; ?>">
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
<h1 class="title"><?php _e('Visited Documents', 'e-blueinfo'); echo ': ' . $total; ?></h1>
<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No documents visited','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<section id="categories" class="container containerAos">
    <div class="row">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
            <article class="col s12" data-aos="fade-left">
                <div class="card cardSingle">
                    <div class="card-content">
                        <b><a class="doc-title" href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id; ?>" onclick="__gaTracker('send','event','Visited Documents','View','<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id; ?>');"><?php echo $doc->ti[0]; ?></a></b> <br />
                        <p><small><?php echo short_string(get_abstract($doc->ab, $lang)); ?></small></p> <br />

                        <a href="#modal" class="btn-floating waves-effect waves-light blue lightn-3 btn-small modal-trigger" title="<?php _e('Favorites', 'e-blueinfo'); ?>" data-author="<?php echo $doc->au[0]; ?>" onclick="__gaTracker('send','event','Visited Documents','Favorites','<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id; ?>');"><i class="material-icons">star</i></a>
                        <?php if ( isset($doc->ur[0]) ) : ?>
                        <a href="<?php echo $doc->ur[0]; ?>" class="btn-floating waves-effect waves-light blue lightn-3 waves-light btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Visited Documents','Full Text','<?php echo $doc->ur[0]; ?>');"><i class="material-icons">visibility</i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Modal Trigger -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h4>Favorites</h4>
        <p>All e-BlueInfo services are free, but to improve your experience there is a need for a quick registration. This allows us to record what you consider favorites, your downloads so you don't have to download again ...</p>
        <a href="#!">Login</a>
        <hr />
        <h4>MyVHL</h4>
        <p>With the same e-BlueInfo registration you have full and degree access to MyVHL</p>
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
