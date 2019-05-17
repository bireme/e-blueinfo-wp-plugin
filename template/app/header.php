<?php
    $eblueinfo_config = get_option('eblueinfo_config');

    if ( function_exists( 'pll_the_languages' ) ) {
        $current_lang = pll_current_language();
        $eblueinfo_plugin_title = $eblueinfo_config['plugin_title_' . $current_lang];
    }else{
        $eblueinfo_plugin_title = $eblueinfo_config['plugin_title'];
    }

    $pos = explode('/', $wp->request);

    if ( 'country' == end($pos) ) {
        $title = $eblueinfo_plugin_title . " - " . __('Choose country', 'e-blueinfo') . " | " . get_bloginfo('name');
    } else {
        $title = $eblueinfo_plugin_title . " | " . get_bloginfo('name');
    }
?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- === Page Title === -->
	<title><?php echo $title; ?></title>

	<!-- === Embedding Stylesheets === -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
		<?php require_once('style.css'); ?>
    </style>
    <?php if ( strpos($_SERVER['HTTP_USER_AGENT'], 'gonative') !== false || ( strpos($wp->request, 'country') !== false && 'country' == end($pos) ) ) : ?>
    <style type="text/css">
		<?php require_once('style-app.css'); ?>
    </style>
    <?php endif; ?>

	<!-- === Embedding Scripts === -->
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/app/app.js'; ?>"></script>

</head>
<body>
