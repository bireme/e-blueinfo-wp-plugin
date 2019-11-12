<?php
    $site_language = strtolower(get_bloginfo('language'));
    $lang = substr($site_language,0,2);

    $text = array(
        'pt' => array(
            'title'  => 'Queremos a sua opinião sobre o app e-BlueInfo',
            'invite' => 'Convidamos-lhe a responder a uma pesquisa que não levará mais que 3 minutos',
            'survey' => 'Ir para a pesquisa'
        ),
        'es' => array(
            'title'  => 'Queremos sus comentarios sobre el app e-BlueInfo',
            'invite' => 'Lo invitamos a completar una encuesta que no tomará más de 3 minutos',
            'survey' => 'Ir a la encuesta'
        ),
        'en' => array(
            'title'  => 'We want your opinion about the e-BlueInfo app',
            'invite' => 'We invite you to complete a survey that will take no more than 3 minutes',
            'survey' => 'Go to survey (in Spanish)'
        ),
    );

    $survey = array(
        'pt' => 'https://docs.google.com/forms/d/e/1FAIpQLSepz3Wg0Ij1nueMWCt1XuS9kRiSWhh_OaQFcEp0mbij9pM6Sg/viewform',
        'es' => 'https://docs.google.com/forms/d/e/1FAIpQLSeKCyRVkYjBqebRZKJQwdzSs23j36BEOnCY5B7dg4FTn9M6Qw/viewform',
        'en' => 'https://docs.google.com/forms/d/e/1FAIpQLSeKCyRVkYjBqebRZKJQwdzSs23j36BEOnCY5B7dg4FTn9M6Qw/viewform'
    );
?>
<div id="feedback">
    <div id="feedbackBox">
        <div id="feedbackFechar" class=""><i class="material-icons md-18">close</i></div>
        <h1><?php echo $text[$lang]['title']; ?></h1>
        <h2><?php echo $text[$lang]['invite']; ?></h2>
        <hr>
        <a href="<?php echo $survey[$lang]; ?>" class="btn btn-primary" target="_blank"><?php echo $text[$lang]['survey']; ?></a>
    </div>
    <div id="feedbackIcone">
        <img src="<?php echo EBLUEINFO_PLUGIN_URL . 'template/images/iconFeedback-' . $lang . '.svg'; ?>" alt="Feedback">
    </div>
    <div class="clear"></div>
</div> 