<?php
/*
Template Name: e-BlueInfo Detail
*/

global $wp, $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_plugin_title, $similar_docs_url, $thumb_service_url, $pdf_service_url;

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

// set country
$country = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : '';

$community_id  = ( !empty($_GET['community']) ? $_GET['community'] : NULL );
$collection_id = ( !empty($_GET['collection']) ? $_GET['collection'] : NULL );
$count  = ( !empty($_GET['count']) ? $_GET['count'] : 6 );
$format = ( !empty($_GET['format']) ? $_GET['format'] : 'json' );

$explode = explode('/', $current_slug);
$docid = $explode[count($explode)-1];
$explode = explode('-', $docid);
$resource_id = $explode[count($explode)-1];
$resource_prefix = $explode[0];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

if ( $_COOKIE['e-blueinfo-lang'] ) {
    $lang = $_COOKIE['e-blueinfo-lang'];
}

if ( 'leisref' == $resource_prefix ) {
    require_once('leisref-metadata.php');
} else {
    require_once('biblio-metadata.php');
}
?>
