<?php
    global $services_platform_url;

    if ( ! defined( 'HTTP_HOST' ) ) {
        $path = ( $_SERVER['REDIRECT_URL'] ) ? $_SERVER['REDIRECT_URL'] : '';
        define( 'HTTP_HOST', get_bloginfo('url').$path );
    }

    $site_language = strtolower(get_bloginfo('language'));
    $lang = substr($site_language,0,2);

    $home_url = isset($eblueinfo_config['home_url_' . $lang]) ? $eblueinfo_config['home_url_' . $lang] : real_site_url();
?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<!-- ./Header -->

<!-- Template -->
<section class="row" id="containerCenter">
    <h4 class="center-align"><?php _e('Login', 'e-blueinfo'); ?></h4>
    <form class="col s10 offset-s1 m6 offset-m3 l4 offset-l4" method="POST" action="<?php echo $services_platform_url.'/client/controller/authentication/origin/'.base64_encode(HTTP_HOST); ?>">
        <input type="hidden" name="control" value="business" />
        <input type="hidden" name="action" value="authentication" />
        <input type="hidden" name="lang" value="<?php echo $lang; ?>" />
        <div class="row">
            <div class="input-field col s12">
                <i class="material-icons prefix">account_circle</i>
                <input id="userID" type="text" name="userID">
                <label for="userID"><?php _e('User', 'e-blueinfo'); ?></label>
            </div>
            <div class="input-field col s12">
                <i class="material-icons prefix">lock</i>
                <input id="userPass" type="password" name="userPass">
                <label for="userPass"><?php _e('Password', 'e-blueinfo'); ?></label>
            </div>
            <div class="input-field col s12 center-align">
                <button class="btn waves-effect waves-light blue darken-4 bt100" type="submit" name="action">Login</button>
                <a href="register.php"><?php _e('Register Yourself', 'e-blueinfo'); ?></a>
            </div>
            <div class="input-field col s12">
                <label>
                    <input type="checkbox" class="filled-in" name="remember_me" />
                    <span><?php _e('Remember me', 'e-blueinfo'); ?></span>
                </label>
            </div>
        </div>
    </form>
    <div class="col s10 offset-s1 m6 offset-m3 l4 offset-l4">
        <br>
        <div class="row">
            <div class="col s6">
                <a href="<?php echo $this->servplat_domain.'/connector/facebook/?origin='.base64_encode(HTTP_HOST); ?>" class="waves-effect waves-light waves-light btn blue darken-4 bt100"><i class="fab fa-facebook-f"></i> Facebook</a>
            </div>
            <div class="col s6 right-align">
                <a href="<?php echo $this->servplat_domain.'/connector/google/?origin='.base64_encode(HTTP_HOST); ?>" class="waves-effect waves-light waves-light btn red darken-2 bt100"><i class="fab fa-google"></i> Google</a>
            </div>
        </div>
    </div>
</section>
<!-- ./Template -->

<!-- Footer -->
<?php get_footer(); ?>
<!-- ./Footer -->
