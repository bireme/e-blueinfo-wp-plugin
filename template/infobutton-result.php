<?php
/*
Template Name: e-BlueInfo InfoButton Result
*/

global $infobutton_service_url;

$params = http_build_query($_GET);
$query = str_replace('_', '.', $params);

$infobutton_service_request = $infobutton_service_url . '/infobutton/search?' . $query . '&knowledgeResponseType=application/json';

$response = @file_get_contents($infobutton_service_request);
if ($response){
    $response_json = json_decode($response);
    $docs = $response_json->feed->entry;
    $total = count($docs);
    // echo "<pre>"; print_r($response_json); echo "</pre>";
}

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
<?php if ( isset($total) && strval($total) == 0 ) : ?>
<h1 class="title"><?php _e('Contextualized Search', 'e-blueinfo'); ?></h1>
<section class="container containerAos">
    <div class="row">
        <div class="card-panel center-align">
            <span class="blue-text text-darken-2"><?php _e('No results found','e-blueinfo'); ?></span>
        </div>
    </div>
</section>
<?php else : ?>
<h1 class="title"><?php _e('Documents found in VHL', 'e-blueinfo'); ?>: <?php echo $total; ?></h1>
<section id="categories" class="container">
    <ul class="collection">
        <?php foreach ( $docs as $index => $doc ) : $index++; ?>
        <li class="collection-item"><?php echo $index; ?> - <a href="<?php echo $doc->link->href; ?>" target="_blank"><?php echo $doc->title->_value; ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
<!-- ./Template -->
<!-- 
<script type="text/javascript">
    (function($) { 
        $(function () {
            var mostrados = 10;
            var filhos = $('.collection').children();
            var footer_height = $('#footer').outerHeight();

            for (var i=0; i<filhos.length; i++) {
                if (i < mostrados) filhos.eq(i).show();
                else filhos.eq(i).hide();
            }

            $(window).scroll(function() {
                if($(window).scrollTop() >= $(document).height() - $(window).height() - footer_height) {
                    mostrados += 10;
                    var filhos = $('.collection').children();
                    for (var i=0; i<mostrados; i++)
                        filhos.eq(i).show();
                }
            });
        });
    })(jQuery);
</script>
 -->
<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
