<?php
/*
Template Name: e-BlueInfo Collection Page
*/
global $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $eblueinfo_texts;

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

$user_filter = stripslashes($_GET['filter']);
$community_id = ( !empty($_GET['community']) ? $_GET['community'] : '' );
$page   = ( !empty($_GET['page']) ? $_GET['page'] : 1 );
$offset = ( !empty($_GET['offset']) ? $_GET['offset'] : 0 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );
$sort   = ( !empty($_GET['sort']) ? $order[$_GET['sort']] : 'created_date desc' );
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

if ( $query ) {
    $eblueinfo_service_request = $eblueinfo_service_url . 'api/bibliographic/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&count=' . $count . '&sort=' . urlencode($sort) . '&lang=' . $lang;
} else {
    $eblueinfo_service_request = $eblueinfo_service_url . 'api/collection/?community=' . $community_id . '&format=' . $format . '&offset=' . $offset . '&limit=' . $count . '&lang=' . $lang;
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
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $total = $response_json->meta->total_count;
    $start = $response_json->meta->offset;
    $next  = $response_json->meta->next;
    $collection_list = $response_json->objects;
    usort($collection_list, "cmp");
}

$params = $count != 2 ? '&count=' . $count : '';
$params .= !empty($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';

$page_url_params = real_site_url($eblueinfo_plugin_slug) . 'collection/?community=' . $community_id . $params;
// $feed_url = real_site_url($eblueinfo_plugin_slug) . 'e-blueinfo-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);
$home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
/*
$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);
*/
?>

<?php get_header('e-blueinfo');?>

<!-- Breadcrumb -->
<ol class="breadcrumb">
    <li><a href="<?php echo $home_url ?>"><?php _e('Home','e-blueinfo'); ?></a></li>
    <li><a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>"><?php echo $eblueinfo_plugin_title; ?> </a></li>
    <?php if ($query == '' && $filter == ''): ?>
    <li class="active"><?php echo $response_json->objects{0}->parent; ?></li>
    <?php else: ?>
    <li class="active"><?php _e('Search result', 'e-blueinfo'); ?></li>
    <?php endif; ?>
</ol>
<!-- ./Breadcrumb -->

<!-- Template -->
<section id="eblueinfo" class="pb-5 eblueinfo-col">
    <div class="container">
        <!-- Search Bar -->
        <header class="page-header">
            <?php simple_sliding_menu($lang); ?>
            <div class="searchBarMain">
        		<i class="material-icons searchBarSearchIcon noUserSelect" onclick="__gaTracker('send','event','Collection','Search',document.getElementById('searchBarInput').value);">search</i>
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search">
                    <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
        		    <input type="text" name="q" value="<?php echo $query; ?>" id="searchBarInput" placeholder="<?php _e('Search...', 'e-blueinfo'); ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                </form>
        		<i class="material-icons clearSearchBarField noUserSelect" onClick="resetInput()">clear</i>
        	</div>
        </header>
        <h3 class="section-title parent-title"><?php echo $response_json->objects{0}->parent; ?></h3>
        <h3 class="section-title"><?php _e('Collections', 'e-blueinfo'); ?></h3>
        <div class="row">
            <?php if ( isset($total) && strval($total) == 0 ) : ?>
            <h4 class="results"><?php _e('No results found','e-blueinfo'); ?></h4>
            <?php else : ?>
            <div class="h-label col-xs-12 col-sm-12 col-md-12 border-bottom">
                <?php if ( ( $query != '' || $user_filter != '' ) && strval($total) > 0) : ?>
                <h4><?php _e('Results', 'e-blueinfo'); echo ': ' . $total; ?></h4>
                <?php else: ?>
                <h4><?php _e('Total', 'e-blueinfo'); echo ': ' . $total; ?></h4>
                <?php endif; ?>
            </div>
                <?php foreach ( $collection_list as $index => $collection) : $index++; $id = "col".$collection->id; ?>
                <!-- Collection -->
                <div class="col-xs-12 col-sm-6 col-md-4 item">
                    <div id="<?php echo $id; ?>" class="image-flip">
                        <div class="mainflip">
                            <div class="col" onclick="__gaTracker('send','event','Collection','View','<?php echo $collection->name; ?>');">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title"><?php echo $collection->name; ?></h4>
                                        <?php $class = ( $collection->description ) ? 'show' : 'hide'; ?>
                                        <p class="card-text <?php echo $class; ?>"><?php echo $collection->description; ?></p>
                                        <a class="redirect <?php echo $class; ?>" href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>browse/?community=<?php echo $community_id; ?>&collection=<?php echo $collection->id; ?>"><i class="fa fa-angle-right"></i></a>
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
<script type="text/javascript" src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/js/base.js'; ?>"></script>
<?php if ( $next ) : ?>
<!-- Load More -->
<div class="load-more col-xs-3 col-sm-3 col-md-3">
    <a href="#" id="loadMore"><span class="text"><?php _e('Load More', 'e-blueinfo') ?></span></a>
    <p class="totop">
        <a href="#top"><?php _e('back to top', 'e-blueinfo') ?></a>
    </p>
    <span class="loadmore-last"><?php _e('No more collections', 'e-blueinfo'); ?></span>
</div>
<!-- ./Load More -->
<script type="text/javascript">
    $('.row').loadmore('', {
        loadingText : '<?php _e('Loading...', 'e-blueinfo') ?>',
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
<!-- Footer -->
<div class="eblueinfo-footer">
    <img class="img-fluid" src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/bireme_' . $lang . '_banner.png'; ?>" alt="footer image" />
</div>
<!-- ./Footer -->
<?php get_footer(); ?>
