<?php
/*
Template Name: Memória Azul Collection Page
*/
global $memoria_azul_service_url, $memoria_azul_plugin_slug, $memoria_azul_plugin_title, $memoria_azul_texts, $pdf_service_url;

require_once(MEMORIA_AZUL_PLUGIN_PATH . '/lib/Paginator.php');

$order = array(
        'RELEVANCE' => 'score desc',
        'YEAR_ASC'  => 'publication_year asc',
        'YEAR_DESC' => 'publication_year desc'
    );

$memoria_azul_config         = get_option('memoria_azul_config');
$memoria_azul_initial_filter = $memoria_azul_config['initial_filter'];
$memoria_azul_addthis_id     = $memoria_azul_config['addthis_profile_id'];

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
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'da desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 6 );
$total  = 0;
$filter = '';
$com_name = '-';
$col_name = '-';

if ($memoria_azul_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $memoria_azul_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $memoria_azul_initial_filter;
    }
}else{
    $filter = $user_filter;
}

$start = ($page * $count) - $count;

if ( !empty($collection_id) ) {
    if ( empty($query) ) {
        $query = 'col:' . $collection_id;
    } else {
        $query = 'col:' . $collection_id . ' AND ' . $query;
    }
}

if ( !empty($community_id) ) {
    if ( empty($query) && empty($collection_id) ) {
        $query = 'com:' . $community_id;
    } else {
        $query = 'com:' . $community_id . ' AND ' . $query;
    }
}

$memoria_azul_service_request = $pdf_service_url . '&q=' . urlencode($query) . '&start=' . $offset . '&rows=' . $count . '&sort=' . urlencode($sort) . '&lang=' . $lang;

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filter){
        preg_match('/([a-z_]+):(.+)/',$filter, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$response = @file_get_contents($memoria_azul_service_request);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $total = $response_json->response->numFound;
    $start = $response_json->response->start;
    $docs  = $response_json->response->docs;
    $snippets = $response_json->highlighting;
}

$community_request = $memoria_azul_service_url . 'api/community/?community=' . $community_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($community_request);
if ($response){
    $community = json_decode($response);
    $com_name = $community->objects{0}->name;
    // echo "<pre>"; print_r($community); echo "</pre>"; die();
}

$collection_request = $memoria_azul_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    $col_name = $collection->objects{0}->name;
    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
}

$params = $count != 2 ? '&count=' . $count : '';
$params .= !empty($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';

$page_url_params = real_site_url($memoria_azul_plugin_slug) . 'browse/?collection=' . $collection_id . $params;
// $feed_url = real_site_url($memoria_azul_plugin_slug) . 'memoria-azul-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);
$home_url = isset($memoria_azul_config['home_url_' . $lang]) ? $memoria_azul_config['home_url_' . $lang] : real_site_url();
/*
$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);
*/
?>

<?php get_header('memoria-azul'); ?>

<!-- Breadcrumb -->
<ol class="breadcrumb">
    <li><a href="<?php echo $home_url ?>"><?php _e('Home','memoria-azul'); ?></a></li>
    <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug); ?>"><?php echo $memoria_azul_plugin_title; ?> </a></li>
    <?php if ( isset($community_id, $community) ) : ?>
    <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'collection/?community=' . $community_id; ?>"><?php echo $community->objects{0}->name; ?> </a></li>
    <?php endif; ?>
    <?php if ( isset($community_id, $collection_id, $collection) ) : ?>
    <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'browse/?community=' . $community_id . '&collection=' . $collection_id; ?>"><?php echo $collection->objects{0}->name; ?> </a></li>
    <?php endif; ?>
    <?php if ($q == '' && $filter == ''): ?>
    <li class="active"><?php echo $doc[0]->reference_title; ?></li>
    <?php else: ?>
    <li class="active"><?php _e('Search result', 'memoria-azul'); ?></li>
    <?php endif; ?>
</ol>
<!-- ./Breadcrumb -->

<!-- Template -->
<section id="memoria-azul" class="pb-5 blueinfo-search">
    <div class="container">
        <!-- Search Bar -->
        <header class="page-header">
            <div class="searchBarMain">
        		<i class="material-icons searchBarSearchIcon noUserSelect" onclick="__gaTracker('send','event','Search Results','Search',document.getElementById('searchBarInput').value);">search</i>
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($memoria_azul_plugin_slug); ?>search">
                    <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                    <input type="hidden" name="collection" id="collection" value="<?php echo $collection_id; ?>">
        		    <input type="text" name="q" value="<?php echo $q; ?>" id="searchBarInput" placeholder="<?php _e('Search...', 'memoria-azul'); ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                </form>
        		<i class="material-icons clearSearchBarField noUserSelect" onClick="resetInput()">clear</i>
        	</div>
        </header>
        <h3 class="section-title"><?php _e('Results', 'memoria-azul'); echo ': ' . $total; ?></h3>
        <div class="row">
            <?php if ( isset($total) && strval($total) == 0 ) : ?>
            <h4 class="results"><?php _e('No results found. Try searching with another keywords.','memoria-azul'); ?></h4>
            <?php else : ?>
                <?php foreach ( $docs as $index => $doc ) : $index++; $id = "s".$doc->id; ?>
                    <?php
                        if ( !isset($community) ) {
                            $com_name = array();

                            foreach ($doc->com as $com) {
                                $community_request = $memoria_azul_service_url . 'api/community/?community=' . $com . '&format=' . $format . '&lang=' . $lang;

                                $response = @file_get_contents($community_request);
                                if ($response){
                                    $community = json_decode($response);
                                    $com_name[] = $community->objects{0}->name;
                                    // echo "<pre>"; print_r($community); echo "</pre>"; die();
                                }
                            }

                            $com_name = ( $com_name ) ? implode('; ', $com_name) : '-';
                        }

                        if ( !isset($collection) ) {
                            $col_name = array();

                            foreach ($doc->col as $col) {
                                $collection_request = $memoria_azul_service_url . 'api/collection/?collection=' . $col . '&format=' . $format . '&lang=' . $lang;

                                $response = @file_get_contents($collection_request);
                                if ($response){
                                    $collection = json_decode($response);
                                    $col_name[] = $collection->objects{0}->name;
                                    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
                                }
                            }

                            $col_name = ( $col_name ) ? implode('; ', $col_name) : '-';
                        }
                    ?>
                    <!-- Document -->
                    <div class="col-xs-12 col-sm-12 col-md-12 item">
                        <div id="<?php echo $id; ?>" class="image-flip">
                            <div class="mainflip">
                                <div class="doc-meta" onclick="__gaTracker('send','event','Search Results','Full Text','<?php echo $doc->ur[0]; ?>');">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="meta">
                                                <a class="full-text" href="<?php echo $doc->ur[0]; ?>"><h4 class="card-title"><?php echo substr($doc->ti[0], 4); ?></h4></a>
                                                <strong><?php _e('Communities', 'memoria-azul'); ?></strong>
                                                <p class="card-text"><?php echo $com_name; ?></p>
                                                <strong><?php _e('Collections', 'memoria-azul'); ?></strong>
                                                <p class="card-text"><?php echo $col_name; ?></p>
                                                <?php if ( isset($snippets->{$doc->id}->_text_) ) : ?>
                                                <p class="paragraph"><?php echo get_highlight($snippets->{$doc->id}->_text_); ?></p>
                                                <?php endif; ?>
                                                <a class="btn btn-primary btn-sm btn-details redirect" href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id . '&lang=' . $lang; ?>" onclick="__gaTracker('send','event','Search Results','View','<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id; ?>');"><?php _e('See details', 'memoria-azul'); ?></a>
                                                <a class="btn btn-primary btn-sm btn-meta redirect" href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id . '&lang=' . $lang; ?>" onclick="__gaTracker('send','event','Search Results','View','<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id; ?>');"><i class="fa fa-info-circle"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ./Document -->
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- ./Template -->
<!-- Pagination -->
<?php // echo $pages->display_pages(); ?>
<!-- ./Pagination -->
<script type="text/javascript" src="<?php echo MEMORIA_AZUL_PLUGIN_URL . 'template/js/scripts.js' ?>"></script>
<?php if ( $total > $count ) : ?>
<!-- Load More -->
<div class="load-more col-xs-3 col-sm-3 col-md-3">
    <a href="#" id="loadMore"><span class="text"><?php _e('Load More', 'memoria-azul') ?></span></a>
    <p class="totop">
        <a href="#top"><?php _e('back to top', 'memoria-azul') ?></a>
    </p>
    <span class="loadmore-last"><?php _e('No more documents', 'memoria-azul'); ?></span>
</div>
<!-- ./Load More -->
<script type="text/javascript">
    $('.row').loadmore('', {
        loadingText : '<?php _e('Loading...', 'memoria-azul') ?>',
        filterResult: '.row > .item',
        useExistingButton: '#loadMore',
        useOffset: true,
        rowsPerPage: 1,
        baseOffset: -1,
        itemSelector: '.image-flip',
        pageParam : 'offset',
        pageStartParam: ''
    });

    $(document).on("loadmore:last", function() {
        var msg = $('.loadmore-last').text();
        alert(msg);
    });
</script>
<?php endif; ?>
<?php get_footer(); ?>
