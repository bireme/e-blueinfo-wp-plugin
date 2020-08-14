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
$country = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : '';

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
    $docs  = $response_json->response->docs;
    $snippets = $response_json->highlighting;
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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Visited Documents','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
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
            <?php $altid = ( $doc->alternate_ids ) ? $doc->alternate_ids[0] : $doc->id; ?>
            <?php $title = ( 'leisref' == $doc->is ) ? get_leisref_title($doc, $lang) : $doc->ti[0]; ?>
            <article class="col s12" data-aos="fade-left">
                <div class="card cardSingle">
                    <div class="card-content">
                        <b><a class="doc-title" href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id; ?>" onclick="__gaTracker('send','event','Visited Documents','View','<?php echo $countries[$country].'|'.$title; ?>');"><?php echo $title; ?></a></b> <br />
                        <?php if ( 'leisref' == $doc->is ) : ?>
                            <?php if ( $doc->oe ) : ?>
                            <p><small><?php echo short_string($doc->oe[0]); ?></small></p> <br />
                            <?php elseif ( $doc->ue ) : ?>
                            <p><small><?php echo short_string($doc->ue[0]); ?></small></p> <br />
                            <?php else : ?>
                            <p><small>-</small></p> <br />
                            <?php endif; ?>
                        <?php else : ?>
                        <p><small><?php echo short_string(get_abstract($doc->ab, $lang)); ?></small></p> <br />
                        <?php endif; ?>
                        
                        <?php if ( 'leisref' != $doc->is ) : ?>
                        <a class="btn-favorites btn-floating waves-effect waves-light blue lightn-3 btn-small" data-author="<?php echo $doc->au[0]; ?>" data-altid="<?php echo $altid; ?>" data-docid="<?php echo $doc->id; ?>" title="<?php _e('Favorites', 'e-blueinfo'); ?>" data-author="<?php echo $doc->au[0]; ?>" onclick="__gaTracker('send','event','Visited Documents','Favorites','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">star</i></a>
                        <?php endif; ?>
                        <?php if ( isset($doc->ur[0]) ) : ?>
                        <a href="<?php echo $doc->ur[0]; ?>" data-docid="<?php echo $doc->id; ?>" class="btn-ajax btn-floating waves-effect waves-light blue lightn-3 waves-light btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Visited Documents','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a>
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
        <h4><?php _e('Favorites', 'e-blueinfo'); ?></h4>
        <p><?php _e("All e-BlueInfo services are free, but to improve your experience there is a need for a quick registration. This allows us to record your favorites list, visited documents and other features.", 'e-blueinfo'); ?></p>
        <a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'auth/'; ?>"><?php _e('Login', 'e-blueinfo'); ?></a>
        <hr />
        <h4><?php _e('MyVHL', 'e-blueinfo'); ?></h4>
        <p><?php _e('With the same e-BlueInfo registration you have full access to MyVHL Services Platform.', 'e-blueinfo'); ?></p>
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
