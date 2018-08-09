<?php
/*
Template Name: Memória Azul Collection Page
*/
global $memoria_azul_service_url, $memoria_azul_plugin_slug, $memoria_azul_plugin_title, $memoria_azul_texts;

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

$user_filter   = stripslashes($_GET['filter']);
$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : '' );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : '' );
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'created_date desc' );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 6 );
$total  = 0;
$filter = '';

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

if ( $query ) {
    $memoria_azul_service_request = $memoria_azul_service_url . 'api/bibliographic/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&count=' . $count . '&sort=' . urlencode($sort) . '&lang=' . $lang;
} else {
    $memoria_azul_service_request = $memoria_azul_service_url . 'api/bibliographic/?collection=' . $collection_id . '&format=' . $format . '&offset=' . $offset . '&limit=' . $count . '&lang=' . $lang;
}

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
    $total = $response_json->meta->total_count;
    $start = $response_json->meta->offset;
    $next  = $response_json->meta->next;
    $docs = $response_json->objects;
}

$collection_request = $memoria_azul_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
}

$params = $count != 2 ? '&count=' . $count : '';
$params .= !empty($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';

$page_url_params = real_site_url($memoria_azul_plugin_slug) . 'browse/?collection=' . $collection_id . $params;
$feed_url = real_site_url($memoria_azul_plugin_slug) . 'memoria-azul-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);

$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);

$home_url = isset($memoria_azul_config['home_url_' . $lang]) ? $memoria_azul_config['home_url_' . $lang] : real_site_url();
?>

<?php get_header('memoria-azul');?>

<!-- Breadcrumb -->
<ol class="breadcrumb">
    <li><a href="<?php echo $home_url ?>"><?php _e('Home','memoria-azul'); ?></a></li>
    <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug); ?>"><?php echo $memoria_azul_plugin_title; ?> </a></li>
    <li><a href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'collection/?community=' . $community_id; ?>"><?php echo $collection->objects{0}->parent; ?> </a></li>
    <?php if ($query == '' && $filter == ''): ?>
    <li class="active"><?php echo $collection->objects{0}->name; ?></li>
    <?php else: ?>
    <li class="active"><?php _e('Search result', 'memoria-azul'); ?></li>
    <?php endif; ?>
</ol>
<!-- ./Breadcrumb -->

<!-- Template -->
<section id="memoria-azul" class="pb-5">
    <div class="container">
        <!-- Search Bar -->
        <header class="page-header">
            <div class="searchBarMain">
        		<i class="material-icons searchBarSearchIcon noUserSelect">search</i>
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($memoria_azul_plugin_slug); ?>search">
                    <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                    <input type="hidden" name="collection" id="collection" value="<?php echo $collection_id; ?>">
        		    <input type="text" name="q" value="<?php echo $query; ?>" id="searchBarInput" placeholder="<?php _e('Search...', 'memoria-azul'); ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                </form>
        		<i class="material-icons clearSearchBarField noUserSelect" onClick="resetInput()">clear</i>
        	</div>
        </header>
        <h3 class="section-title"><?php _e($collection->objects{0}->name, 'memoria-azul'); ?></h3>
        <div class="row">
            <?php if ( isset($total) && strval($total) == 0 ) : ?>
            <h4><?php _e('No results found','memoria-azul'); ?></h4>
            <?php else : ?>
            <div class="h-label col-xs-12 col-sm-12 col-md-12 border-bottom">
                <?php if ( ( $query != '' || $user_filter != '' ) && strval($total) > 0) :?>
                <h4><?php _e('Results', 'memoria-azul'); echo ': ' . $total ?></h4>
                <?php else: ?>
                <h4><?php _e('Total', 'memoria-azul'); echo ': ' . $total ?></h4>
                <?php endif; ?>
            </div>
                <?php foreach ( $docs as $index => $doc ) : $index++; $id = "col".$doc->id; ?>
                <!-- Collection -->
                <div class="col-xs-12 col-sm-6 col-md-4 item">
                    <div id="<?php echo $id; ?>" class="image-flip">
                        <div class="mainflip">
                            <div class="doc">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <p><img class="img-fluid" src="http://placehold.it/160x210" alt="card image"></p>
                                        <?php if ( $doc->electronic_address[0]->_u ) : ?>
                                        <a class="full-text" href="<?php echo $doc->electronic_address[0]->_u; ?>"><h4 class="card-title"><?php echo $doc->reference_title; ?></h4></a>
                                        <?php else : ?>
                                        <a class="full-text" href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id . '&lang=' . $lang; ?>"><h4 class="card-title"><?php echo $doc->reference_title; ?></h4></a>
                                        <?php endif; ?>
                                        <!-- <p class="card-text">This is basic card with image on top, title, description and button.</p> -->
                                        <a class="btn btn-primary btn-sm redirect" href="<?php echo real_site_url($memoria_azul_plugin_slug) . 'doc/' . $doc->id . '?community=' . $community_id . '&collection=' . $collection_id . '&lang=' . $lang; ?>"><i class="fa fa-info-circle"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./Collection -->
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- ./Template -->
<!-- Pagination -->
<?php // echo $pages->display_pages(); ?>
<!-- ./Pagination -->
<script type="text/javascript" src="<?php echo MEMORIA_AZUL_PLUGIN_URL . 'template/js/scripts.js'; ?>"></script>
<?php if ( $next ) : ?>
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
