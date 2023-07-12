<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts, $thumb_service_url, $pdf_service_url, $solr_service_url;

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
$q = $query;

// set country
$country = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : '';

$user_filter   = stripslashes($_GET['filter']);
$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : '' );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : '' );
$info_source = ( $_GET['is'] ) ? $_GET['is'] : '';
$media_type  = ( $_GET['mt'] ) ? $_GET['mt'] : '';
$output = ( $_GET['output'] ) ? $_GET['output'] : false;
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'score desc,da desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 10 );
$total  = 0;
$filter = '';
$class  = 'cardSingle';

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

    // Information Source filter
    if ( $info_source ) {
        $query = $query . ' AND is:' . $info_source;
    }

    // Media Type filter
    if ( $media_type ) {
        $query = $query . ' AND mt:' . $media_type;
    }

    // Last Visited Cookie
    if ( $output && 'last_visited' == $output ) {
        $query = '';
        if ( $_COOKIE['last_visited_'.$hash] ) {
            $class = 'cardLastVisited';
            $query = 'id:' . $_COOKIE['last_visited_'.$hash];
        }
    }
    
    // Visited Cookie
    if ( $output && 'visited' == $output ) {
        $query = '';
        if ( $_COOKIE['visited_'.$hash] ) {
            $query = '';
            $count = 10;
            $class = 'cardVisited';
            $docs  = explode(',', $_COOKIE['visited_'.$hash]);
            $docs  = preg_filter('/^/', 'id:', $docs);
            $docs  = implode(' ', $docs);
            $query = $docs;
            unset($docs);
        }
    }

    // Latest Cookie
    if ( $output && 'latest' == $output ) {
        $latest = date("Y-m-d\TH:i:s", strtotime('-15 days'));
        $query = $query . ' AND ud:['.$latest.' TO NOW]';
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

    $community_name = $response_json->response->docs[0]->com;
    if ( count($community_name) > 1 ) {
        if ( $community_id ) {
            $community_name = array_filter($community_name, function($value) use ($community_id) {
                return strpos($value, $community_id) === 0;
            });
            sort($community_name);
            $com_name = get_parent_name($community_name[0], $lang);
        } else {
            $com_name = implode('; ', array_map("get_parent_name", $community_name));
        }
    } else {
        $com_name = get_parent_name($community_name[0], $lang);
    }

    $collection_name = $response_json->response->docs[0]->col;
    if ( count($collection_name) > 1 ) {
        if ( $collection_id ) {
            $collection_name = array_filter($collection_name, function($value) use ($collection_id) {
                return strpos($value, $collection_id) === 0;
            });
            sort($collection_name);
            $col_name = get_parent_name($collection_name[0], $lang);
        } else {
            $col_name = implode('; ', array_map("get_parent_name", $collection_name));
        }
    } else {
        $col_name = get_parent_name($collection_name[0], $lang);
    }
}

/*
$collection_request = $eblueinfo_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;
$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
}
*/

// $filters_cluster_request = $solr_service_url . 'query?q=' . urlencode($query) . '&facet=true&facet.field=mt&facet.field=is&rows=0';
$filters_cluster_request = $solr_service_url . 'query?q=(col:' . $collection_id . '|*)&facet=true&facet.field=mt&facet.field=is&rows=0';
$response = @file_get_contents($filters_cluster_request);
if ($response){
    $response_json = json_decode($response);
    $cluster_total = $response_json->response->numFound;
    $facet_fields = $response_json->facet_counts->facet_fields->mt;
    $mt_cluster = get_cluster($facet_fields);
    $facet_fields = $response_json->facet_counts->facet_fields->is;
    $is_cluster = get_cluster($facet_fields);
}

$media_type_texts = array(
    'pdf'   => __('PDF','e-blueinfo'),
    'video' => __('Video','e-blueinfo'),
    'audio' => __('Audio','e-blueinfo'),
    'presentation' => __('Presentation','e-blueinfo'),
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
        <div class="col s10 m11" id="barSearch">
            <nav>
                <div class="nav-wrapper">
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="gtag('send','event','Browse','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
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
    <?php if ( $community_id && $collection_id ) : ?>
    <div class="title3 light-blue-text text-darken-1"><a href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'collection/?community=' . $community_id; ?>"><?php echo $com_name; ?></a></div>
    <div class="title3 title4"><?php echo $col_name; ?></div>
    <?php endif; ?>
    <div class="col s12 m6 margin1 center-align">
        <a id="moreFilters" class="blue waves-effect waves-light btn margin1"><i class="material-icons left">filter_list</i><?php _e('Filters', 'e-blueinfo'); ?></a>
    </div>
    <div class="row" id="fieldSetFilters" style="display: none;">
        <div class="col s12 m6">
            <select class="info-source center-align <?php if ( is_ios() ) { echo 'browser-default'; } ?>">
                <option value=""><?php _e('All information sources','e-blueinfo'); ?> <?php echo '('.$cluster_total.')'; ?></option>
                <option value="biblio" <?php if ( 'biblio' == $info_source ) { echo 'selected'; } ?>><?php _e('Bibliographic','e-blueinfo'); ?> <?php echo '('.$is_cluster['_biblio']['total'].')'; ?></option>
                <option value="leisref" <?php if ( 'leisref' == $info_source ) { echo 'selected'; } ?>><?php _e('Legislation','e-blueinfo'); ?> <?php echo '('.$is_cluster['_leisref']['total'].')'; ?></option>
                <option value="multimedia" <?php if ( 'multimedia' == $info_source ) { echo 'selected'; } ?>><?php _e('Multimedia','e-blueinfo'); ?> <?php echo '('.$is_cluster['_multimedia']['total'].')'; ?></option>
            </select>
        </div>
        <div class="col s12 m6">
            <select class="media-type center-align <?php if ( is_ios() ) { echo 'browser-default'; } ?>">
                <option value=""><?php _e('All media','e-blueinfo'); ?> <?php echo '('.$cluster_total.')'; ?></option>
                <option value="pdf" <?php if ( 'pdf' == $media_type ) { echo 'selected'; } ?>><?php _e('PDF','e-blueinfo'); ?> <?php echo ( $mt_cluster['_pdf'] ) ? '('.$mt_cluster['_pdf']['total'].')' : "(0)"; ?></option>
                <option value="video" <?php if ( 'video' == $media_type ) { echo 'selected'; } ?>><?php _e('Video','e-blueinfo'); ?> <?php echo ( $mt_cluster['_video'] ) ? '('.$mt_cluster['_video']['total'].')' : "(0)"; ?></option>
                <option value="audio" <?php if ( 'audio' == $media_type ) { echo 'selected'; } ?>><?php _e('Audio','e-blueinfo'); ?> <?php echo ( $mt_cluster['_audio'] ) ? '('.$mt_cluster['_audio']['total'].')' : "(0)"; ?></option>
                <option value="presentation" <?php if ( 'presentation' == $media_type ) { echo 'selected'; } ?>><?php _e('Presentation','e-blueinfo'); ?> <?php echo ( $mt_cluster['_presentation'] ) ? '('.$mt_cluster['_presentation']['total'].')' : "(0)"; ?></option>
                <option value="image" <?php if ( 'image' == $media_type ) { echo 'selected'; } ?>><?php _e('Image','e-blueinfo'); ?> <?php echo ( $mt_cluster['_image'] ) ? '('.$mt_cluster['_image']['total'].')' : "(0)"; ?></option>
                <option value="link" <?php if ( 'link' == $media_type ) { echo 'selected'; } ?>><?php _e('Link','e-blueinfo'); ?> <?php echo ( $mt_cluster['_link'] ) ? '('.$mt_cluster['_link']['total'].')' : "(0)"; ?></option>
            </select>
        </div>
        <?php if ( $_COOKIE['userData'] ) : ?>
        <div class="col s12 m6 offset-m3">
            <select class="center-align <?php if ( is_ios() ) { echo 'browser-default'; } ?>" onchange="location=this.value;">
                <option value="<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id; ?>" <?php if ( !$output ) { echo 'selected'; } ?>><?php _e('All documents','e-blueinfo'); ?></option>
                <option value="<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=last_visited'; ?>" <?php if ( 'last_visited' == $output ) { echo 'selected'; } ?>><?php _e('Last Visited','e-blueinfo'); ?></option>
                <option value="<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=visited'; ?>" <?php if ( 'visited' == $output ) { echo 'selected'; } ?>><?php _e('Visited','e-blueinfo'); ?></option>
                <option value="<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=latest'; ?>" <?php if ( 'latest' == $output ) { echo 'selected'; } ?>><?php _e('Latest','e-blueinfo'); ?></option>
            </select>
        </div>
        <?php endif; ?>
    </div>
<!--
    <div class="row">
        <div class="col s4 m3 offset-l2 l2 center-align">
            <div class="blue-grey lighten-4" id="cardSingle" onclick="location='<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id; ?>';"><small><?php _e('All','e-blueinfo'); ?></small></div>
        </div>
        <div class="col s4 m3 l2 center-align">
            <div class="blue darken-1 white-text" id="cardLastVisited" onclick="location='<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=last_visited'; ?>';"><small><?php _e('Last Visited','e-blueinfo'); ?></small></div>
        </div>
        <div class="col s4 m3 l2 center-align">
            <div class="cyan lighten-3" id="cardVisited" onclick="location='<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=visited'; ?>';"><small><?php _e('Visited','e-blueinfo'); ?></small></div>
        </div>
        <div class="col s4 m3 l2 center-align">
            <div class="light-green accent-3" id="cardLatest" onclick="location='<?php echo real_site_url($eblueinfo_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id . '&output=latest'; ?>';"><small><?php _e('Latest','e-blueinfo'); ?></small></div>
        </div>
    </div>
-->
</section>

<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <?php if ( $output && 'last_visited' == $output || $output && 'visited' == $output ) : ?>
            <span class="blue-text text-darken-2"><?php _e('No documents visited','e-blueinfo'); ?></span>
            <?php else : ?>
            <span class="blue-text text-darken-2"><?php _e('No results found','e-blueinfo'); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row flexContainer">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
            <?php $altid = ( $doc->alternate_ids ) ? $doc->alternate_ids[0] : $doc->id; ?>
            <?php $title = ( 'leisref' == $doc->is ) ? get_leisref_title($doc, $lang) : $doc->ti[0]; ?>
            <?php $latest = ( strtotime($doc->ud) > strtotime('-15 days') ) ? 'cardLatest' : ''; ?>
            <article class="flexCol1 item card-<?php echo $lang; ?> <?php echo $class; ?> <?php echo $latest; ?>">
                <div class="row padding3 cardBox">
                    <div class="cardBoxText">
                        <a class="e-blueinfo-doc" data-docid="<?php echo $doc->id; ?>" href="<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id; ?>" onclick="gtag('send','event','Browse','View','<?php echo $countries[$country].'|'.$title; ?>');">
                            <div class="col s3">
                                <img src="<?php echo get_thumbnail($doc->id, $doc->mt); ?>" class="thumbnail responsive-img" alt="" onerror="this.src='<?php echo EBLUEINFO_PLUGIN_URL . "template/images/nothumb.jpg"; ?>'">
                            </div>
                            <div class="col s7">
                                <p class="doc-title"><?php echo $title; ?></p>
                                <br /><br />
                            </div>
                        </a>
                        <div class="col s2 right-align">
                            <?php if ( 'leisref' != $doc->is ) : ?>
                            <div class="iconActions btn-favorites" data-author="<?php echo implode('; ', $doc->au); ?>" data-altid="<?php echo $altid; ?>" data-docid="<?php echo $doc->id; ?>"><a class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Favorites', 'e-blueinfo'); ?>" onclick="gtag('send','event','Browse','Favorites','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">star</i></a></div>
                            <?php endif; ?>
                            <?php if ( isset($doc->ur[0]) ) : ?>
                            <div class="iconActions"><a href="<?php echo $doc->ur[0]; ?>" data-docid="<?php echo $doc->id; ?>" class="btn-ajax e-blueinfo-doc btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="gtag('send','event','Browse','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                            <?php endif; ?>
                        </div>
                        <div class="col s12 blue-grey lighten-5 padding1 boxCardGray">
                            <small><?php echo $media_type_texts[$doc->mt]; ?></small> | <small><?php _e('Downloads', 'e-blueinfo'); ?>: <?php echo ( $eblueinfo_data[$doc->id] ) ? $eblueinfo_data[$doc->id] : 0; ?></small>
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
