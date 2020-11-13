<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $pdf_service_url;

require_once(EBLUEINFO_PLUGIN_PATH . '/lib/Paginator.php');

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
$q = $query;
$query = prepare_query($query);

// set country
$country = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : '';

$user_filter   = stripslashes($_GET['filter']);
$community_id  = ( !empty($_GET['community']) ? explode(',', $_GET['community']) : '' );
$com_id = ( !empty($community_id) && count($community_id) == 1 ) ? $community_id[0] : NULL;
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : NULL );
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'score desc,da desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 10 );
$total  = 0;
$filter = '';
$com_name = '-';
$col_name = '-';

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

if ( !empty($collection_id) ) {
    if ( empty($query) ) {
        $query = 'col:' . $collection_id . '|*';
    } else {
        $query = '(col:' . $collection_id . '|*) AND ' . $query;
    }
}

if ( !empty($community_id) ) {
    $com_ids = implode('|* OR com:', $community_id);
    if ( empty($query) && empty($collection_id) ) {
        $query = '(com:' . $com_ids . '|*)';
    } else {
        $query = '(com:' . $com_ids . '|*) AND ' . $query;
    }
    $community_id = implode(',', $community_id);
}

// echo "<pre>"; print_r($query); echo "</pre>"; die();

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $uf){
        preg_match('/([a-z_]+):(.+)/', $uf, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
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

$collection_request = $eblueinfo_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;
$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    $col_name = $collection->objects{0}->name;
    $com_name = $collection->objects{0}->parent;
    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
}

$params = $count != 2 ? '&count=' . $count : '';
$params .= !empty($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';

$page_url_params = real_site_url($eblueinfo_plugin_slug) . 'browse/?collection=' . $collection_id . $params;
// $feed_url = real_site_url($eblueinfo_plugin_slug) . 'e-blueinfo-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);
$home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
/*
$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);
*/
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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Search Results','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
                        <div class="input-field">
                            <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                            <input type="hidden" name="collection" id="collection" value="<?php echo $collection_id; ?>">
                            <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                            <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">

                            <a href="#!" id="speakBtn"><i class="material-icons">settings_voice</i></a>
                            <input type="search" name="q" value="<?php echo $q; ?>" id="searchBarInput">
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
<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No results found. Try searching with another keywords.', 'e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<h1 class="title"><?php _e('Results', 'e-blueinfo'); echo ': ' . $total; ?></h1>
<section class="container containerAos">
    <div class="row flexContainer">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
            <?php
                $altid = ( $doc->alternate_ids ) ? $doc->alternate_ids[0] : $doc->id;
                $title = ( 'leisref' == $doc->is ) ? get_leisref_title($doc, $lang) : $doc->ti[0];
                $class = ( strtotime($doc->ud) > strtotime('-15 days') ) ? 'cardLatest' : '';
                $com_name = ( $doc->com ) ? implode('; ', array_map("remove_prefix", $doc->com)) : '-';
                $col_name = ( $doc->col ) ? implode('; ', array_map("remove_prefix", $doc->col)) : '-';
            ?>
            <article class="flexCol1 item cardSingle card-<?php echo $lang; ?> <?php echo $class; ?>">
                <div class="row padding3 cardBox">
                    <div class="col s3">
                        <img src="<?php echo get_thumbnail($doc->id, $doc->mt); ?>" class="thumbnail responsive-img" alt="">
                    </div>
                    <div class="col s7">
                        <p><b><?php _e('Contents', 'e-blueinfo'); ?>:</b> <br /><?php echo $com_name; ?></p>
                        <p><b><?php _e('Collections', 'e-blueinfo'); ?>:</b> <br /><?php echo $col_name; ?></p>
                    </div>
                    <div class="col s2 right-align">
                        <?php if ( 'leisref' != $doc->is ) : ?>
                        <div class="iconActions btn-favorites" data-author="<?php echo $doc->au[0]; ?>" data-altid="<?php echo $altid; ?>" data-docid="<?php echo $doc->id; ?>"><a class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Add to Favorites', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Search Results','Favorites','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">star</i></a></div>
                        <?php endif; ?>
                        <?php if ( isset($doc->ur[0]) ) : ?>
                        <div class="iconActions"><a href="<?php echo $doc->ur[0]; ?>" data-docid="<?php echo $doc->id; ?>" class="btn-ajax e-blueinfo-doc btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Search Results','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                        <?php endif; ?>
                    </div>
                    <div class="col s12 blue-grey lighten-5 padding1">
                        <a class="e-blueinfo-doc" data-docid="<?php echo $doc->id; ?>" href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id; ?>" onclick="__gaTracker('send','event','Search Results','View','<?php echo $countries[$country].'|'.$title; ?>');">
                            <p class="doc-title"><?php echo $title; ?></p>
                        </a>
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

<?php if ( $total > $count ) : ?>
<!-- Load More -->
<div class="load-more col s3">
    <a href="#" id="loadMore" onclick="return false;"><span class="text"><?php _e('Load More', 'e-blueinfo') ?></span></a>
    <span class="loadmore-last"><?php _e('No more documents', 'e-blueinfo'); ?></span>
</div>
<script type="text/javascript">
    (function($) { 
        $(function () {
            $('.flexContainer').loadmore('', {
                loadingText : '<?php _e('Loading...', 'e-blueinfo') ?>',
                filterResult: '.flexContainer > .item',
                useExistingButton: '#loadMore',
                useOffset: true,
                rowsPerPage: 1,
                baseOffset: -1,
                itemSelector: '.cardBox',
                pageParam : 'offset',
                pageStartParam: ''
            });

            $(document).on("loadmore:last", function() {
                var msg = $('.loadmore-last').text();
                alert(msg);
            });
        });
    })(jQuery);
</script>
<!-- ./Load More -->
<?php endif; ?>

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
