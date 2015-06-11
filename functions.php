<?php
/**
 * HQTheme only works in WordPress 3.6 or later.
 */

/**
 * HQTheme main class
 */
class HQTheme {

    const THEME_SLUG = 'hqtheme';
    const VERSION = '1.0.0';

    static $instance;

    /**
     * HQTheme setup.
     *
     * Sets up theme defaults and registers the various WordPress features that
     * HQTheme supports.
     *
     * @uses load_theme_textdomain() For translation/localization support.
     * @uses add_editor_style() To add Visual Editor stylesheets.
     * @uses add_theme_support() To add support for automatic feed links, post
     * formats, and post thumbnails.
     * @uses register_nav_menu() To add support for a navigation menu.
     * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
     *
     * @since HQTheme 1.0
     */
    public function __construct() {
        //`lessc /home/katrazanov/www/hqtheme/theme/wp-content/themes/hqtheme/less/hq-base/hq-base.less > /home/katrazanov/www/hqtheme/theme/wp-content/themes/hqtheme/css/hq-base.css`;

        self::$instance = & $this;

        // Set HQTheme default values
        add_action('after_switch_theme', array($this, 'switch_theme'));

        add_action('after_setup_theme', array($this, 'setup_theme'));
        add_action('wp_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('wp_enqueue_scripts', array($this, 'register_styles'));

        require dirname(__FILE__) . '/inc/multiple_sidebars.php';
        require_once dirname(__FILE__) . '/inc/class-tgm-plugin-activation.php';
        add_action('tgmpa_register', array($this, 'hqtheme_register_required_plugins'));

        require get_template_directory() . '/inc/Customize.php';
        new HQTheme_Customize();

        // Enable shortcodes in the_excerpt
        add_filter('the_excerpt', 'do_shortcode');

        // ACF
        add_filter('acf/settings/path', array($this, 'acf_settings_path'));
        add_filter('acf/settings/dir', array($this, 'acf_settings_dir'));
        include_once( get_template_directory() . '/inc/acf-pro/acf.php' );
        include_once( get_template_directory() . '/inc/acf-pro/customfields.php' );

        add_action('admin_init', array($this, 'admin_init'), 20);
    }

    function hqtheme_register_required_plugins() {
        $plugins = array(
            array(
                'name' => 'Intuitive Custom Post Order',
                'slug' => 'intuitive-custom-post-order',
                'required' => false,
            ),
            array(
                'name' => 'WP Options Importer',
                'slug' => 'options-importer',
                'required' => true,
            ),
            array(
                'name' => 'Max Mega Menu',
                'slug' => 'megamenu',
                'required' => true,
            ),
            array(
                'name' => 'Breadcrumb NavXT',
                'slug' => 'breadcrumb-navxt',
                'required' => true,
            ),
            array(
                'name' => 'Regenerate Thumbnails',
                'slug' => 'regenerate-thumbnails',
                'required' => false,
            ),
        );

        /**
         * Array of configuration settings. Amend each line as needed.
         * If you want the default strings to be available under your own theme domain,
         * leave the strings uncommented.
         * Some of the strings are added into a sprintf, so see the comments at the
         * end of each line for what each argument will be.
         */
        $config = array(
            'default_path' => '', // Default absolute path to pre-packaged plugins.
            'menu' => 'tgmpa-install-plugins', // Menu slug.
            'has_notices' => true, // Show admin notices or not.
            'dismissable' => true, // If false, a user cannot dismiss the nag message.
            'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false, // Automatically activate plugins after installation or not.
            'message' => '', // Message to output right before the plugins table.
            'strings' => array(
                'page_title' => __('Install Required Plugins', 'tgmpa'),
                'menu_title' => __('Install Plugins', 'tgmpa'),
                'installing' => __('Installing Plugin: %s', 'tgmpa'), // %s = plugin name.
                'oops' => __('Something went wrong with the plugin API.', 'tgmpa'),
                'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.'), // %1$s = plugin name(s).
                'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.'), // %1$s = plugin name(s).
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'), // %1$s = plugin name(s).
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.'), // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.'), // %1$s = plugin name(s).
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'), // %1$s = plugin name(s).
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'), // %1$s = plugin name(s).
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.'), // %1$s = plugin name(s).
                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins'),
                'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins'),
                'return' => __('Return to Required Plugins Installer', 'tgmpa'),
                'plugin_activated' => __('Plugin activated successfully.', 'tgmpa'),
                'complete' => __('All plugins installed and activated successfully. %s', 'tgmpa'), // %s = dashboard link.
                'nag_type' => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            )
        );

        tgmpa($plugins, $config);
    }

    function admin_init() {
        include_once( get_template_directory() . '/inc/Import.php' );
        $HQTheme_Import = new HQTheme_Import();
    }

    /**
     * Set Theme defaults
     */
    function switch_theme() {
        $theme = get_option('stylesheet');
        if (false === get_option('theme_mods_' . $theme)) {
            $HQTheme_Customize = new HQTheme_Customize();
            update_option("theme_mods_$theme", $HQTheme_Customize->getDefaults());
        }
    }

    public function setup_theme() {
        /*
         * Makes Theme available for translation.
         * Translations can be added to the /languages/ directory.
         */
        load_theme_textdomain(self::THEME_SLUG, get_template_directory() . '/languages');

        /*
         * This theme styles the visual editor to resemble the theme style,
         * specifically font, colors, icons, and column width.
         */
        add_editor_style(array('css/editor-style.css', 's'));

        // Adds RSS feed links to <head> for posts and comments.
        add_theme_support('automatic-feed-links');

        // Adds WooCommerce support
        add_theme_support('woocommerce');

        /*
         * Switches default core markup for search form, comment form,
         * and comments to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
        ));

        /*
         * This theme supports all available post formats by default.
         * See http://codex.wordpress.org/Post_Formats
         */
        add_theme_support('post-formats', array(
            'audio', 'gallery', 'image', 'link', 'quote', 'status', 'video'
        ));

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'primary' => __('Main Menu', HQTheme::THEME_SLUG),
            'header_topline-left' => __('Header Left Menu', HQTheme::THEME_SLUG),
            'header_topline-center' => __('Header Center Menu', HQTheme::THEME_SLUG),
            'header_topline-right' => __('Header Right Menu', HQTheme::THEME_SLUG),
            'footer_bottom-left' => __('Footer Left Menu', HQTheme::THEME_SLUG),
            'footer_bottom-center' => __('Footer Center Menu', HQTheme::THEME_SLUG),
            'footer_bottom-right' => __('Footer Right Menu', HQTheme::THEME_SLUG),
        ));

        /* HQTODO
         * This theme uses a custom image sizes
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(350, 9999, true);
        add_image_size('cropped', 350, 166, true);
        add_image_size('fullwidth', 825, 9999, false);
        add_image_size('cropped-fullwidth', 825, 390, true);

        // Enable Retina Support HQTODO
        //require get_template_directory() . '/inc/Retina.php';
        // This theme uses its own gallery styles. HQTODO
        //add_filter('use_default_gallery_style', '__return_false');

        if (!isset($content_width))
            $content_width = 500;

        $defaults = array(
            'post' => '500',
            'page' => '500',
            'attachment' => '650',
            'artist' => '300',
            'movie' => '400',
        );
        add_theme_support('content-width', $defaults);

        $this->loadClasses();
    }

    public function loadClasses() {
        $classes = array(
            'elements' => 'HQTheme_Elements',
            'featuredContent' => 'HQTheme_FeaturedContent',
            'options' => 'HQTheme_Options',
            'layout' => 'HQTheme_Layout',
            'header' => 'HQTheme_Header',
            'footer' => 'HQTheme_Footer',
            'blog' => 'HQTheme_Blog',
            'woocommerce' => 'HQTheme_WooCommerce',
        );
        $currentDir = dirname(__FILE__);
        foreach ($classes as $file => $class) {
            require_once $currentDir . '/parts/' . $file . '.class.php';
            ${$class} = new $class;
        }

        //require_once $currentDir . '/inc/NavigationWalker.php';
        require_once $currentDir . '/inc/HQ_Walker_Comment.php';
    }

    /**
     * Returns the active path+skin.css
     *
     * @package Customizr
     * @since Customizr 3.0.15
     */
    function register_styles() {
        wp_register_style(HQTheme::THEME_SLUG, get_template_directory_uri() . '/css/hq-' . (get_theme_mod('hq_skin') ? get_theme_mod('hq_skin') : 'bussines') . '.css');
        wp_enqueue_style(HQTheme::THEME_SLUG);

        // Google Fonts
        if (get_theme_mod('hq_typography_custom_fonts') == 1) {
            $custom_font_subsets = get_theme_mod('hq_typography_custom_font_subsets');
            $subset_cyrillic = get_theme_mod('hq_typography_custom_font_subset_cyrillic');
            $subset_greek = get_theme_mod('hq_typography_custom_font_subset_greek');
            $subset_vietnamese = get_theme_mod('hq_typography_custom_font_subset_vietnamese');

            $protocol = is_ssl() ? 'https' : 'http';
            $subsets = 'latin,latin-ext';
            if ($custom_font_subsets == 1) {
                if ($subset_cyrillic == 1) {
                    $subsets .= ',cyrillic,cyrillic-ext';
                }
                if ($subset_greek == 1) {
                    $subsets .= ',greek,greek-ext';
                }
                if ($subset_vietnamese == 1) {
                    $subsets .= ',vietnamese';
                }
            }

            $os = array(
                'hq_typography_logo' => 1,
                'hq_typography_headings' => 1,
                'hq_typography_navigation' => 1,
                'hq_footer_widgets_headings' => 1,
                'hq_footer_widgets_links' => 1,
                'hq_footer_bottom_links' => 1,
                'hq_typography_links' => 1,
                'header_topline_links' => 1,
                'hq_typography_body' => 'all',
                'hq_footer_widgets_text' => 'all',
                'hq_footer_bottom_text' => 'all',
                'header_topline_text' => 'all',
            );

            $families = array();
            foreach ($os as $o => $all) {
                $tmp = explode(';', get_theme_mod($o));
                $params = array();
                foreach ($tmp as $p) {
                    $tmp2 = explode(':', $p);
                    if (empty($tmp2[1])) {
                        continue;
                    }
                    $params[$tmp2[0]] = $tmp2[1];
                }
                if (empty($params['font-family'])) {
                    continue;
                }
                if ($params['font-family'] == 'inherit') {
                    continue;
                }
                if (!in_array($params['font-family'], HQTheme_Customize::getInstance()->google_fonts)) {
                    continue;
                }
                $params['all'] = $all;
                $families[$params['font-family']] = $params;
            }
            $family = '';
            foreach ($families as $fam => $style) {
                if ($family != '') {
                    $family .= '|';
                }
                
                $family .= $fam . ':400,400italic,500italic,700italic,900,300,300italic,500,700,900italic';
                /* Load only used
                  if ($style['all'] == 'all') {
                  $family .= $fam . ':italic,700,700italic';
                  } else {
                  $fweight = '';
                  if ($style['font-weight'] != 'inherit') {
                  $fweight = $style['font-weight'];
                  }
                  $fstyle = '';
                  if ($style['font-style'] != 'inherit') {
                  $fstyle .= $style['font-style'];
                  }
                  if ($fweight || $fstyle) {
                  $family .= $fam . ':' . $fweight . $fstyle;
                  } else {
                  $family .= $fam . ':italic,700,700italic';
                  }
                  }
                 */
            }

            if ($family) {
                $custom_font_args = array(
                    'family' => str_replace(' ', '+', $family),
                    'subset' => $subsets,
                );
                wp_register_style('hq-google-fonts', add_query_arg($custom_font_args, $protocol . '://fonts.googleapis.com/css'), NULL, NULL, 'all');
                wp_enqueue_style('hq-google-fonts');
            }
        }
    }

    /**
     * Enqueue scripts and styles for the front end.
     *
     * @since HQTheme 1.0
     */
    function scripts_styles() {
        /*
         * Adds JavaScript to pages with the comment form to support
         * sites with threaded comments (when in use).
         */
        if (is_singular() && comments_open() && get_option('thread_comments'))
            wp_enqueue_script('comment-reply');

        // Adds Masonry to handle vertical alignment of footer widgets.
        if (is_active_sidebar('main'))
            wp_enqueue_script('jquery-masonry');

        // Loads JavaScript file with functionality specific to Twenty Thirteen.
        wp_enqueue_script(self::THEME_SLUG . '-script', get_template_directory_uri() . '/js/functions.js', array('jquery'), HQTheme::VERSION, true);

        // Loads our main stylesheet.
        wp_enqueue_style(self::THEME_SLUG . '-style', get_stylesheet_uri(), array(), HQTheme::VERSION);

        // Loads the Internet Explorer specific stylesheet.
        wp_enqueue_style(self::THEME_SLUG . '-ie', get_template_directory_uri() . '/css/ie.css', array(self::THEME_SLUG . '-style'), HQTheme::VERSION);
        wp_style_add_data(self::THEME_SLUG . '-ie', 'conditional', 'lt IE 9');

        // Bootstrap 3.0
        wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), HQTheme::VERSION, true);

        // Smart menu http://www.smartmenus.org/docs/
        wp_enqueue_script('jquery.smartmenus', get_template_directory_uri() . '/js/smartmenus/jquery.smartmenus.js', array('jquery'), HQTheme::VERSION, true);
        wp_enqueue_script('jquery.smartmenus.bootstrap', get_template_directory_uri() . '/js/smartmenus/jquery.smartmenus.bootstrap.js', array('jquery'), HQTheme::VERSION, true);

        // Search http://tympanus.net/codrops/2013/06/26/expanding-search-bar-deconstructed/
        wp_enqueue_script('modernizr.custom', get_template_directory_uri() . '/js/modernizr.custom.js', array(), HQTheme::VERSION, true);
        wp_enqueue_script('classie', get_template_directory_uri() . '/js/classie.js', array(), HQTheme::VERSION, true);
        wp_enqueue_script('uisearch', get_template_directory_uri() . '/js/uisearch.js', array(), HQTheme::VERSION, true);

        // Waypoint
        wp_register_script('waypoint', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'), HQTheme::VERSION, true);
        // Easing
        wp_register_script('jquery.easing', get_template_directory_uri() . '/js/jquery.easing.min.js', array('jquery'), HQTheme::VERSION, true);
        // Easy Pie Chart
        wp_register_script('jquery.easypiechart', get_template_directory_uri() . '/js/jquery.easypiechart.min.js', array('jquery'), HQTheme::VERSION, true);
        // 
        wp_register_script('jquery.animate-enhanced', get_template_directory_uri() . '/js/jquery.animate-enhanced.js', array('jquery'), HQTheme::VERSION, true);
        // SuperSlides
        wp_register_script('jquery.superslides', get_template_directory_uri() . '/js/jquery.superslides.min.js', array('jquery', 'jquery.easing', 'jquery.animate-enhanced'), HQTheme::VERSION, true);

        wp_enqueue_script('skrollr', get_template_directory_uri() . '/js/skrollr.js', array(), HQTheme::VERSION, true);
        wp_enqueue_script('imagesloaded', get_template_directory_uri() . '/js/imagesloaded.js', array(), HQTheme::VERSION);
        // wp_enqueue_script('jquery-wookmark', get_template_directory_uri() . '/js/jquery.wookmark.js', array('jquery'), HQTheme::VERSION, true); // TODO
        wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/js/magnific-popup.js', array('jquery'), HQTheme::VERSION);

        wp_enqueue_script('jquery-infinitescroll', get_template_directory_uri() . '/js/jquery.infinitescroll.dev.js', array('jquery'), HQTheme::VERSION);
        wp_enqueue_script('jquery-masonry', get_template_directory_uri() . '/js/jquery.masonry.js', array('jquery'), HQTheme::VERSION);
    }

    /**
     * Enqueue Javascript postMessage handlers for the Customizer.
     *
     * Binds JavaScript handlers to make the Customizer preview
     * reload changes asynchronously.
     *
     * @since HQTheme 1.0
      function customize_preview_js() {
      wp_enqueue_script(self::THEME_SLUG . '-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array('customize-preview'), '20130226', true);
      }
     */
    // ACF Pro
    function acf_settings_path($path) {
        return get_template_directory() . '/inc/acf-pro/';
    }

    function acf_settings_dir($dir) {
        return get_template_directory_uri() . '/inc/acf-pro/';
    }

}

$isHQTheme = true;
$HQTheme = new HQTheme();

/**
 * Functions
 */

/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since HQTheme 1.0.0
 */
function hqtheme_paging_nav() {
    global $wp_query;

// Don't print empty markup if there's only one page.
    if ($wp_query->max_num_pages < 2)
        return;
    ?>
    <nav class="navigation paging-navigation" role="navigation">
        <?php if (get_next_posts_link()) : ?>
            <div class="nav-previous"><?php next_posts_link(__('Older posts', HQTheme::THEME_SLUG)); ?></div>
        <?php endif; ?>

        <?php if (get_previous_posts_link()) : ?>
            <div class="nav-next"><?php previous_posts_link(__('Newer posts', HQTheme::THEME_SLUG)); ?></div>
        <?php endif; ?>
        <div class="clear"></div>
    </nav><!-- .navigation -->
    <?php
}

/**
 * Display navigation to next/previous post when applicable.
 *
 * @since HQTheme 1.0.0
 */
function hqtheme_post_nav() {
    if (!get_theme_mod('hq_blog_single_prev_next')) {
        return;
    }
    global $post;

// Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
    $next = get_adjacent_post(false, '', false);

    if (!$next && !$previous)
        return;
    ?>
    <nav class="navigation post-navigation" role="navigation">
        <span class="prev-post"><?php previous_post_link('%link', _x('<i class="fa fa-angle-double-left"></i> %title', 'Previous post link', HQTheme::THEME_SLUG)); ?></span>
        <span class="next-post"><?php next_post_link('%link', _x('%title <i class="fa fa-angle-double-right"></i>', 'Next post link', HQTheme::THEME_SLUG)); ?></span>
    </nav><!-- .navigation -->
    <?php
}

/**
 * Print HTML with meta information for current post
 *
 * Create your own hqtheme_entry_meta() to override in a child theme.
 *
 * @since HQTheme 1.0.0
 */
function hqtheme_entry_meta() {
    if (is_single()) {
        $pos = 'single';
    } else {
        $pos = 'list';
    }
    if (!get_theme_mod('hq_blog_' . $pos . '_meta')) {
        return;
    }
    if (is_sticky() && is_home() && !is_paged())
        echo '<span class="featured-post">' . __('Sticky', HQTheme::THEME_SLUG) . '</span>';

    if (!has_post_format('link') && 'post' == get_post_type() && get_theme_mod('hq_blog_' . $pos . '_meta_date'))
        hqtheme_entry_date();

    // Translators: used between list items, there is a space after the comma.
    if (get_theme_mod('hq_blog_' . $pos . '_meta_categories')) {
        $categories_list = get_the_category_list(__(', ', HQTheme::THEME_SLUG));
        if ($categories_list) {
            echo '<span class="categories-links"><i class="fa fa-stack-exchange"></i> ' . $categories_list . '</span>';
        }
    }

    // Comments
    if (comments_open() && !is_single()) {
        ?>
        <span class="comments-link">
            <i class="fa fa-comment"></i> <?php comments_popup_link('<span class="leave-reply">' . __('Leave a comment', HQTheme::THEME_SLUG) . '</span>', __('One comment so far', HQTheme::THEME_SLUG), __('View all % comments', HQTheme::THEME_SLUG)); ?>
        </span><!-- .comments-link -->
        <?php
    }

    // Post author
    if (get_theme_mod('hq_blog_' . $pos . '_meta_author')) {
        if ('post' == get_post_type()) {
            printf('<span class="author vcard"><i class="fa fa-user"></i> <a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(__('View all posts by %s', HQTheme::THEME_SLUG), get_the_author())), get_the_author()
            );
        }
    }
    edit_post_link(__('Edit', HQTheme::THEME_SLUG), '<span class="edit-link"><i class="fa fa-pencil-square-o"></i> ', '</span>');
}

function hqtheme_entry_meta_bottom() {
    if (is_single()) {
        $pos = 'single';
    } else {
        $pos = 'list';
    }
    if (!get_theme_mod('hq_blog_' . $pos . '_meta')) {
        return;
    }
    // Translators: used between list items, there is a space after the comma.
    if (get_theme_mod('hq_blog_' . $pos . '_meta_tags')) {
        $tag_list = get_the_tag_list('', __(', ', HQTheme::THEME_SLUG));
        if ($tag_list) {
            echo '<span class="tags-links"><i class="fa fa-tags"></i> ' . $tag_list . '</span>';
        }
    }

    if ($pos == 'list') {
        if (get_theme_mod('hq_blog_list_meta_read_more')) {
            echo '<a class="read-more" href="' . esc_url(get_permalink()) . '">' . _('Read more') . '</a>';
        }
    }
    echo '<div class="clear"></div>';
}

/**
 * Print HTML with date information for current post.
 *
 * Create your own hqtheme_entry_date() to override in a child theme.
 *
 * @since HQTheme 1.0.0
 *
 * @param boolean $echo (optional) Whether to echo the date. Default true.
 * @return string The HTML-formatted post date.
 */
function hqtheme_entry_date($echo = true) {
    $format_prefix = '%2$s';

    $date = sprintf('<span class="date"><i class="fa fa-calendar"></i> <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>', esc_url(get_permalink()), esc_attr(sprintf(__('Permalink to %s', HQTheme::THEME_SLUG), the_title_attribute('echo=0'))), esc_attr(get_the_date('c')), esc_html(sprintf($format_prefix, get_post_format_string(get_post_format()), get_the_date(get_theme_mod('hq_blog_meta_date_format') ? get_theme_mod('hq_blog_meta_date_format') : null)))
    );

    if ($echo)
        echo $date;

    return $date;
}

/**
 * Print the attached image with a link to the next attached image.
 *
 * @since Twenty Thirteen 1.0
 */
function hqtheme_the_attached_image() {
    /**
     * Filter the image attachment size to use.
     *
     * @since HQTheme 1.0.0
     *
     * @param array $size {
     *     @type int The attachment height in pixels.
     *     @type int The attachment width in pixels.
     * }
     */
    $attachment_size = apply_filters('hqtheme_attachment_size', array(724, 724));
    $next_attachment_url = wp_get_attachment_url();
    $post = get_post();

    /*
     * Grab the IDs of all the image attachments in a gallery so we can get the URL
     * of the next adjacent image in a gallery, or the first image (if we're
     * looking at the last image in a gallery), or, in a gallery of one, just the
     * link to that image file.
     */
    $attachment_ids = get_posts(array(
        'post_parent' => $post->post_parent,
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'orderby' => 'menu_order ID'
    ));

// If there is more than 1 attachment in a gallery...
    if (count($attachment_ids) > 1) {
        foreach ($attachment_ids as $attachment_id) {
            if ($attachment_id == $post->ID) {
                $next_id = current($attachment_ids);
                break;
            }
        }

        // get the URL of the next image attachment...
        if ($next_id)
            $next_attachment_url = get_attachment_link($next_id);

        // or get the URL of the first image attachment.
        else
            $next_attachment_url = get_attachment_link(array_shift($attachment_ids));
    }

    printf('<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>', esc_url($next_attachment_url), the_title_attribute(array('echo' => false)), wp_get_attachment_image($post->ID, $attachment_size)
    );
}

/**
 * Return the post URL.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since HQTheme 1.0.0
 *
 * @return string The Link format URL.
 */
function hqtheme_get_link_url() {
    $content = get_the_content();
    $has_url = get_url_in_content($content);

    return ( $has_url ) ? $has_url : apply_filters('the_permalink', get_permalink());
}

function hqtheme_entry_social() {

    $share_header_title = get_theme_mod('x_portfolio_share_project_title');

    $enable_twitter = get_theme_mod('hq_social_share_twitter');
    $enable_facebook = get_theme_mod('hq_social_share_facebook');
    $enable_google_plus = get_theme_mod('hq_social_share_google');
    $enable_pinterest = get_theme_mod('hq_social_share_pinterest');
    $enable_linkedin = get_theme_mod('hq_social_share_linkedin');
    $enable_reddit = get_theme_mod('hq_social_share_facebook');
    $enable_email = get_theme_mod('hq_social_share_email');

    $share_url = urlencode(get_permalink());
    $share_title = urlencode(get_the_title());
    $share_source = urlencode(get_bloginfo('name'));
    $share_content = urlencode(get_the_content());
    $share_media = wp_get_attachment_thumb_url(get_post_thumbnail_id());

    $twitter = ( $enable_twitter == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"share\" title=\"" . __('Share on Twitter', '__x__') . "\" onclick=\"window.open('https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}', 'popupTwitter', 'width=500, height=370, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-twitter\"></i></a>" : '';
    $facebook = ( $enable_facebook == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share\" title=\"" . __('Share on Facebook', '__x__') . "\" onclick=\"window.open('http://www.facebook.com/sharer.php?u={$share_url}&amp;t={$share_title}', 'popupFacebook', 'width=650, height=270, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-facebook\"></i></a>" : '';
    $google_plus = ( $enable_google_plus == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share\" title=\"" . __('Share on Google+', '__x__') . "\" onclick=\"window.open('https://plus.google.com/share?url={$share_url}', 'popupGooglePlus', 'width=650, height=226, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-google-plus\"></i></a>" : '';
    $pinterest = ( $enable_pinterest == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share\" title=\"" . __('Share on Pinterest', '__x__') . "\" onclick=\"window.open('http://pinterest.com/pin/create/button/?url={$share_url}&amp;media={$share_media}&amp;description={$share_title}', 'popupPinterest', 'width=750, height=265, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-pinterest\"></i></a>" : '';
    $linkedin = ( $enable_linkedin == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share\" title=\"" . __('Share on LinkedIn', '__x__') . "\" onclick=\"window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url={$share_url}&amp;title={$share_title}&amp;summary={$share_content}&amp;source={$share_source}', 'popupLinkedIn', 'width=610, height=480, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-linkedin\"></i></a>" : '';
    $reddit = ( $enable_reddit == 1 ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share\" title=\"" . __('Share on Reddit', '__x__') . "\" onclick=\"window.open('http://www.reddit.com/submit?url={$share_url}', 'popupReddit', 'width=875, height=450, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"fa fa-reddit\"></i></a>" : '';
    $email = ( $enable_email == 1 ) ? "<a href=\"mailto:?subject=" . get_the_title() . "&amp;body=" . __('Hey, thought you might enjoy this! Check it out when you have a chance:', '__x__') . " " . get_permalink() . "\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"hq-share email\" title=\"" . __('Share via Email', '__x__') . "\"><span><i class=\"fa fa-envelope-o\"></i></span></a>" : '';
    ?>

    <?php if ($enable_facebook == 1 || $enable_twitter == 1 || $enable_google_plus == 1 || $enable_linkedin == 1 || $enable_pinterest == 1 || $enable_reddit == 1 || $enable_email == 1) : ?>
        <div class="entry-share man">
            <div class="share-options">
                <p><?php echo $share_header_title; ?></p>
                <?php echo $facebook . $twitter . $google_plus . $linkedin . $pinterest . $reddit . $email ?>
            </div>
        </div>
        <?php
    endif;
}

//add_filter('wp_nav_menu_items', 'sk_wcmenucart', 10, 2);

function sk_wcmenucart($menu, $args) {
    // Check if WooCommerce is active and add a new item to a menu assigned to Primary Navigation Menu location
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ||
            'primary' !== $args->theme_location) {
        return $menu;
    }
    ob_start();
    global $woocommerce;
    $viewing_cart = __('View your shopping cart', 'your-theme-slug');
    $start_shopping = __('Start shopping', 'your-theme-slug');
    $cart_url = $woocommerce->cart->get_cart_url();
    $shop_page_url = get_permalink(woocommerce_get_page_id('shop'));
    $cart_contents_count = $woocommerce->cart->cart_contents_count;
    $cart_contents = sprintf(_n('%d item', '%d items', $cart_contents_count, 'your-theme-slug'), $cart_contents_count);
    $cart_total = $woocommerce->cart->get_cart_total();
    // Uncomment the line below to hide nav menu cart item when there are no items in the cart
    // if ( $cart_contents_count > 0 ) {
    if ($cart_contents_count == 0) {
        $menu_item = '<li class="right"><a class="wcmenucart-contents" href="' . $shop_page_url . '" title="' . $start_shopping . '">';
    } else {
        $menu_item = '<li class="right"><a class="wcmenucart-contents" href="' . $cart_url . '" title="' . $viewing_cart . '">';
    }
    $menu_item .= '<i class="fa fa-shopping-cart"></i> ';
    $menu_item .= $cart_contents . ' - ' . $cart_total;
    $menu_item .= '</a></li>';
    // Uncomment the line below to hide nav menu cart item when there are no items in the cart
    // }
    echo $menu_item;
    $social = ob_get_clean();
    return $menu . $social;
}
