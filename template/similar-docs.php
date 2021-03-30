<?php

	$country = ( $_COOKIE['e-blueinfo-country'] ) ? $_COOKIE['e-blueinfo-country'] : '';

    $eblueinfo_config = get_option('eblueinfo_config');
    $countries = $eblueinfo_config['country_data'];
	
	$title = $_POST['title'];
	$lang = $_POST['lang'];

	$similar_docs = Similar::getSimilarDocs($title, $lang);

?>

<?php if ( $similar_docs ) : ?>
<div class="col s12">
    <ul class="collection">
        <?php foreach ($similar_docs as $similar) : ?>
        <li class="collection-item"><a href="<?php echo $similar['url']; ?>" target="_blank" onclick="__gaTracker('send','event','Document','Similar','<?php echo $countries[$country].'|'.$similar['title']; ?>');"><?php echo $similar['title']; ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else : ?>
<div class="col s12">
    <div class="card-panel center-align">
        <span class="blue-text text-darken-2"><?php _e('No similar found','e-blueinfo'); ?></span>
    </div>
</div>
<?php endif; ?>