<?php
    global $wp, $services_platform_url;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    $current_slug = add_query_arg( array(), $wp->request );

    $site_language = strtolower(get_bloginfo('language'));
    $lang = substr($site_language,0,2);

    if ( $_COOKIE['e-blueinfo-lang'] ) {
        $lang = $_COOKIE['e-blueinfo-lang'];
    }
?>

<!-- Header -->
<?php get_header('e-blueinfo'); ?>
<?php require_once('header.php'); ?>
<!-- ./Header -->

<!-- Template -->
<section class="row">
    <h4 class="center-align"><?php _e('Login', 'e-blueinfo'); ?></h4>
    <form class="col s10 offset-s1 m6 offset-m3 l4 offset-l4" method="POST" action="<?php echo $services_platform_url.'/client/controller/authentication/origin/'.base64_encode($current_url); ?>">
        <input type="hidden" name="control" value="business" />
        <input type="hidden" name="action" value="authentication" />
        <input type="hidden" name="lang" value="<?php echo $lang; ?>" />
        <div class="row">

            <?php if ( $_REQUEST['status'] == 'userconfirmed' ){ ?>
            <div class="col s12">
                <div class="card-panel green success-text center-align">
                    <span class="white-text">
                        <?php _e('Account successfully created!', 'e-blueinfo'); ?>
                        <br />
                        <?php _e('Your account details were sent by email.', 'e-blueinfo'); ?>
                    </span>
                </div>
            </div>
            <?php } ?>

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

            <?php if ( $_REQUEST['status'] == 'access_denied' ){ ?>
            <div class="input-field col s12">
                <span class="red-text center-align"><?php _e('access denied', 'e-blueinfo'); ?></span>
            </div>
            <?php } ?>
            <?php if ( $_REQUEST['status'] == 'false' ){ ?>
            <div class="input-field col s12">
                <span class="red-text center-align"><?php _e('invalid login', 'e-blueinfo'); ?></span>
            </div>
            <?php } ?>

            <div class="input-field col s12 center-align">
                <button class="btn btn-register waves-effect waves-light blue darken-4 bt100" type="submit" name="action"><?php _e('Login', 'e-blueinfo'); ?></button>
                <a href="https://platserv.bvsalud.org/server/pub/userData.php?c=<?php echo base64_encode($current_url); ?>&theme=e-blueinfo&lang=<?php echo $lang; ?>"><?php _e('Register Yourself', 'e-blueinfo'); ?></a>
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
                <a href="<?php echo $services_platform_url.'/connector/facebook/?origin='.base64_encode($current_url); ?>" class="waves-effect waves-light waves-light btn blue darken-4 bt100"><i class="fab fa-facebook-f"></i> Facebook</a>
            </div>
            <div class="col s6 right-align">
                <a href="<?php echo $services_platform_url.'/connector/google/?origin='.base64_encode($current_url); ?>" class="waves-effect waves-light waves-light btn red darken-2 bt100"><i class="fab fa-google"></i> Google</a>
            </div>
        </div>
    </div>
</section>
<!-- ./Template -->

<!-- Footer -->
<?php get_footer(); ?>
<!-- ./Footer -->
