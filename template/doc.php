<?php
/*
Template Name: Memória Azul Detail
*/

global $wp, $memoria_azul_service_url, $memoria_azul_plugin_slug, $memoria_azul_plugin_title, $similar_docs_url, $thumb_service_url;

$current_slug = add_query_arg( array(), $wp->request );
$current_url = home_url(add_query_arg(array(),$wp->request));

$memoria_azul_config         = get_option('memoria_azul_config');
$memoria_azul_initial_filter = $memoria_azul_config['initial_filter'];
$memoria_azul_addthis_id     = $memoria_azul_config['addthis_profile_id'];
$alternative_links     = (bool)$memoria_azul_config['alternative_links'];

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
if ($memoria_azul_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $memoria_azul_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $memoria_azul_initial_filter;
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

$memoria_azul_service_request = $memoria_azul_service_url . 'api/bibliographic/?id=' . $resource_id . '&lang=' . $lang;

//print $memoria_azul_service_request;

$response = @file_get_contents($memoria_azul_service_request);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $doc = $response_json->objects;

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

$community_request = $memoria_azul_service_url . 'api/community/?community=' . $community_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($community_request);
if ($response){
    $community = json_decode($response);
    // echo "<pre>"; print_r($community); echo "</pre>"; die();
}

$collection_request = $memoria_azul_service_url . 'api/collection/?collection=' . $collection_id . '&format=' . $format . '&lang=' . $lang;

$response = @file_get_contents($collection_request);
if ($response){
    $collection = json_decode($response);
    // echo "<pre>"; print_r($collection); echo "</pre>"; die();
}

// $feed_url = real_site_url($memoria_azul_plugin_slug) . 'memoria-azul-feed?q=' . urlencode($query) . '&filter=' . urlencode($filter);

$home_url = isset($memoria_azul_config['home_url_' . $lang]) ? $memoria_azul_config['home_url_' . $lang] : real_site_url();
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
    <?php if ($query == '' && $filter == ''): ?>
    <li class="active"><?php echo $doc[0]->reference_title; ?></li>
    <?php else: ?>
    <li class="active"><?php _e('Search result', 'memoria-azul'); ?></li>
    <?php endif; ?>
</ol>
<!-- ./Breadcrumb -->

<!-- Template -->
<section id="memoria-azul" class="pb-5 doc-data">
    <div class="container">
        <!-- Search Bar -->
        <header class="page-header">
            <div class="searchBarMain">
        		<i class="material-icons searchBarSearchIcon noUserSelect" onclick="__gaTracker('send','event','Browse','Search',document.getElementById('searchBarInput').value);">search</i>
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($memoria_azul_plugin_slug); ?>search">
                    <input type="hidden" name="community" id="community" value="<?php echo $community_id; ?>">
                    <input type="hidden" name="collection" id="collection" value="<?php echo $collection_id; ?>">
        		    <input type="text" name="q" value="" id="searchBarInput" placeholder="<?php _e('Search...', 'memoria-azul'); ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                </form>
        		<i class="material-icons clearSearchBarField noUserSelect" onClick="resetInput()">clear</i>
        	</div>
        </header>
        <!-- <h3 class="section-title">Documentos</h3> -->
        <div class="row">
            <!-- Collection -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="mainflip">
                    <div class="card">
                        <div class="card-body">
                            <!-- <p><img class="img-fluid" src="http://dab.saude.gov.br/imgs/portaldab/biblioteca/publicacoes/praticas_reabilitacao_ab.png" alt="card image"></p> -->
                            <?php if ( isset($doc[0]->electronic_address[0]->_u) ) : ?>
                            <p class="thumb"><img class="img-fluid" src="<?php echo $thumb_service_url . '?id=' . $doc[0]->id . '&url=' . $doc[0]->electronic_address[0]->_u; ?>" alt="card image"></p>
                            <?php else : ?>
                            <p class="thumb"><img class="img-fluid" src="http://placehold.it/270x350" alt="card image"></p>
                            <?php endif; ?>
                            <!-- <a class="redirect" href="/wordpress/memoria-azul/memoria-azul-doc" onclick="return false;"><h4 class="card-title">Título do Documento</h4></a> -->
                            <div class="meta">
                                <div>
                                    <span><?php _e('Title', 'memoria-azul'); ?></span>
                                    <p class="card-text"><?php echo $doc[0]->reference_title; ?></p>
                                    <span><?php _e('Author', 'memoria-azul'); ?></span>
                                    <p class="card-text"><?php echo $author[0]->text; ?></p>
                                    <span><?php _e('Year', 'memoria-azul'); ?></span>
                                    <p class="card-text"><?php echo substr($doc[0]->publication_date_normalized, 0, 4); ?></p>
                                    <span><?php _e('Publisher', 'memoria-azul'); ?></span>
                                    <p class="card-text"><?php echo $doc[0]->publisher; ?></p>
                                </div>
                                <div>
                                    <?php if ( isset($doc[0]->electronic_address[0]->_u) ) : ?>
                                    <span><?php _e('Document Access', 'memoria-azul'); ?></span>
                                    <p class="card-text"><a href="<?php echo $doc[0]->electronic_address[0]->_u; ?>" onclick="__gaTracker('send','event','My Document','Full Text','<?php echo $doc[0]->electronic_address[0]->_u; ?>');"><?php _e('Download link', 'memoria-azul'); ?></a></p>
                                    <?php endif; ?>
                                    <span><?php _e('Abstract', 'memoria-azul'); ?></span>
                                    <p class="card-text"><?php echo $abstract; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ./Collection -->
        </div>
    </div>
</section>
<!-- ./Template -->
<script type="text/javascript" src="<?php echo MEMORIA_AZUL_PLUGIN_URL . 'template/js/scripts.js' ?>"></script>
<?php get_footer(); ?>
