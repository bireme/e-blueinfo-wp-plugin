<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $thumb_service_url, $pdf_service_url;

require_once(EBLUEINFO_PLUGIN_PATH . '/lib/Paginator.php');

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

// set query using default param q (query) or s (wordpress search) or newexpr (metaiah)
$query = $_GET['s'] . $_GET['q'];
$query = stripslashes( trim($query) );
$q = $query;

$user_filter   = stripslashes($_GET['filter']);
$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : '' );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : '' );
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'score desc,da desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 6 );
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

if ( !empty($collection_id) ) {
    if ( empty($query) ) {
        $query = 'col:' . $collection_id . '|*';
    } else {
        $query = '(col:' . $collection_id . '|*) AND ' . $query;
    }
}

// echo "<pre>"; print_r($query); echo "</pre>"; die();

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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Browse','Search',document.getElementById('searchBarInput').value);">
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
<section class="container">
    <div class="row">
        <div class="col s12 m6">
            <select class="center-align">
                <option value="All" selected>All information sources (<?php echo $total; ?>)</option>
                <option value="Option 1">Option 1</option>
                <option value="Option 2">Option 2</option>
                <option value="Option 3">Option 3</option>
            </select>
        </div>
        <div class="col s12 m6">
            <select class="center-align">
                <option value="All" selected>All media (<?php echo $total; ?>)</option>
                <option value="PDF">PDF</option>
                <option value="Video">Video</option>
                <option value="Audio">Audio</option>
                <option value="PPT">PPT</option>
                <option value="Image">Imagem</option>
                <option value="Link">Link</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col s4 m3 offset-l3 l2 center-align">
            <div class="blue-grey lighten-4" id="cardSingle"><small>All</small></div>
        </div>
        <div class="col s4 m3 l2 center-align">
            <div class="blue darken-1 white-text" id="cardLastVisited"><small>Last Visited</small></div>
        </div>
        <div class="col s4 m3 l2 center-align">
            <div class="cyan lighten-3" id="cardVisited"><small>Visted</small></div>
        </div>
    </div>
</section>

<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No results found','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row flexContainer">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
            <article class="flexCol1 item cardSingle cardLastVisited cardVisited">
                <div class="row padding3 cardBox">
                    <div class="cardBoxText">
                        <a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id . '&lang=' . $lang; ?>" onclick="__gaTracker('send','event','Browse','View','<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id; ?>');">
                            <div class="col s3">
                                <img src="<?php echo $thumb_service_url . '/' . $doc->id . '/' . $doc->id . '.jpg'; ?>" class="responsive-img" alt="" onerror="this.src='http://thumbs.bireme.org/nothumb.jpg'">
                            </div>
                            <div class="col s7">
                                <?php echo $doc->ti[0]; ?>
                                <br /><br />
                            </div>
                        </a>
                        <div class="col s2 right-align">
                            <div class="iconActions"><a href="#modal" class="btn-floating waves-effect waves-light blue lightn-3 btn-small modal-trigger" title="<?php _e('Favorites', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Browse','Favorites','<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id; ?>');"><i class="material-icons">star</i></a></div>
                            <?php if ( isset($doc->ur[0]) ) : ?>
                            <div class="iconActions"><a href="<?php echo $doc->ur[0]; ?>" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Browse','Full Text','<?php echo $doc->ur[0]; ?>');"><i class="material-icons">visibility</i></a></div>
                            <?php endif; ?>
                        </div>
                        <div class="col s12 blue-grey lighten-5 padding1 boxCardGray">
                            <small>PDF</small> | <small>Update: 01/01/2020</small> | <small>Downloads: 0</small>
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

            $('#cardSingle').on("click", function() {
                $('.load-more').show();
            });

            $('#cardLastVisited, #cardVisited').on("click", function() {
                $('.load-more').hide();
            });
        });
    })(jQuery);
</script>
<!-- ./Load More -->
<?php endif; ?>

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
