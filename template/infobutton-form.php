<?php
/*
Template Name: e-BlueInfo InfoButton Form
*/

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

if ( $_COOKIE['e-blueinfo-lang'] ) {
    $lang = $_COOKIE['e-blueinfo-lang'];
}

$lang_label = array(
    'pt' => 'Português',
    'es' => 'Español',
    'en' => 'English'
);

// InfoButton Country Code
$cc = '';
$country_code = array(
    "BR" => 'BRA', // Brazil
    "SV" => 'SLV', // El Salvador
    "GT" => 'GTM', // Guatemala
    "PE" => 'PER'  // Peru
);

$response = @file_get_contents($this->country_service_url);
if ($response){
    $response_json = json_decode($response);
    $cc_list = wp_list_pluck( $response_json, 'code', 'id' );
    $cc = $cc_list[$_COOKIE['e-blueinfo-country']];
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
<h1 class="title"><?php _e('Contextualized Search', 'e-blueinfo'); ?></h1>
<section id="categories" class="container">
    <div class="row">
        <form id="infobutton-form" class="col s12" action="<?php echo real_site_url($eblueinfo_plugin_slug); ?>infobutton/result">
            <div class="row">
                <div class="input-field col s12 m6 margin1">
                    <input id="mainSearchCriteria-v-c" type="text" name="mainSearchCriteria.v.c" class="validate" required="" aria-required="true">
                    <label for="mainSearchCriteria-v-c" id="labelCode"><?php _e('Code', 'e-blueinfo'); ?> <span>ICD-10</span> *</label>
                </div>
                <div class="input-field col s12 m6 margin1">
                    <select id="mainSearchCriteria-v-cs" name="mainSearchCriteria.v.cs" onchange="change_code_text(this);">
                        <option value="2.16.840.1.113883.6.3">ICD-10</option>
                        <option value="2.16.840.1.113883.6.177">DeCS/MeSH</option>
                        <option value="2.16.840.1.113883.6.96">SNOMED-CT</option>
                    </select>
                    <label for="mainSearchCriteria-v-cs"><?php _e('Code System', 'e-blueinfo'); ?></label>
                </div>
                <div class="input-field col s12 m6 margin1">
                    <div class="switch">
                        <label>
                            <input type="checkbox" id="moreOptions">
                            <span class="lever"></span>
                            <?php _e('More options', 'e-blueinfo'); ?>
                        </label>
                    </div>
                </div>
                <fieldset class="fieldset col s12 margin1" id="fieldSetOptions">
                    <div class="input-field col s12 m6 margin1">
                        <select id="patientPerson-administrativeGenderCode-c" name="patientPerson.administrativeGenderCode.c">
                            <option value="" disabled selected><?php _e('Select Genre', 'e-blueinfo'); ?></option>
                            <option value="M"><?php _e('Male', 'e-blueinfo'); ?></option>
                            <option value="F"><?php _e('Female', 'e-blueinfo'); ?></option>
                            <option value="UN"><?php _e('Undifferentiated', 'e-blueinfo'); ?></option>
                        </select>
                        <label for="patientPerson-administrativeGenderCode-c"><?php _e('Genre', 'e-blueinfo'); ?></label>
                    </div>
                    <div class="input-field col s12 m6 margin1">
                        <select id="ageGroup-v-c" name="ageGroup.v.c">
                            <option value="" disabled selected><?php _e('Select Age', 'e-blueinfo'); ?></option>
                            <option value="D007231"><?php _e('infant, newborn; birth to 1 month', 'e-blueinfo'); ?></option>
                            <option value="D007223"><?php _e('infant; 1 to 23 months', 'e-blueinfo'); ?></option>
                            <option value="D002675"><?php _e('child, preschool; 2 to 5 years', 'e-blueinfo'); ?></option>
                            <option value="D002648"><?php _e('child; 6 to 12 years', 'e-blueinfo'); ?></option>
                            <option value="D000293"><?php _e('adolescent; 13-18 years', 'e-blueinfo'); ?></option>
                            <option value="D055815"><?php _e('young adult; 19-24 years', 'e-blueinfo'); ?></option>
                            <option value="D000328"><?php _e('adult; 19-44 years', 'e-blueinfo'); ?></option>
                            <option value="D008875"><?php _e('middle aged; 45-64 years', 'e-blueinfo'); ?></option>
                            <option value="D000368"><?php _e('aged; 56-79 years', 'e-blueinfo'); ?></option>
                        </select>
                        <label for="ageGroup-v-c"><?php _e('Age', 'e-blueinfo'); ?></label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="mainSearchCriteria-v-ot" name="mainSearchCriteria.v.ot" class="materialize-textarea"></textarea>
                        <label for="mainSearchCriteria-v-ot"><?php _e('Keywords', 'e-blueinfo'); ?></label>
                    </div>
                    <div class="col s12 m6 margin1">
                        <label>
                            <input id="informationRecipient-languageCode-c" name="informationRecipient.languageCode.c" type="checkbox" value="<?php echo $lang; ?>" />
                            <span><?php _e('Documents only in', 'e-blueinfo'); ?> <?php echo $lang_label[$lang]; ?></span>
                        </label>
                    </div>
                    <?php if ( $cc ) : ?>
                    <div class="col s12 m6 margin1">
                        <label>
                            <input id="locationOfInterest-addr-CNT" name="locationOfInterest.addr.CNT" type="checkbox" value="<?php echo $country_code[$cc]; ?>" />
                            <span><?php _e('Documents from your country', 'e-blueinfo'); ?></span>
                        </label>
                    </div>
                    <?php endif; ?>
                </fieldset>
                <div class="col s12">
                    <br />
                    <button class="btn waves-effect waves-light blue lightn-3" type="submit"><?php _e('Search', 'e-blueinfo'); ?>
                        <i class="material-icons right">search</i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- ./Template -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript">
    (function($) { 
        $(function () {
            $("#infobutton-form").validate({
                rules: {
                    "mainSearchCriteria.v.c": "required",
                },
                messages: {
                    "mainSearchCriteria.v.c": "<?php _e('This field is required', 'e-blueinfo'); ?>",
                },
                errorElement : 'div',
                errorClass: 'invalid error',
                validClass: "valid",
                errorPlacement: function(error, element) {
                    var placement = $(element).data('error');
                    if (placement) {
                        $(placement).append(error)
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    })(jQuery);
</script>

<!-- Footer -->
<?php require_once('footer.php'); ?>
<?php get_footer(); ?>
<!-- ./Footer -->
