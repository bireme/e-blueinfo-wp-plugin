<?php
/*
Template Name: e-BlueInfo Detail
*/

global $wp, $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $similar_docs_url, $thumb_service_url;

$current_slug = add_query_arg( array(), $wp->request );
$current_url = home_url(add_query_arg(array(),$wp->request));

$eblueinfo_config         = get_option('eblueinfo_config');
$eblueinfo_initial_filter = $eblueinfo_config['initial_filter'];
$eblueinfo_addthis_id     = $eblueinfo_config['addthis_profile_id'];
$alternative_links     = (bool)$eblueinfo_config['alternative_links'];

/*
$referer = wp_get_referer();
$path = parse_url($referer);
if ( array_key_exists( 'query', $path ) ) {
    $path = parse_str($path['query'], $output);
    if ( array_key_exists( 'q', $output ) && !empty( $output['q'] ) ) {
        $query = $output['q'];
        $q = ( strlen($output['q']) > 10 ? substr($output['q'],0,10) . '...' : $output['q'] );
        $ref = ' / <a href="'. $referer . '">' . $q . '</a>';
    }
}
*/
$filter = '';
$user_filter = stripslashes($output['filter']);
if ($eblueinfo_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $eblueinfo_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $eblueinfo_initial_filter;
    }
}else{
    $filter = $user_filter;
}

$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : NULL );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : NULL );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 6 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );

$explode = explode('/', $current_slug);
$resource_id = end($explode);

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$eblueinfo_service_request = $eblueinfo_service_url . 'api/bibliographic/?id=' . $resource_id . '&lang=' . $lang;

//print $eblueinfo_service_request;

$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $doc = $response_json->objects;

    $ref_title = explode('|', $doc[0]->reference_title);
    $title = ( count($ref_title) > 1 ) ? $ref_title[1] : $ref_title[0];

    $author = '-';
    if ( isset($doc[0]->individual_author_monographic) ) {
        $author = $doc[0]->individual_author_monographic;
    } elseif ( isset($doc[0]->corporate_author_monographic) ) {
        $author = $doc[0]->corporate_author_monographic;
    } elseif ( isset($doc[0]->individual_author_collection) ) {
        $author = $doc[0]->individual_author_collection;
    } elseif ( isset($doc[0]->corporate_author_collection) ) {
        $author = $doc[0]->corporate_author_collection;
    } elseif ( isset($doc[0]->individual_author) ) {
        $author = $doc[0]->individual_author;
    } elseif ( isset($doc[0]->corporate_author) ) {
        $author = $doc[0]->corporate_author;
    }

    $abstract  = '-';
    if ( !empty($doc[0]->abstract) ) {
        $abstracts = wp_list_pluck( $doc[0]->abstract, 'text', '_i' );
        if ( !empty($abstracts) ) {
            if ( array_key_exists($lang, $abstracts) ) {
                $abstract = $abstracts[$lang];
            } else {
                $abstract = $doc[0]->abstract[0]->text;
            }
        }
    }
/*
    // find similar documents
    $similar_docs_url = $similar_docs_url . '?adhocSimilarDocs=' . urlencode($doc[0]->reference_title);
    // get similar docs
    $similar_docs_xml = @file_get_contents($similar_docs_url);
    // transform to php array
    $xml = simplexml_load_string($similar_docs_xml,'SimpleXMLElement',LIBXML_NOCDATA);
    $json = json_encode($xml);
    $similar_docs = json_decode($json, TRUE);
*/
}

$collection_request = $eblueinfo_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    $community_name = $collection->objects{0}->parent;
    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
}

// $feed_url = real_site_url($eblueinfo_plugin_slug) . 'e-blueinfo-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);

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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Document','Search',document.getElementById('searchBarInput').value);">
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
<section class="container containerAos">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <div class="col s6 m4 l3 text-center" data-aos="fade-left">
                    <img class="thumbnail-doc" src="<?php echo $thumb_service_url . '/' . $doc[0]->id . '/' . $doc[0]->id . '.jpg'; ?>" class="responsive-img" alt="" onerror="this.src='http://thumbs.bireme.org/nothumb.jpg'">
                </div>
                <div class="col s6 m8 l9 right-align">
                    <div class="iconActions" data-aos="fade-right" data-aos-delay="300"><a href="#modal" class="btn-floating waves-effect waves-light blue lightn-3 btn-small modal-trigger" title="<?php _e('Favorites', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Favorites','<?php echo real_site_url($eblueinfo_plugin_slug) . 'doc/' . $doc[0]->id; ?>');"><i class="material-icons">star</i></a></div>
                    <?php if ( isset($doc[0]->electronic_address[0]->_u) ) : ?>
                    <div class="iconActions" data-aos="fade-right" data-aos-delay="400"><a id="btShare" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Share', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Share','<?php echo $doc[0]->electronic_address[0]->_u; ?>');"><i class="material-icons">share</i></a></div>
                    <div class="iconActions" data-aos="fade-right" data-aos-delay="500"><a href="<?php echo $doc[0]->electronic_address[0]->_u; ?>" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Full Text','<?php echo $doc[0]->electronic_address[0]->_u; ?>');"><i class="material-icons">visibility</i></a></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="divider"></div>
            <div data-aos="fade-right">
                <h5 class="titleDefault"><?php _e('Title', 'e-blueinfo'); ?></h5>
                <p><?php echo $title; ?></p>

                <h5 class="titleDefault"><?php _e('Author', 'e-blueinfo'); ?></h5>
                <p><?php echo $author[0]->text; ?></p>

                <h5 class="titleDefault"><?php _e('Year', 'e-blueinfo'); ?></h5>
                <p><?php echo substr($doc[0]->publication_date_normalized, 0, 4); ?></p>

                <h5 class="titleDefault"><?php _e('Publisher', 'e-blueinfo'); ?></h5>
                <p><?php echo $doc[0]->publisher; ?></p>

                <h5 class="titleDefault"><?php _e('Abstract', 'e-blueinfo'); ?></h5>
                <p><?php echo $abstract; ?></p>
            </div>
        </div>
    </div>
</section>

<?php $similar_docs = Similar::getSimilarDocs($title, $lang); ?>
<?php if ( $similar_docs ) : ?>
<section class="container containerAos">
    <div class="row" data-aos="fade-right">
        <div class="col s12">
            <h2 class="title2 grey lighten-3"><?php _e('Similar', 'e-blueinfo'); ?></h2>
        </div>
        <div class="col s12">
            <ul class="collection">
                <?php foreach ($similar_docs as $similar) : ?>
                <li class="collection-item"><a href="<?php echo $similar['url'] ?>" target="_blank"><?php echo $similar['title']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row" data-aos="fade-right">
        <div class="col s12">
            <h2 class="title2 grey lighten-3">Similars</h2>
        </div>
        <div class="col s12">
            <div class="card-panel center-align">
                <span class="blue-text text-darken-2"><?php _e('No similar found','e-blueinfo'); ?></span>
            </div>
        </div>
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

<!-- AddThis -->
<div id="bvsFrameBoxShare">
    <div id="bvsFrameBoxContent">
        <p class="share-label center-align"><b><?php _e('Share','e-blueinfo'); ?></b></p>
        <script type="text/javascript">
          var addthis_config = addthis_config||{};
          var addthis_share = addthis_share||{};
              addthis_share.title = "<?php echo $title; ?>";
              addthis_share.url = "<?php echo $doc[0]->electronic_address[0]->_u; ?>";
        </script>
        <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="<?php echo $doc[0]->electronic_address[0]->_u; ?>">
            <a class="bvsFrameImg addthis_button_link"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/link.svg'; ?>" width="50" alt=""></a>
                <a class="bvsFrameImg addthis_button_facebook"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/facebook.svg'; ?>" width="50" alt=""></a>
                <a class="bvsFrameImg addthis_button_twitter"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/twitter.svg'; ?>" width="50" alt=""></a>
                <a class="bvsFrameImg addthis_button_whatsapp"><img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/whatsapp.svg'; ?>" width="50" alt=""></a>
                <!--a class="addthis_button_compact"></a-->
        </div>
        <script type="text/javascript" src="https://s7.addthis.com/js/300/addthis_widget.js#async=1"></script>
        <script type="text/javascript">addthis.init();</script>
    </div>
</div>
<script type="text/javascript">
    (function($) { 
        $(function () {
            $("#bvsFrameBoxShare").click(function(){
                $(this).hide(300);
            });
            $("#btShare").click(function(){
                $("#bvsFrameBoxShare").show(300);
            });
        });
    })(jQuery);
</script>

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
