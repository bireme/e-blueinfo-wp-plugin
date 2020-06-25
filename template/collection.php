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
    $eblueinfo_service_request = $eblueinfo_service_url . 'api/collection/?community=' . $community_id . '&format=' . $format . '&lang=' . $lang;
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
    $community_name = $response_json->objects{0}->parent;
    $total = $response_json->meta->total_count;
    $start = $response_json->meta->offset;
    $next  = $response_json->meta->next;
    $collection_list = $response_json->objects;
    usort($collection_list, "cmp");
    $collection_types = array_unique(array_column($collection_list, 'type'));
    // Categories
    $categories = array_filter($collection_list, function() { 
        return $collection_list->type == 0; }
    );
    // Themes
    $themes = array_filter($collection_list, function() { 
        return $collection_list->type == 1; }
    );
}

$eblueinfo_service_request = $eblueinfo_service_url . 'api/community/?country=' . $_COOKIE['e-blueinfo-country'] . '&format=json';
$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    $community_list = $response_json->objects;
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

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<section class="container">
    <div class="row">
        <?php require_once('menu.php'); ?>
        <div class="col s10 m11">
            <nav>
                <div class="nav-wrapper">
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Collection','Search',document.getElementById('searchBarInput').value);">
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
<h1 class="title"><?php echo $community_name; ?></h1>

<?php if ( $collection_types && count($collection_types) == 2 ) : ?>
<section class="center-align">
    Display by:
    <label>
        <input id="radioCategories" class="with-gap" name="exibir" type="radio" value="Categorias" checked />
        <span><?php echo strtoupper(__('Categories','e-blueinfo')); ?></span>
    </label>
    <label>
        <input id="radioThemes" class="with-gap" name="exibir" type="radio" value="Temas" />
        <span><?php echo strtoupper(__('Themes','e-blueinfo')); ?></span>
    </label>
</section>
<?php endif; ?>
<br />

<?php if ( isset($total) && strval($total) == 0 ) : ?>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No results found','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php endif; ?>

<!--------------------- CATEGORIES -------------------------->
<?php if ( $categories ) : ?>
<section id="categories" class="container containerAos">
    <div class="row">
        <?php foreach ( $categories as $index => $collection) : $index++; ?>
        <article class="col s12">
            <div class="card cardSingle">
                <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>browse/?community=<?php echo $community_id; ?>&collection=<?php echo $collection->id; ?>" onclick="__gaTracker('send','event','Collection','View','<?php echo $collection->name; ?>');">
                    <div class="card-content">
                        <b><?php echo $collection->name; ?></b> <br />
                        <p><small><?php echo $collection->description; ?></small></p>
                        <?php if ( is_timestamp($collection->updated_time) ) : ?>
                            <small><?php _e('Last Update','e-blueinfo'); ?>: <?php echo $collection->updated_time; ?></small>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!--------------------- THEMES -------------------------->
<?php if ( $themes ) : ?>
<section id="themes" class="container containerAos" style="display: none">
    <div class="row">
        <?php foreach ( $themes as $index => $collection) : $index++; ?>
        <article class="col s12">
            <div class="card cardSingle">
                <a href="<?php echo real_site_url($eblueinfo_plugin_slug); ?>browse/?community=<?php echo $community_id; ?>&collection=<?php echo $collection->id; ?>" onclick="__gaTracker('send','event','Collection','View','<?php echo $collection->name; ?>');">
                    <div class="card-content">
                        <b><?php echo $collection->name; ?></b> <br />
                        <p><small><?php echo $collection->description; ?></small></p>
                        <?php if ( is_timestamp($collection->updated_time) ) : ?>
                            <small><?php _e('Last Update','e-blueinfo'); ?>: <?php echo $collection->updated_time; ?></small>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<!-- ./Template -->

<?php if ( $next ) : ?>
<!-- Load More -->
<div class="load-more col s3">
    <a href="#" id="loadMore" onclick="return false;"><span class="text"><?php _e('Load More', 'e-blueinfo') ?></span></a>
    <span class="loadmore-last"><?php _e('No more collections', 'e-blueinfo'); ?></span>
</div>
<script type="text/javascript">
    (function($) { 
        $(function () {
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
        });
    })(jQuery);
</script>
<!-- ./Load More -->
<?php endif; ?>

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
