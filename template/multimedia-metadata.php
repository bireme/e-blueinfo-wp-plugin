<?php
$eblueinfo_service_request = $eblueinfo_service_url . 'api/multimedia/search/?id=multimedia.media.' . $resource_id . '&op=related&lang=' . $lang . '&format=json';

$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    
    $altid = ( $resource->alternate_ids ) ? $resource->alternate_ids[0] : $docid;
    $title = $resource->title;
    $authors = implode('; ', $resource->authors) . '.';
    $contributors = implode('; ', $resource->contributors) . '.';
    $descriptors = (array)$resource->descriptor;
    $keywords = (array)$resource->keyword;
}

$media_type = 'thumb';
$query = 'id:' . $docid;
$eblueinfo_service_request = $pdf_service_url . '&q=' . urlencode($query) . '&lang=' . $lang;
$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);

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

    $media_type = $response_json->response->docs[0]->mt;
}

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
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Document','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
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
    <?php if ( $community_id && $collection_id ) : ?>
    <div class="title3 light-blue-text text-darken-1"><?php echo $com_name; ?></div>
    <div class="title3 title4"><?php echo $col_name; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col s12">
            <article class="doc-detail">
                <?php if ($resource->link) : ?>
                    <?php $output = display_multimedia($resource->link[0], $docid, $media_type); ?>
                    <?php if ($output['service']) : ?>
                        <div class="row">
                            <div class="col s12 m8 offset-m2 l8 offset-l2" data-aos="fade-left">
                                <?php if ($resource->link): ?>
                                    <?php echo $output['html']; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m12 l12 multimedia-icon-actions">
                                <div class="iconActions btn-favorites" data-aos="fade-right" data-aos-delay="300" data-author="<?php echo $authors; ?>" data-altid="<?php echo $altid; ?>" data-docid="<?php echo $docid; ?>"><a class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Favorites', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Favorites','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">star</i></a></div>
                                <?php if ( isset($resource->link) ) : ?>
                                <div class="iconActions" data-aos="fade-right" data-aos-delay="400"><a id="btShare" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Share', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Share','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">share</i></a></div>
                                <div class="iconActions" data-aos="fade-right" data-aos-delay="500"><a href="<?php echo $resource->link[0]; ?>" data-docid="<?php echo $docid; ?>" class="btn-ajax btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="row">
                            <div class="col s6 m4 l3 text-center" data-aos="fade-left">
                                <?php if ($resource->link): ?>
                                    <?php echo $output['html']; ?>
                                <?php endif; ?>
                            </div>
                            <div class="col s6 m8 l9 right-align">
                                <div class="iconActions btn-favorites" data-aos="fade-right" data-aos-delay="300" data-author="<?php echo $authors; ?>" data-altid="<?php echo $altid; ?>" data-docid="<?php echo $docid; ?>"><a class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Favorites', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Favorites','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">star</i></a></div>
                                <?php if ( isset($resource->link) ) : ?>
                                <div class="iconActions" data-aos="fade-right" data-aos-delay="400"><a id="btShare" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Share', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Share','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">share</i></a></div>
                                <div class="iconActions" data-aos="fade-right" data-aos-delay="500"><a href="<?php echo $resource->link[0]; ?>" data-docid="<?php echo $docid; ?>" class="btn-ajax btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="divider"></div>
                <div data-aos="fade-right">
                    <h5 class="titleDefault"><?php _e('Title', 'e-blueinfo'); ?></h5>
                    <p class="doc-title"><?php echo $title; ?></p>

                    <h5 class="titleDefault"><?php _e('Link', 'e-blueinfo'); ?></h5>
                    <p><a href="<?php echo $resource->link[0]; ?>"><?php echo $resource->link[0]; ?></a></p>

                    <?php if ( !$community_id || !$collection_id ) : ?>
                    <h5 class="titleDefault"><?php _e('Contents', 'e-blueinfo'); ?></h5>
                    <p><?php echo $com_name; ?></p>
                    <?php endif; ?>

                    <?php if ( !$community_id || !$collection_id ) : ?>
                    <h5 class="titleDefault"><?php _e('Collections', 'e-blueinfo'); ?></h5>
                    <p><?php echo $col_name; ?></p>
                    <?php endif; ?>

                    <?php if ($resource->media_collection): ?>
                    <h5 class="titleDefault"><?php _e('Collection', 'e-blueinfo'); ?></h5>
                    <p><?php echo $resource->media_collection; ?></p>
                    <?php endif; ?>

                    <h5 class="titleDefault"><?php _e('Description', 'e-blueinfo'); ?></h5>
                    <p><?php echo $resource->description[0]; ?></p>

                    <?php if ($resource->authors): ?>
                    <h5 class="titleDefault"><?php _e('Author(s)', 'e-blueinfo'); ?></h5>
                    <p><?php echo $authors; ?></p>
                    <?php endif; ?>

                    <?php if ($resource->contributors): ?>
                    <h5 class="titleDefault"><?php _e('Contributor(s)', 'e-blueinfo'); ?></h5>
                    <p><?php echo $contributors; ?></p>
                    <?php endif; ?>

                    <?php if ($resource->objective): ?>
                    <h5 class="titleDefault"><?php _e('Objective', 'e-blueinfo'); ?></h5>
                    <p><?php echo $resource->objective; ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->language_display ) : ?>
                    <h5 class="titleDefault"><?php _e('Language','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->language_display, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ($resource->item_extension): ?>
                    <h5 class="titleDefault"><?php _e('Duration', 'e-blueinfo'); ?></h5>
                    <p><?php echo $resource->item_extension[0]; ?></p>
                    <?php endif; ?>

                    <?php if ($resource->publisher): ?>
                    <h5 class="titleDefault"><?php _e('Publisher', 'e-blueinfo'); ?></h5>
                    <p><?php echo $resource->publisher[0]; ?></p>
                    <?php endif; ?>

                    <?php if ($resource->descriptor || $resource->keyword ) : ?>
                    <h5 class="titleDefault"><?php _e('Subject(s)', 'e-blueinfo'); ?></h5>
                    <p><?php echo implode(", ", array_merge( $descriptors, $keywords) ); ?></p>
                    <?php endif; ?>
                </div>
            </article>
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
                <li class="collection-item"><a href="<?php echo $similar['url']; ?>" target="_blank" onclick="__gaTracker('send','event','Document','Similar','<?php echo $countries[$country].'|'.$similar['title']; ?>');"><?php echo $similar['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
<?php else : ?>
<section class="container containerAos">
    <div class="row" data-aos="fade-right">
        <div class="col s12">
            <h2 class="title2 grey lighten-3"><?php _e('Similar', 'e-blueinfo'); ?></h2>
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

<!-- AddThis -->
<div id="bvsFrameBoxShare">
    <div id="bvsFrameBoxContent">
        <p class="share-label center-align"><b><?php _e('Share','e-blueinfo'); ?></b></p>
        <script type="text/javascript">
          var addthis_config = addthis_config||{};
          var addthis_share = addthis_share||{};
              addthis_share.title = "<?php echo $title; ?>";
              addthis_share.url = "<?php echo $resource->link[0]; ?>";
        </script>
        <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="<?php echo $resource->link[0]; ?>">
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