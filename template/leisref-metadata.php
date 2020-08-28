<?php
$eblueinfo_service_request = $eblueinfo_service_url . 'api/leisref/search/?id=leisref.act.' . $resource_id . '&op=related&lang=' . $lang . '&format=json';

$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);
    $resource = $response_json->diaServerResponse[0]->match->docs[0];

    $title = '';
    if ( $resource->title ) {
        $title = $resource->title;
    } else {
        $act_type = print_lang_value($resource->act_type, $lang, false);
        $title = $act_type . ' Nº ' . $resource->act_number[0];
        
        if ( $resource->issue_date[0] ) {
            $title .= ' - ' . format_act_date($resource->issue_date[0], $lang);
        }
    }

    $scope = '';
    if ( $resource->scope ) {
        $scope = print_lang_value($resource->scope, $lang, false);

        if ( $resource->scope_city ) {
            $scope .= ' - ' . print_lang_value($resource->scope_city, $lang, false);
        }

        if ( $resource->scope_state ) {
            $scope .= ' - ' . print_lang_value($resource->scope_state, $lang, false);
        }
    }
}

$media_type = 'thumb';
$query = 'id:' . $docid;
$eblueinfo_service_request = $pdf_service_url . '&q=' . urlencode($query) . '&lang=' . $lang;
$response = @file_get_contents($eblueinfo_service_request);
if ($response){
    $response_json = json_decode($response);

    $community_name = $response_json->response->docs[0]->com[0];
    $community_name = explode(' ', $community_name, 2);
    $com_name = $community_name[1];
    $com_id = explode('|', $community_name[0])[0];

    $collection_name = $response_json->response->docs[0]->col[0];
    $collection_name = explode(' ', $collection_name, 2);
    $col_name = $collection_name[1];
    $col_id = explode('|', $collection_name[0])[0];

    $media_type = $response_json->response->docs[0]->mt;
}

$home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<section class="container">
    <div class="title3 light-blue-text text-darken-1"><?php echo $com_name; ?></div>
    <div class="title3 title4"><?php echo $col_name; ?></div>
    <div class="row">
        <?php require_once('menu.php'); ?>
        <div class="col s10 m11">
            <nav>
                <div class="nav-wrapper">
                    <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>search" onsubmit="__gaTracker('send','event','Document','Search','<?php echo $countries[$country]; ?>|'+document.getElementById('searchBarInput').value);">
                        <div class="input-field">
                            <input type="hidden" name="community" id="community" value="<?php echo $com_id; ?>">
                            <input type="hidden" name="collection" id="collection" value="<?php echo $col_id; ?>">
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
            <article class="doc-detail">
                <div class="row">
                    <div class="col s6 m4 l3 text-center" data-aos="fade-left">
                        <img class="thumbnail-doc responsive-img" src="<?php echo get_thumbnail($docid, $media_type); ?>" alt="">
                    </div>
                    <div class="col s6 m8 l9 right-align">
                        <?php if ( $resource->fulltext ) : ?>
                        <div class="iconActions" data-aos="fade-right" data-aos-delay="400"><a id="btShare" class="btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('Share', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Share','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">share</i></a></div>
                        <div class="iconActions" data-aos="fade-right" data-aos-delay="500"><a href="<?php echo get_leisref_fulltext($resource, $lang); ?>" data-docid="<?php echo $docid; ?>" class="btn-ajax btn-floating waves-effect waves-light blue lightn-3 btn-small" title="<?php _e('View Document', 'e-blueinfo'); ?>" onclick="__gaTracker('send','event','Document','Full Text','<?php echo $countries[$country].'|'.$title; ?>');"><i class="material-icons">visibility</i></a></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="divider"></div>
                <div data-aos="fade-right">
                    <h5 class="titleDefault doc-title"><?php echo $title; ?></h5>
                    <?php if ($resource->official_ementa): ?>
                        <p><?php echo $resource->official_ementa[0];?></p>
                    <?php elseif ($resource->unofficial_ementa): ?>
                        <p><?php echo $resource->unofficial_ementa[0];?></p>
                    <?php endif; ?>

                    <?php if ( $resource->scope_region ) : ?>
                    <h5 class="titleDefault"><?php _e('Act Country/Region','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->scope_region, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->organ_issuer ) : ?>
                    <h5 class="titleDefault"><?php _e('Organ Issuer','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->organ_issuer, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->scope ) : ?>
                    <h5 class="titleDefault"><?php _e('Act Scope','e-blueinfo') ?></h5>
                    <p><?php echo $scope; ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->source_name ) : ?>
                    <h5 class="titleDefault"><?php _e('Source','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->source_name, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->language ) : ?>
                    <h5 class="titleDefault"><?php _e('Language','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->language, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->collection ) : ?>
                    <h5 class="titleDefault"><?php _e('Collection','e-blueinfo'); ?></h5>
                    <p><?php print_lang_value($resource->collection, $lang); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->publication_date ) : ?>
                    <h5 class="titleDefault"><?php _e('Publication Date','e-blueinfo'); ?></h5>
                    <p><?php echo format_date($resource->publication_date); ?></p>
                    <?php endif; ?>

                    <?php if ( $resource->descriptor ) : ?>
                    <h5 class="titleDefault"><?php _e('Descriptors','e-blueinfo'); ?></h5>
                    <p><?php echo implode(', ', $resource->descriptor); ?></p>
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
              addthis_share.url = "<?php echo get_leisref_fulltext($resource, $lang); ?>";
        </script>
        <div class="addthis_toolbox addthis_default_style addthis_32x32_style" addthis:url="<?php echo get_leisref_fulltext($resource, $lang); ?>">
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