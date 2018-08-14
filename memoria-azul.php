<?php
/*
Plugin Name: Memória Azul
Plugin URI: https://github.com/bireme/memoria-azul-wp-plugin/
Description: Memória Azul WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.1
Author URI: http://reddes.bvsalud.org/
*/

define('MEMORIA_AZUL_VERSION', '0.1' );

define('MEMORIA_AZUL_SYMBOLIC_LINK', false );
define('MEMORIA_AZUL_PLUGIN_DIRNAME', 'memoria-azul' );

if(MEMORIA_AZUL_SYMBOLIC_LINK == true) {
    define('MEMORIA_AZUL_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . MEMORIA_AZUL_PLUGIN_DIRNAME );
} else {
    define('MEMORIA_AZUL_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('MEMORIA_AZUL_PLUGIN_DIR',   plugin_basename( MEMORIA_AZUL_PLUGIN_PATH ) );
define('MEMORIA_AZUL_PLUGIN_URL',   plugin_dir_url(__FILE__) );

require_once(MEMORIA_AZUL_PLUGIN_PATH . '/settings.php');
require_once(MEMORIA_AZUL_PLUGIN_PATH . '/template-functions.php');

if(!class_exists('Memoria_Azul_Plugin')) {
    class Memoria_Azul_Plugin {

        private $plugin_slug = 'memoria-azul';
        private $service_url = 'http://fi-admin.data.bvsalud.org/';
        private $similar_docs_url = 'http://similardocs.bireme.org/SDService';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            add_action( 'init', array(&$this, 'load_translation'));
            add_action( 'admin_menu', array(&$this, 'admin_menu'));
            add_action( 'plugins_loaded', array(&$this, 'plugin_init'));
            add_action( 'wp_head', array(&$this, 'google_analytics_code'));
            add_action( 'template_redirect', array(&$this, 'theme_redirect'));
            add_action( 'widgets_init', array(&$this, 'register_sidebars'));
            add_filter( 'get_search_form', array(&$this, 'search_form'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );
            add_filter( 'document_title_parts', array(&$this, 'theme_slug_render_title'));

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
            global $memoria_azul_texts;

		    // load internal plugin translations
		    load_plugin_textdomain( 'memoria-azul', false,  MEMORIA_AZUL_PLUGIN_DIR . '/languages' );
            // load plugin translations
            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            $memoria_azul_texts = @parse_ini_file(MEMORIA_AZUL_PLUGIN_PATH . "/languages/texts_" . $lang . ".ini", true);
		}

		function plugin_init() {
		    $memoria_azul_config = get_option('memoria_azul_config');

		    if ( $memoria_azul_config && $memoria_azul_config['plugin_slug'] != ''){
		        $this->plugin_slug = $memoria_azul_config['plugin_slug'];
		    }

		}

		function admin_menu() {

		    add_submenu_page( 'options-general.php', __('Memória Azul Settings', 'memoria-azul'), __('Memória Azul', 'memoria-azul'), 'manage_options', 'memoria-azul', 'memoria_azul_page_admin');

		    //call register settings function
		    add_action( 'admin_init', array(&$this, 'register_settings') );

		}

		function theme_redirect() {
		    global $wp, $memoria_azul_service_url, $memoria_azul_plugin_slug, $memoria_azul_texts, $similar_docs_url;
		    $pagename = '';

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

                $memoria_azul_service_url = $this->service_url;
                $memoria_azul_plugin_slug = $this->plugin_slug;
                $similar_docs_url = $this->similar_docs_url;

                if ($pagename == $this->plugin_slug
                 || $pagename == $this->plugin_slug . '/doc'
                 || $pagename == $this->plugin_slug . '/collection'
                 || $pagename == $this->plugin_slug . '/browse'
                 || $pagename == $this->plugin_slug . '/search') {

    		        add_action( 'wp_enqueue_scripts', array(&$this, 'template_styles_scripts') );

    		        if ($pagename == $this->plugin_slug){
    		            $template = MEMORIA_AZUL_PLUGIN_PATH . '/template/home.php';
                    }elseif ($pagename == $this->plugin_slug . '/collection'){
    		            $template = MEMORIA_AZUL_PLUGIN_PATH . '/template/collection.php';
                    }elseif ($pagename == $this->plugin_slug . '/browse'){
    		            $template = MEMORIA_AZUL_PLUGIN_PATH . '/template/browse.php';
                    }elseif ($pagename == $this->plugin_slug . '/search'){
    		            $template = MEMORIA_AZUL_PLUGIN_PATH . '/template/search.php';
    		        }else{
    		            $template = MEMORIA_AZUL_PLUGIN_PATH . '/template/doc.php';
    		        }
    		        // force status to 200 - OK
    		        status_header(200);

    		        // redirect to page and finish execution
    		        include($template);
    		        die();
    		    }
            }
		}

		function register_sidebars(){
		    $args = array(
		        'name' => __('Memória Azul sidebar', 'memoria-azul'),
		        'id'   => 'memoria-azul-home',
		        'description' => 'Memória Azul Area',
		        'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
		        'after_widget'  => '</section>',
		        'before_title'  => '<h2 class="widgettitle">',
		        'after_title'   => '</h2>',
		    );
		    register_sidebar( $args );
		}


        function theme_slug_render_title() {
            global $wp, $memoria_azul_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $memoria_azul_config = get_option('memoria_azul_config');
                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $memoria_azul_plugin_title = $memoria_azul_config['plugin_title_' . $current_lang];
                }else{
                    $memoria_azul_plugin_title = $memoria_azul_config['plugin_title'];
                }
                $title_parts['title'] = $memoria_azul_plugin_title . " | " . get_bloginfo('name');
            }

            return $title_parts;
        }

		function page_title(){
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ( strpos($pagename, $this->plugin_slug) === 0 ) { //pagename starts with plugin slug
		        return __('Memória Azul', 'memoria-azul') . ' | ';
		    }
		}

		function search_form( $form ) {
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ($pagename == $this->plugin_slug || preg_match('/detail\//', $pagename)) {
		        $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($this->plugin_slug) . '"',$form);
		    }

		    return $form;
		}

		function template_styles_scripts(){
            wp_enqueue_style ('memoria-azul-page', MEMORIA_AZUL_PLUGIN_URL . 'template/css/style.css', array(), MEMORIA_AZUL_VERSION);
            wp_enqueue_script('memoria-azul-page', MEMORIA_AZUL_PLUGIN_URL . 'template/js/functions.js', array(), MEMORIA_AZUL_VERSION);
            wp_enqueue_script('memoria-azul-loadmore', MEMORIA_AZUL_PLUGIN_URL . 'template/js/loadmore.js', array(), MEMORIA_AZUL_VERSION);
            wp_enqueue_script('memoria-azul-bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array(), MEMORIA_AZUL_VERSION);
		}

		function register_settings(){
		    register_setting('memoria-azul-settings-group', 'memoria_azul_config');
		}

        function settings_link($links) {
            $settings_link = '<a href="options-general.php?page=memoria-azul.php">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

		function google_analytics_code(){
		    global $wp;

		    $pagename = $wp->query_vars["pagename"];
		    $plugin_config = get_option('memoria_azul_config');

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

	}
}

if(class_exists('Memoria_Azul_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('Memoria_Azul_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('Memoria_Azul_Plugin', 'deactivate'));

    // Instantiate the plugin class
    $wp_plugin_template = new Memoria_Azul_Plugin();
}

?>
