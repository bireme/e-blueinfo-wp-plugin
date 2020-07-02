<?php
/*
Plugin Name: e-BlueInfo
Plugin URI: https://github.com/bireme/e-blueinfo-wp-plugin/
Description: e-BlueInfo WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.1
Author URI: http://reddes.bvsalud.org/
*/

define('EBLUEINFO_VERSION', '0.1' );
define('EBLUEINFO_SYMBOLIC_LINK', false );
define('EBLUEINFO_PLUGIN_DIRNAME', 'e-blueinfo' );
defined('EBLUEINFO_REDIRECT') or define('EBLUEINFO_REDIRECT', false);
defined('EBLUEINFO_FEEDBACK') or define('EBLUEINFO_FEEDBACK', false);

if (EBLUEINFO_SYMBOLIC_LINK == true) {
    define( 'EBLUEINFO_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . EBLUEINFO_PLUGIN_DIRNAME );
} else {
    define( 'EBLUEINFO_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('EBLUEINFO_PLUGIN_DIR',   plugin_basename( EBLUEINFO_PLUGIN_PATH ) );
define('EBLUEINFO_PLUGIN_URL',   plugin_dir_url(__FILE__) );

require_once(EBLUEINFO_PLUGIN_PATH . '/settings.php');
require_once(EBLUEINFO_PLUGIN_PATH . '/template-functions.php');
require_once(EBLUEINFO_PLUGIN_PATH . '/similar.php');

if(!class_exists('EBlueInfo_Plugin')) {
    class EBlueInfo_Plugin {

        private $plugin_slug            = 'e-blueinfo';
        private $service_url            = 'http://fi-admin-api.bvsalud.org/';
        private $similar_docs_url       = 'http://similardocs.bireme.org/SDService';
        private $thumb_service_url      = 'http://thumbs.bireme.org';
        private $country_service_url    = 'http://fi-admin-api.bvsalud.org/api/community/get_country_list/?format=json';
        private $infobutton_service_url = 'http://bvsinfobutton.homolog.bvsalud.org';
        private $services_platform_url  = 'https://platserv2.teste.bvsalud.org';
        private $pdf_service_url        = 'http://basalto01.bireme.br:9292/solr/pdfs/select?fl=id,ti,com,col,ur,tu,fo';
        // private $pdf_service_url     = 'http://basalto01.bireme.br:9292/solr/pdfs/select?hl=on&hl.fl=_text_&hl.fragsize=500&hl.snippets=10&hl.maxAnalyzedChars=800000&fl=id,ti,com,col,ur,tu,fo';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            add_action( 'init', array(&$this, 'load_translation') );
            add_action( 'wp', array(&$this, 'force_cookie_lang') );
            add_action( 'admin_menu', array(&$this, 'admin_menu') );
            add_action( 'plugins_loaded', array(&$this, 'plugin_init') );
            add_action( 'wp_head', array(&$this, 'google_analytics_code') );
            add_action( 'widgets_init', array(&$this, 'register_sidebars') );
            add_action( 'template_redirect', array(&$this, 'theme_redirect') );
            add_action( 'wp_loaded', array(&$this, 'plugin_page_redirect') );
            add_action( 'wp_footer', array(&$this, 'show_feedback_tab') );
            add_action( 'wp_enqueue_scripts', array(&$this, 'template_styles_scripts'), 20 );

            add_filter( 'get_search_form', array(&$this, 'search_form') );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );
            add_filter( 'document_title_parts', array(&$this, 'theme_slug_render_title') );
            add_filter( 'body_class', array(&$this, 'custom_body_classes') );

        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

        function load_translation(){
            global $eblueinfo_texts;

            // force locale
            add_filter( 'locale', array(&$this, 'force_locale') );

            // load internal plugin translations
            load_plugin_textdomain( 'e-blueinfo', false,  EBLUEINFO_PLUGIN_DIR . '/languages' );

            // load plugin translations
            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            $eblueinfo_texts = @parse_ini_file(EBLUEINFO_PLUGIN_PATH . "/languages/texts_" . $lang . ".ini", true);
        }

        function force_locale($locale) {
            if ( defined( 'POLYLANG_VERSION' ) && $_COOKIE['e-blueinfo-lang'] ) {
                $slugs   = pll_languages_list( array( 'fields' => 'slug' ) );
                $locales = pll_languages_list( array( 'fields' => 'locale' ) );
                $langs   = array_combine($slugs, $locales);
                $locale  = $langs[$_COOKIE['e-blueinfo-lang']];
            }

            return $locale;
        }

        function plugin_init() {
            $eblueinfo_config = get_option('eblueinfo_config');

            if ( $eblueinfo_config && $eblueinfo_config['plugin_slug'] != ''){
                $this->plugin_slug = $eblueinfo_config['plugin_slug'];
            }

            // check if request contains plugin slug string
            $pos_slug = strpos($_SERVER['REQUEST_URI'], $this->plugin_slug);

            if ( ! is_admin() && ! $_COOKIE['e-blueinfo'] && $pos_slug !== false ) {
                /* app page redirect */
                add_action( 'template_redirect', array(&$this, 'app_page_redirect'), 1 );
            }
        }

        function plugin_page_redirect() {
            if ( EBLUEINFO_REDIRECT ) {
                $eblueinfo_config = get_option('eblueinfo_config');

                // check if request contains plugin slug string
                $pos_slug = strpos($_SERVER['REQUEST_URI'], $this->plugin_slug);

                if ( ! is_admin() && $pos_slug !== false ) {
                    $redirect = ( !empty($eblueinfo_config['redirect']) ) ? $eblueinfo_config['redirect'] : 'https://e-blueinfo.bvsalud.org/';
                    header('Location: '.$redirect);
                    exit;
                }
            }
        }

        function app_page_redirect() {
            global $eblueinfo_plugin_slug, $country_service_url;
            $eblueinfo_plugin_slug = $this->plugin_slug;
            $country_service_url = $this->country_service_url;

            // generate app cookie
            setCookie( 'e-blueinfo', time(), time() + (10 * 365 * 24 * 60 * 60), '/' );

            $template = EBLUEINFO_PLUGIN_PATH . 'template/home.php';

            // force reload the page on hitting back button
            header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
            header('Pragma: no-cache'); // HTTP 1.0.
            header('Expires: 0'); // Proxies.

            // force status to 200 - OK
            status_header(200);

            // redirect to page and finish execution
            include($template);
            die();
        }

        function admin_menu() {

            add_submenu_page( 'options-general.php', __('e-BlueInfo Settings', 'e-blueinfo'), __('e-BlueInfo', 'e-blueinfo'), 'manage_options', 'e-blueinfo', 'eblueinfo_page_admin');

            //call register settings function
            add_action( 'admin_init', array(&$this, 'register_settings') );

        }

        function theme_redirect() {
            global $wp, $eblueinfo_service_url, $eblueinfo_plugin_slug, $eblueinfo_texts, $similar_docs_url, $pdf_service_url, $thumb_service_url, $country_service_url, $infobutton_service_url, $services_platform_url;
            $pagename = '';
            $template = '';

            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            // check if request contains doc slug string
            $doc_slug = '/doc/';
            $pos_doc_slug = strpos($pagename, $doc_slug);
            if ( $pos_doc_slug !== false ){
                $pagename = substr($pagename, 0, $pos_doc_slug + strlen($doc_slug));
                $pagename = rtrim($pagename, '/');
            }

            if ( is_404() && $pos_slug !== false ){

                $eblueinfo_service_url = $this->service_url;
                $eblueinfo_plugin_slug = $this->plugin_slug;
                $similar_docs_url = $this->similar_docs_url;
                $pdf_service_url = $this->pdf_service_url;
                $thumb_service_url = $this->thumb_service_url;
                $country_service_url = $this->country_service_url;
                $infobutton_service_url = $this->infobutton_service_url;
                $services_platform_url = $this->services_platform_url;

                if ($pagename == $this->plugin_slug
                 || $pagename == $this->plugin_slug . '/doc'
                 || $pagename == $this->plugin_slug . '/collection'
                 || $pagename == $this->plugin_slug . '/browse'
                 || $pagename == $this->plugin_slug . '/search'
                 || $pagename == $this->plugin_slug . '/country'
                 || $pagename == $this->plugin_slug . '/infobutton'
                 || $pagename == $this->plugin_slug . '/infobutton/result'
                 || $pagename == $this->plugin_slug . '/auth') {

                    if ($pagename == $this->plugin_slug){
                        // generate lang cookie
                        if ( ! $_COOKIE['e-blueinfo-lang'] ) {
                            setCookie( 'e-blueinfo-lang', $lang, time() + (10 * 365 * 24 * 60 * 60), '/' );
                        }

                        // generate country cookie
                        if ( ! $_COOKIE['e-blueinfo-country'] ) {
                            if ( $_GET['country'] ) {
                                setCookie( 'e-blueinfo-country', $_GET['country'], time() + (10 * 365 * 24 * 60 * 60), '/' );
                            } else {
                                $template = EBLUEINFO_PLUGIN_PATH . '/template/home.php';
                            }
                        }

                        if ( ! wp_get_referer() && ! $_COOKIE['e-blueinfo-redirect'] ) {
                            if ( defined( 'POLYLANG_VERSION' ) ) {
                                $default_language = pll_default_language();

                                if ( $default_language != $_COOKIE['e-blueinfo-lang'] ) {
                                    setCookie( 'e-blueinfo-redirect', time(), time() + (10 * 365 * 24 * 60 * 60), '/' );

                                    $home_url = pll_home_url($_COOKIE['e-blueinfo-lang']) . $pagename;

                                    wp_redirect( $home_url );
                                    exit();
                                }
                            }
                        }

                        if ( empty($template) ) {
                            $template = EBLUEINFO_PLUGIN_PATH . '/template/community.php';
                        }
                    } elseif ($pagename == $this->plugin_slug . '/collection') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/collection.php';
                    } elseif ($pagename == $this->plugin_slug . '/browse') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/browse.php';
                    } elseif ($pagename == $this->plugin_slug . '/search') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/search.php';
                    } elseif ($pagename == $this->plugin_slug . '/country') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/country.php';
                    } elseif ($pagename == $this->plugin_slug . '/infobutton') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/infobutton-form.php';
                    } elseif ($pagename == $this->plugin_slug . '/infobutton/result') {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/infobutton-result.php';
                    } elseif ($pagename == $this->plugin_slug . '/auth') {
                        if ( $_COOKIE['userData'] ) {
                            echo '<script type="text/javascript">';
                            echo '    history.go(-(history.length - 1));';
                            echo '</script>';
                        } else {
                            $template = EBLUEINFO_PLUGIN_PATH . '/template/login.php';
                        }
                    } else {
                        $template = EBLUEINFO_PLUGIN_PATH . '/template/doc.php';
                    }

                    // force reload the page on hitting back button
                    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
                    header('Pragma: no-cache'); // HTTP 1.0.
                    header('Expires: 0'); // Proxies.

                    // force status to 200 - OK
                    status_header(200);

                    // redirect to page and finish execution
                    include($template);
                    die();
                }
            }
        }

        function register_sidebars(){
            global $eblueinfo_service_url, $country_service_url;
            $eblueinfo_service_url = $this->service_url;
            $country_service_url = $this->country_service_url;

            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            $response = @file_get_contents($country_service_url);
            if ($response){
                $countries = json_decode($response);
                $countries = normalize_country_object($countries, $lang);
            }

            if ( $countries ) {
                $count = 0;
                foreach ($countries as $key => $value) {
                    $eblueinfo_service_request = $eblueinfo_service_url . 'api/community/?country=' . $key . '&format=json';

                    $response = @file_get_contents($eblueinfo_service_request);
                    if ($response){
                        $response_json = json_decode($response);
                        $community_list = $response_json->objects;
                    }

                    if ( $community_list ) {
                        foreach ( $community_list as $community ) {
                            $count++;
                            $args = array(
                                'name' => __('e-BlueInfo Sidebar', 'e-blueinfo').' '.$count,
                                'id'   => 'e-blueinfo-sidebar-'.$community->id,
                                'description' => __('e-BlueInfo Sidebar', 'e-blueinfo').': '.$community->name.' ('.$value.')',
                                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                                'after_widget'  => '</div>',
                                'before_title'  => '<h2 class="widgettitle">',
                                'after_title'   => '</h2>',
                            );
                            register_sidebar( $args );
                        }
                    }
                }
            }
        }


        function theme_slug_render_title() {
            global $wp, $eblueinfo_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $eblueinfo_config = get_option('eblueinfo_config');

                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $eblueinfo_plugin_title = $eblueinfo_config['plugin_title_' . $current_lang];
                }else{
                    $eblueinfo_plugin_title = $eblueinfo_config['plugin_title'];
                }

                if ($pagename == $this->plugin_slug){
                    $text = __('Communities', 'e-blueinfo');
                }elseif ($pagename == $this->plugin_slug . '/collection'){
                    $text = __('Collections', 'e-blueinfo');
                }elseif ($pagename == $this->plugin_slug . '/browse'){
                    $text = __('Browse collection', 'e-blueinfo');
                }elseif ($pagename == $this->plugin_slug . '/search'){
                    $text = __('Search result', 'e-blueinfo');
                }elseif ($pagename == $this->plugin_slug . '/country'){
                    $text = __('Please choose a country', 'e-blueinfo');
                }else{
                    $text = __('Document', 'e-blueinfo');
                }

                $title_parts['title'] = $eblueinfo_plugin_title . " - " . $text . " | " . get_bloginfo('name');
            }

            return $title_parts;
        }

        function page_title(){
            global $wp;
            $pagename = $wp->request;

            if ( strpos($pagename, $this->plugin_slug) === 0 ) { // pagename starts with plugin slug
                return __('e-BlueInfo', 'e-blueinfo') . ' | ';
            }
        }

        function search_form( $form ) {
            global $wp;
            $pagename = $wp->request;

            if ($pagename == $this->plugin_slug || preg_match('/detail\//', $pagename)) {
                $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($this->plugin_slug) . '"',$form);
            }

            return $form;
        }

        function template_styles_scripts(){
            global $eblueinfo_plugin_slug, $polylang, $wp, $wp_styles, $wp_scripts;
            $home = real_site_url($eblueinfo_plugin_slug);
            $languages = array();
            $pagename = '';

            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            if ( $_COOKIE['e-blueinfo-lang'] ) {
                $lang = $_COOKIE['e-blueinfo-lang'];
            }

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
                $pagename = rtrim($pagename, '/') . '/';
            }

            if ( defined( 'POLYLANG_VERSION' ) ) {
                $pll_languages = pll_the_languages( array( 'raw' => 1 ) );

                foreach ($pll_languages as $slug => $language) {
                    $url = add_query_arg( $_SERVER['QUERY_STRING'], '', pll_home_url($slug) . $pagename );
                    $languages[$slug] = array(
                        'label' => $language['name'],
                        'url'  => $url
                    );
                }
            }

            foreach ($wp_styles->queue as $handle) {
                wp_dequeue_style( $handle );
                wp_deregister_style( $handle );
            }

            wp_enqueue_style('e-blueinfo-materialize', '//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-google-fonts-asap', '//fonts.googleapis.com/css?family=Asap+Condensed&display=swap', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-google-fonts-bebas', '//fonts.googleapis.com/css?family=Bebas+Neue&display=swap', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-material-icons', '//fonts.googleapis.com/icon?family=Material+Icons', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-font-awesome', '//use.fontawesome.com/releases/v5.8.2/css/all.css', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-slick', EBLUEINFO_PLUGIN_URL . 'template/css/slick.css', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-aos', EBLUEINFO_PLUGIN_URL . 'template/css/aos.css', array(), EBLUEINFO_VERSION);
            wp_enqueue_style('e-blueinfo-page', EBLUEINFO_PLUGIN_URL . 'template/css/style.css', array(), EBLUEINFO_VERSION);
            
            foreach ($wp_scripts->queue as $handle) {
                wp_dequeue_script( $handle );
                wp_deregister_script( $handle );
            }

            wp_enqueue_script('jquery');
            wp_enqueue_script('e-blueinfo-materialize', '//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-cookie', EBLUEINFO_PLUGIN_URL . 'template/js/cookie.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-page', EBLUEINFO_PLUGIN_URL . 'template/js/functions.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-loadmore', EBLUEINFO_PLUGIN_URL . 'template/js/loadmore.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-slick', EBLUEINFO_PLUGIN_URL . 'template/js/slick.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-aos', EBLUEINFO_PLUGIN_URL . 'template/js/aos.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-main', EBLUEINFO_PLUGIN_URL . 'template/js/main.js', array(), EBLUEINFO_VERSION, true);
            wp_enqueue_script('e-blueinfo-menu', EBLUEINFO_PLUGIN_URL . 'app/js/' . $lang . '/menu.js', array(), EBLUEINFO_VERSION, true);

            if ( $languages ) {
                wp_localize_script('e-blueinfo-menu', 'eblueinfo_script_vars', array(
                        'home' => $home,
                        'home_label' => __('Home','e-blueinfo'),
                        'lang_label' => __('Languages', 'e-blueinfo'),
                        'languages' => $languages
                    )
                );
            } else {
                wp_localize_script('e-blueinfo-page', 'eblueinfo_script_vars', array(
                        'home' => $home,
                        'home_label' => __('Home','e-blueinfo')
                    )
                );
            }
        }

        function register_settings(){
            register_setting('e-blueinfo-settings-group', 'eblueinfo_config');
        }

        function settings_link($links) {
            $settings_link = '<a href="options-general.php?page=e-blueinfo.php">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        function google_analytics_code(){
            global $wp;

            $pagename = $wp->request;
            $plugin_config = get_option('eblueinfo_config');

            // check if is defined GA code and pagename starts with plugin slug
            if ($plugin_config['google_analytics_code'] != ''
                && strpos($pagename, $this->plugin_slug) === 0) {

        ?>
        <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','__gaTracker');
            __gaTracker('create', '<?php echo $plugin_config['google_analytics_code']; ?>', 'auto');
            __gaTracker('send', 'pageview');
        </script>
        <?php
            } //endif
        }

        function force_cookie_lang() {
            global $wp;
            $url = home_url( add_query_arg( array(), $wp->request ) );
            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            if ( defined( 'POLYLANG_VERSION' ) ) {
                $lang = pll_current_language();
            }

            $fcl = ( isset($_GET['fcl']) && true == $_GET['fcl'] ) ? true : false;

            if ( $fcl ) {
                setCookie( 'e-blueinfo-lang', $lang, time() + (10 * 365 * 24 * 60 * 60), '/' );
                header('Location: '.$url);
                exit;
            }
        }

        function show_feedback_tab() {
            if ( EBLUEINFO_FEEDBACK ) {
                require_once(EBLUEINFO_PLUGIN_PATH . '/template/feedback.php');
            }
        }

        function custom_body_classes( $classes ) {
            if ( $_COOKIE['e-blueinfo-color'] ) {
                $classes[] = $_COOKIE['e-blueinfo-color'];
            }

            return $classes;
        }

    }
}

if(class_exists('EBlueInfo_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('EBlueInfo_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('EBlueInfo_Plugin', 'deactivate'));

    // Instantiate the plugin class
    $wp_plugin_template = new EBlueInfo_Plugin();
}

?>
