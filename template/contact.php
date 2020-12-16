<?php
/*
Template Name: e-BlueInfo Contact Form
*/

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

if ( $_COOKIE['e-blueinfo-lang'] ) {
    $lang = $_COOKIE['e-blueinfo-lang'];
}

$contact_lang = ( 'pt' == $lang ) ? 'pt-br' : $lang;

?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<section class="container">
    <div class="row">
        <?php require_once('menu.php'); ?>
    </div>
</section>
<!-- ./Header -->

<!-- Template -->
<h1 class="title"><?php _e('Contact Form', 'e-blueinfo'); ?></h1>
<section class="container containerAos">
    <div class="row">
        <iframe id="contact-form" width="100%" height="645px" src="//contacto.bvsalud.org/chat.php?group=e-blueinfo&ptl=<?php echo $contact_lang; ?>&hg=Pw__&hcgs=MQ__&htgs=MQ__&hinv=MQ__&hfk=MQ__" frameborder="0" scrolling="no"></iframe>        
    </div>
</section>
<!-- ./Template -->

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
