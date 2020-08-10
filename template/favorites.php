<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $thumb_service_url, $pdf_service_url, $services_platform_url;

if ( $_COOKIE['userData'] ) {
    $userData = json_decode(base64_decode($_COOKIE['userData']), true);
    $hash = md5($userData['email']);
}

// set userID
$userID = ( !empty($_COOKIE['userID']) ? $_COOKIE['userID'] : '' );

$order = array(
        'RELEVANCE' => 'score desc',
        'YEAR_ASC'  => 'publication_year asc',
        'YEAR_DESC' => 'publication_year desc'
    );

$eblueinfo_data           = get_option('eblueinfo_data');
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
$info_source = ( $_GET['is'] ) ? $_GET['is'] : '';
$media_type  = ( $_GET['mt'] ) ? $_GET['mt'] : '';
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'score desc,da desc' );
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

$eblueinfo_service_request = $services_platform_url . '/client/controller/api/favorites/?source=e-blueinfo&userID='.$userID;
$response = @file_get_contents($eblueinfo_service_request);
if ( $response ) {
    $response_json = json_decode($response);
    $count = $response_json->total;
    $fav_docs = $response_json->docs;
    $fav_docs_ids = wp_list_pluck($fav_docs, 'docID');
    $docs  = preg_filter('/^/', 'id:', $fav_docs_ids);
    $docs  = implode(' ', $docs);
    $query = $docs;
    unset($docs);
}

// Information Source filter
if ( $info_source ) {
    if ( empty($query) ) {
        $query = 'is:' . $info_source;
    } else {
        $query = 'is:' . $info_source . ' AND (' . $query . ')';
    }
}

// Media Type filter
if ( $media_type ) {
    if ( empty($query) ) {
        $query = 'mt:' . $media_type;
    } else {
        $query = 'mt:' . $media_type . ' AND (' . $query . ')';
    }
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

$media_type_texts = array(
    'pdf'   => __('PDF','e-blueinfo'),
    'video' => __('Video','e-blueinfo'),
    'audio' => __('Audio','e-blueinfo'),
    'ppt'   => __('PPT','e-blueinfo'),
    'image' => __('Image','e-blueinfo'),
    'link'  => __('Link','e-blueinfo')
);

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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Favorite Documents','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
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
<h1 class="title"><?php _e('Favorites', 'e-blueinfo'); echo ': ' . $total; ?></h1>
<section class="container">
    <div class="row">
        <div class="col s12 m6">
            <select class="info-source center-align">
                <option value=""><?php _e('All information sources','e-blueinfo'); ?> <?php if ( empty($info_source) ) { echo '('.$total.')'; } ?></option>
                <option value="biblio" <?php if ( 'biblio' == $info_source ) { echo 'selected'; } ?>><?php _e('Bibliographic','e-blueinfo'); ?> <?php if ( 'biblio' == $info_source ) { echo '('.$total.')'; } ?></option>
                <option value="leisref" <?php if ( 'leisref' == $info_source ) { echo 'selected'; } ?>><?php _e('Legislation','e-blueinfo'); ?> <?php if ( 'leisref' == $info_source ) { echo '('.$total.')'; } ?></option>
            </select>
        </div>
        <div class="col s12 m6">
            <select class="media-type center-align">
                <option value=""><?php _e('All media','e-blueinfo'); ?></option>
                <option value="pdf" <?php if ( 'pdf' == $media_type ) { echo 'selected'; } ?>><?php _e('PDF','e-blueinfo'); ?></option>
                <option value="video" <?php if ( 'video' == $media_type ) { echo 'selected'; } ?>><?php _e('Video','e-blueinfo'); ?></option>
                <option value="audio" <?php if ( 'audio' == $media_type ) { echo 'selected'; } ?>><?php _e('Audio','e-blueinfo'); ?></option>
                <option value="ppt" <?php if ( 'ppt' == $media_type ) { echo 'selected'; } ?>><?php _e('PPT','e-blueinfo'); ?></option>
                <option value="image" <?php if ( 'image' == $media_type ) { echo 'selected'; } ?>><?php _e('Image','e-blueinfo'); ?></option>
                <option value="link" <?php if ( 'link' == $media_type ) { echo 'selected'; } ?>><?php _e('Link','e-blueinfo'); ?></option>
            </select>
        </div>
    </div>
</section>

<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No favorite documents found','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row flexContainer">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
            <?php $title = ( 'leisref' == $doc->is ) ? get_leisref_title($doc, $lang) : $doc->ti[0]; ?>
            <article class="flexCol1 item cardSingle">
                <div class="row padding3 cardBox">
                    <div class="cardBoxText">
                        <a class="e-blueinfo-doc" data-docid="<?php echo $doc->id; ?>" href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id; ?>" onclick="__gaTracker('send','event','Favorite Documents','View','<?php echo $countries[$country].'|'.$title; ?>');">
                            <div class="col s3">
                                <img src="<?php echo $thumb_service_url . '/' . $doc->id . '/' . $doc->id . '.jpg'; ?>" class="responsive-img" alt="" onerror="this.src='http://thumbs.bireme.org/nothumb.jpg'">
                            </div>
                            <div class="col s7">
                                <p class="doc-title"><?php echo $title; ?></p>
                                <br /><br />
                            </div>
                        </a>
                        <div class="col s2 right-align">
                            <?php if ( isset($doc->ur[0]) ) : ?>
                            <div class="iconActions"><a href="<?php echo $doc->ur[0]; ?>" data-docid="<?php echo $doc->id; ?>" class="btn-ajax e-blueinfo-doc btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Favorite Documents','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                            <?php endif; ?>
                        </div>
                        <div class="col s12 blue-grey lighten-5 padding1 boxCardGray">
                            <small><?php echo $media_type_texts[$doc->mt]; ?></small> | <small>Update: 01/01/2020</small> | <small>Downloads: <?php echo $eblueinfo_data['country'.$country]['doc_'.$doc->id]; ?></small>
                        </div>
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

<?php if ( $_COOKIE['userData'] ) : ?>
<!-- Visited and Last Visited -->
<script type="text/javascript">
    (function($) { 
        $( document ).on( "mousedown", ".e-blueinfo-doc", function() {
            var list = new cookieList("visited_<?php echo $hash; ?>");
            var docid = $( this ).data('docid');
            $.cookie('last_visited_<?php echo $hash; ?>', docid, { path: '/', expires: 365 * 10 });
            list.add(docid);
        });
    })(jQuery);
</script>
<!-- ./Visited and Last Visited -->
<?php endif; ?>

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
