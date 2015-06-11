<?php

class Shortcodes_Ultimate {

    /**
     * Constructor
     */
    function __construct() {
        add_action('plugins_loaded', array(__CLASS__, 'init'));
        add_action('init', array(__CLASS__, 'register'));
        add_action('init', array(__CLASS__, 'update'), 20);
        register_activation_hook(HQ_PLUGIN_FILE, array(__CLASS__, 'activation'));
        register_activation_hook(HQ_PLUGIN_FILE, array(__CLASS__, 'deactivation'));
    }

    /**
     * Plugin init
     */
    public static function init() {
        // Make plugin available for translation
        load_plugin_textdomain('hq', false, dirname(plugin_basename(HQ_PLUGIN_FILE)) . '/languages/');
        // Setup admin class
        $admin = new Sunrise4(array(
            'file' => HQ_PLUGIN_FILE,
            'slug' => 'su',
            'prefix' => 'hq_option_',
            'textdomain' => 'su'
        ));
        // Top-level menu
        // Translate plugin meta
        __('Shortcodes Ultimate', 'su');
        __('Vladimir Anokhin', 'su');
        __('hqpercharge your WordPress theme with mega pack of shortcodes', 'su');
        // Add plugin actions links
        add_filter('plugin_action_links_' . plugin_basename(HQ_PLUGIN_FILE), array(__CLASS__, 'actions_links'), -10);
        // Add plugin meta links
        add_filter('plugin_row_meta', array(__CLASS__, 'meta_links'), 10, 2);
        // Shortcodes Ultimate is ready
        do_action('hq/init');
    }

    /**
     * Plugin activation
     */
    public static function activation() {
        self::timestamp();
        self::skins_dir();
        update_option('hq_option_version', HQ_PLUGIN_VERSION);
        do_action('hq/activation');
    }

    /**
     * Plugin deactivation
     */
    public static function deactivation() {
        do_action('hq/deactivation');
    }

    /**
     * Plugin update hook
     */
    public static function update() {
        $option = get_option('hq_option_version');
        if ($option !== HQ_PLUGIN_VERSION) {
            update_option('hq_option_version', HQ_PLUGIN_VERSION);
            do_action('hq/update');
        }
    }

    /**
     * Register shortcodes
     */
    public static function register() {
        // Prepare compatibility mode prefix
        $prefix = hq_cmpt();
        // Loop through shortcodes
        foreach ((array) HQ_Data::shortcodes() as $id => $data) {
            if (isset($data['function']) && is_callable($data['function']))
                $func = $data['function'];
            elseif (is_callable(array('HQ_Shortcodes', $id)))
                $func = array('HQ_Shortcodes', $id);
            elseif (is_callable(array('HQ_Shortcodes', 'hq_' . $id)))
                $func = array('HQ_Shortcodes', 'hq_' . $id);
            else
                continue;
            // Register shortcode
            add_shortcode($prefix . $id, $func);
        }
        // Register [media] manually // 3.x
        add_shortcode($prefix . 'media', array('HQ_Shortcodes', 'media'));
    }

    /**
     * Add timestamp
     */
    public static function timestamp() {
        if (!get_option('hq_installed'))
            update_option('hq_installed', time());
    }

    /**
     * Create directory /wp-content/uploads/shortcodes-ultimate-skins/ on activation
     */
    public static function skins_dir() {
        $upload_dir = wp_upload_dir();
        $path = trailingslashit(path_join($upload_dir['basedir'], 'shortcodes-ultimate-skins'));
        if (!file_exists($path))
            mkdir($path, 0755);
    }

    /**
     * Add plugin actions links
     */
    public static function actions_links($links) {
        $links[] = '<a href="' . admin_url('admin.php?page=shortcodes-ultimate-examples') . '">' . __('Examples', 'su') . '</a>';
        $links[] = '<a href="' . admin_url('admin.php?page=shortcodes-ultimate') . '#tab-0">' . __('Where to start?', 'su') . '</a>';
        return $links;
    }

    /**
     * Add plugin meta links
     */
    public static function meta_links($links, $file) {
        // Check plugin
        if ($file === plugin_basename(HQ_PLUGIN_FILE)) {
            unset($links[2]);
            $links[] = '<a href="http://gndev.info/shortcodes-ultimate/" target="_blank">' . __('Project homepage', 'su') . '</a>';
            $links[] = '<a href="http://wordpress.org/support/plugin/shortcodes-ultimate/" target="_blank">' . __('hqpport forum', 'su') . '</a>';
            $links[] = '<a href="http://wordpress.org/extend/plugins/shortcodes-ultimate/changelog/" target="_blank">' . __('Changelog', 'su') . '</a>';
        }
        return $links;
    }

}

/**
 * Register plugin function to perform checks that plugin is installed
 */
function shortcodes_ultimate() {
    return true;
}

new Shortcodes_Ultimate;
