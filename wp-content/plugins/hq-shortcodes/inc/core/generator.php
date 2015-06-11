<?php

/**
 * Shortcode Generator
 */
class HQ_Generator {

    /**
     * Constructor
     */
    function __construct() {
        add_action('media_buttons', array(__CLASS__, 'button'), 1000);

        add_action('hq/update', array(__CLASS__, 'reset'));
        add_action('hq/activation', array(__CLASS__, 'reset'));
        add_action('sunrise/page/before', array(__CLASS__, 'reset'));
        add_action('create_term', array(__CLASS__, 'reset'), 10, 3);
        add_action('edit_term', array(__CLASS__, 'reset'), 10, 3);
        add_action('delete_term', array(__CLASS__, 'reset'), 10, 3);

        add_action('wp_ajax_hq_generator_settings', array(__CLASS__, 'settings'));
        add_action('wp_ajax_hq_generator_preview', array(__CLASS__, 'preview'));
        add_action('hq/generator/actions', array(__CLASS__, 'presets'));

        add_action('wp_ajax_hq_generator_get_icons', array(__CLASS__, 'ajax_get_icons'));
        add_action('wp_ajax_hq_generator_get_terms', array(__CLASS__, 'ajax_get_terms'));
        add_action('wp_ajax_hq_generator_get_taxonomies', array(__CLASS__, 'ajax_get_taxonomies'));
        add_action('wp_ajax_hq_generator_add_preset', array(__CLASS__, 'ajax_add_preset'));
        add_action('wp_ajax_hq_generator_remove_preset', array(__CLASS__, 'ajax_remove_preset'));
        add_action('wp_ajax_hq_generator_get_preset', array(__CLASS__, 'ajax_get_preset'));
    }

    /**
     * Generator button
     */
    public static function button($args = array()) {
        // Check access
        if (!self::access_check())
            return;
        // Prepare button target
        $target = is_string($args) ? $args : 'content';
        // Prepare args
        $args = wp_parse_args($args, array(
            'target' => $target,
            'text' => __('Insert shortcode', 'su'),
            'class' => 'button',
            'icon' => plugins_url('assets/images/icon.png', HQ_PLUGIN_FILE),
            'echo' => true,
            'shortcode' => false
        ));
        // Prepare icon
        if ($args['icon'])
            $args['icon'] = '<img src="' . $args['icon'] . '" /> ';
        // Print button
        $button = '<a href="javascript:void(0);" class="hq-generator-button ' . $args['class'] . '" title="' . $args['text'] . '" data-target="' . $args['target'] . '" data-mfp-src="#hq-generator" data-shortcode="' . (string) $args['shortcode'] . '">' . $args['icon'] . $args['text'] . '</a>';
        // Show generator popup
        add_action('wp_footer', array(__CLASS__, 'popup'));
        add_action('admin_footer', array(__CLASS__, 'popup'));
        // Request assets
        wp_enqueue_media();
        hq_query_asset('css', array('simpleslider', 'farbtastic', 'magnific-popup', 'font-awesome', 'hq-generator'));
        hq_query_asset('js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'simpleslider', 'farbtastic', 'magnific-popup', 'hq-generator'));
        // Print/return result
        if ($args['echo'])
            echo $button;
        return $button;
    }

    /**
     * Cache reset
     */
    public static function reset() {
        // Clear popup cache
        delete_transient('hq/generator/popup');
        // Clear shortcodes settings cache
        foreach (array_keys((array) HQ_Data::shortcodes()) as $shortcode)
            delete_transient('hq/generator/settings/' . $shortcode);
    }

    /**
     * Generator popup form
     */
    public static function popup() {
        // Get cache
        $output = get_transient('hq/generator/popup');
        if ($output && HQ_ENABLE_CACHE)
            echo $output;
        // Cache not found
        else {
            ob_start();

            // Add add-ons links
            if (!defined('hqE_PLUGIN_FILE') || !defined('hqS_PLUGIN_FILE') || !defined('hqM_PLUGIN_FILE'))
                $tools[] = '<a href="' . admin_url('admin.php?page=shortcodes-ultimate-addons') . '" target="_blank" title="' . __('Add-ons', 'su') . '" class="hq-add-ons">' . __('Add-ons', 'su') . '</a>';
            ?>
            <div id="hq-generator-wrap" style="display:none">
                <div id="hq-generator">
                    <div id="hq-generator-header">
                        <input type="text" name="hq_generator_search" id="hq-generator-search" value="" placeholder="<?php _e('Search for shortcodes', 'su'); ?>" />
                        <div id="hq-generator-filter">
                            <strong><?php _e('Filter by type', 'su'); ?></strong>
                            <?php foreach ((array) HQ_Data::groups() as $group => $label)
                                echo '<a href="#" data-filter="' . $group . '">' . $label . '</a>';
                            ?>
                        </div>
                        <div id="hq-generator-choices" class="hq-generator-clearfix">
                            <?php
                            // Choices loop
                            foreach ((array) HQ_Data::shortcodes() as $name => $shortcode) {
                                $icon = ( isset($shortcode['icon']) ) ? $shortcode['icon'] : 'puzzle-piece';
                                $shortcode['name'] = ( isset($shortcode['name']) ) ? $shortcode['name'] : $name;
                                echo '<span data-name="' . $shortcode['name'] . '" data-shortcode="' . $name . '" title="' . esc_attr($shortcode['desc']) . '" data-desc="' . esc_attr($shortcode['desc']) . '" data-group="' . $shortcode['group'] . '">' . HQ_Tools::icon($icon) . $shortcode['name'] . '</span>' . "\n";
                            }
                            ?>
                        </div>
                    </div>
                    <div id="hq-generator-settings"></div>
                    <input type="hidden" name="hq-generator-selected" id="hq-generator-selected" value="<?php echo plugins_url('', HQ_PLUGIN_FILE); ?>" />
                    <input type="hidden" name="hq-generator-url" id="hq-generator-url" value="<?php echo plugins_url('', HQ_PLUGIN_FILE); ?>" />
                    <input type="hidden" name="hq-compatibility-mode-prefix" id="hq-compatibility-mode-prefix" value="<?php echo hq_compatibility_mode_prefix(); ?>" />
                    <div id="hq-generator-result" style="display:none"></div>
                </div>
            </div>
            <?php
            $output = ob_get_contents();
            set_transient('hq/generator/popup', $output, 2 * DAY_IN_SECONDS);
            ob_end_clean();
            echo $output;
        }
    }

    /**
     * Process AJAX request
     */
    public static function settings() {
        self::access();
        // Param check
        if (empty($_REQUEST['shortcode']))
            wp_die(__('Shortcode not specified', 'su'));
        // Get cache
        $output = get_transient('hq/generator/settings/' . sanitize_text_field($_REQUEST['shortcode']));
        if ($output && HQ_ENABLE_CACHE)
            echo $output;
        // Cache not found
        else {
            // Request queried shortcode
            $shortcode = HQ_Data::shortcodes(sanitize_key($_REQUEST['shortcode']));
            // Prepare skip-if-default option
            $skip = ( get_option('hq_option_skip') === 'on' ) ? ' hq-generator-skip' : '';
            // Prepare actions
            $actions = apply_filters('hq/generator/actions', array(
                'insert' => '<a href="javascript:void(0);" class="button button-primary button-large hq-generator-insert"><i class="fa fa-check"></i> ' . __('Insert shortcode', 'su') . '</a>',
                'preview' => '<a href="javascript:void(0);" class="button button-large hq-generator-toggle-preview"><i class="fa fa-eye"></i> ' . __('Live preview', 'su') . '</a>'
            ));
            // Shortcode header
            $return = '<div id="hq-generator-breadcrumbs">';
            $return .= apply_filters('hq/generator/breadcrumbs', '<a href="javascript:void(0);" class="hq-generator-home" title="' . __('Click to return to the shortcodes list', 'su') . '">' . __('All shortcodes', 'su') . '</a> &rarr; <span>' . $shortcode['name'] . '</span> <small class="alignright">' . $shortcode['desc'] . '</small><div class="hq-generator-clear"></div>');
            $return .= '</div>';

            // Shortcode has atts
            if (count($shortcode['atts']) && $shortcode['atts']) {
                // Loop through shortcode parameters
                foreach ($shortcode['atts'] as $attr_name => $attr_info) {
                    // Prepare default value
                    $default = (string) ( isset($attr_info['default']) ) ? $attr_info['default'] : '';
                    $attr_info['name'] = (isset($attr_info['name'])) ? $attr_info['name'] : $attr_name;
                    $return .= '<div class="hq-generator-attr-container' . $skip . '" data-default="' . esc_attr($default) . '">';
                    $return .= '<h5>' . $attr_info['name'] . '</h5>';
                    // Create field types
                    if (!isset($attr_info['type']) && isset($attr_info['values']) && is_array($attr_info['values']) && count($attr_info['values']))
                        $attr_info['type'] = 'select';
                    elseif (!isset($attr_info['type']))
                        $attr_info['type'] = 'text';
                    if (is_callable(array('HQ_Generator_Views', $attr_info['type'])))
                        $return .= call_user_func(array('HQ_Generator_Views', $attr_info['type']), $attr_name, $attr_info);
                    elseif (isset($attr_info['callback']) && is_callable($attr_info['callback']))
                        $return .= call_user_func($attr_info['callback'], $attr_name, $attr_info);
                    if (isset($attr_info['desc']))
                        $return .= '<div class="hq-generator-attr-desc">' . str_replace(array('<b%value>', '<b_>'), '<b class="hq-generator-set-value" title="' . __('Click to set this value', 'su') . '">', $attr_info['desc']) . '</div>';
                    $return .= '</div>';
                }
            }
            // Single shortcode (not closed)
            if ($shortcode['type'] == 'single')
                $return .= '<input type="hidden" name="hq-generator-content" id="hq-generator-content" value="false" />';
            // Wrapping shortcode
            else
                $return .= '<div class="hq-generator-attr-container"><h5>' . __('Content', 'su') . '</h5><textarea name="hq-generator-content" id="hq-generator-content" rows="5">' . esc_attr(str_replace(array('%prefix_', '__'), hq_cmpt(), $shortcode['content'])) . '</textarea></div>';
            $return .= '<div id="hq-generator-preview"></div>';
            $return .= '<div class="hq-generator-actions hq-generator-clearfix">' . implode(' ', array_values($actions)) . '</div>';
            set_transient('hq/generator/settings/' . sanitize_text_field($_REQUEST['shortcode']), $return, 2 * DAY_IN_SECONDS);
            echo $return;
        }
        exit;
    }

    /**
     * Process AJAX request and generate preview HTML
     */
    public static function preview() {
        // Check authentication
        self::access();
        // Output results
        do_action('hq/generator/preview/before');
        echo '<h5>' . __('Preview', 'su') . '</h5>';
        // echo '<hr />' . stripslashes( $_POST['shortcode'] ) . '<hr />'; // Uncomment for debug
        echo do_shortcode(str_replace('\"', '"', $_POST['shortcode']));
        echo '<div style="clear:both"></div>';
        do_action('hq/generator/preview/after');
        die();
    }

    public static function access() {
        if (!self::access_check())
            wp_die(__('Access denied', 'su'));
    }

    public static function access_check() {
        $by_role = ( get_option('hq_generator_access') ) ? current_user_can(get_option('hq_generator_access')) : true;
        return current_user_can('edit_posts') && $by_role;
    }

    public static function ajax_get_icons() {
        self::access();
        die(HQ_Tools::icons());
    }

    public static function ajax_get_terms() {
        self::access();
        $args = array();
        if (isset($_REQUEST['tax']))
            $args['options'] = (array) HQ_Tools::get_terms(sanitize_key($_REQUEST['tax']));
        if (isset($_REQUEST['class']))
            $args['class'] = (string) sanitize_key($_REQUEST['class']);
        if (isset($_REQUEST['multiple']))
            $args['multiple'] = (bool) sanitize_key($_REQUEST['multiple']);
        if (isset($_REQUEST['size']))
            $args['size'] = (int) sanitize_key($_REQUEST['size']);
        if (isset($_REQUEST['noselect']))
            $args['noselect'] = (bool) sanitize_key($_REQUEST['noselect']);
        die(HQ_Tools::select($args));
    }

    public static function ajax_get_taxonomies() {
        self::access();
        $args = array();
        $args['options'] = HQ_Tools::get_taxonomies();
        die(HQ_Tools::select($args));
    }

    public static function presets($actions) {
        ob_start();
        ?>
        <div class="hq-generator-presets alignright" data-shortcode="<?php echo sanitize_key($_REQUEST['shortcode']); ?>">
            <a href="javascript:void(0);" class="button button-large hq-gp-button"><i class="fa fa-bars"></i> <?php _e('Presets', 'su'); ?></a>
            <div class="hq-gp-popup">
                <div class="hq-gp-head">
                    <a href="javascript:void(0);" class="button button-small button-primary hq-gp-new"><?php _e('Save current settings as preset', 'su'); ?></a>
                </div>
                <div class="hq-gp-list">
        <?php self::presets_list(); ?>
                </div>
            </div>
        </div>
        <?php
        $actions['presets'] = ob_get_contents();
        ob_end_clean();
        return $actions;
    }

    public static function presets_list($shortcode = false) {
        // Shortcode isn't specified, try to get it from $_REQUEST
        if (!$shortcode)
            $shortcode = $_REQUEST['shortcode'];
        // Shortcode name is still doesn't exists, exit
        if (!$shortcode)
            return;
        // Shortcode has been specified, sanitize it
        $shortcode = sanitize_key($shortcode);
        // Get presets
        $presets = get_option('hq_presets_' . $shortcode);
        // Presets has been found
        if (is_array($presets) && count($presets)) {
            // Print the presets
            foreach ($presets as $preset) {
                echo '<span data-id="' . $preset['id'] . '"><em>' . stripslashes($preset['name']) . '</em> <i class="fa fa-times"></i></span>';
            }
            // Hide default text
            echo sprintf('<b style="display:none">%s</b>', __('Presets not found', 'su'));
        }
        // Presets doesn't found
        else
            echo sprintf('<b>%s</b>', __('Presets not found', 'su'));
    }

    public static function ajax_add_preset() {
        self::access();
        // Check incoming data
        if (empty($_POST['id']))
            return;
        if (empty($_POST['name']))
            return;
        if (empty($_POST['settings']))
            return;
        if (empty($_POST['shortcode']))
            return;
        // Clean-up incoming data
        $id = sanitize_key($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $settings = ( is_array($_POST['settings']) ) ? stripslashes_deep($_POST['settings']) : array();
        $shortcode = sanitize_key($_POST['shortcode']);
        // Prepare option name
        $option = 'hq_presets_' . $shortcode;
        // Get the existing presets
        $current = get_option($option);
        // Create array with new preset
        $new = array(
            'id' => $id,
            'name' => $name,
            'settings' => $settings
        );
        // Add new array to the option value
        if (!is_array($current))
            $current = array();
        $current[$id] = $new;
        // Save updated option
        update_option($option, $current);
        // Clear cache
        delete_transient('hq/generator/settings/' . $shortcode);
    }

    public static function ajax_remove_preset() {
        self::access();
        // Check incoming data
        if (empty($_POST['id']))
            return;
        if (empty($_POST['shortcode']))
            return;
        // Clean-up incoming data
        $id = sanitize_key($_POST['id']);
        $shortcode = sanitize_key($_POST['shortcode']);
        // Prepare option name
        $option = 'hq_presets_' . $shortcode;
        // Get the existing presets
        $current = get_option($option);
        // Check that preset is exists
        if (!is_array($current) || empty($current[$id]))
            return;
        // Remove preset
        unset($current[$id]);
        // Save updated option
        update_option($option, $current);
        // Clear cache
        delete_transient('hq/generator/settings/' . $shortcode);
    }

    public static function ajax_get_preset() {
        self::access();
        // Check incoming data
        if (empty($_GET['id']))
            return;
        if (empty($_GET['shortcode']))
            return;
        // Clean-up incoming data
        $id = sanitize_key($_GET['id']);
        $shortcode = sanitize_key($_GET['shortcode']);
        // Default data
        $data = array();
        // Get the existing presets
        $presets = get_option('hq_presets_' . $shortcode);
        // Check that preset is exists
        if (is_array($presets) && isset($presets[$id]['settings']))
            $data = $presets[$id]['settings'];
        // Print results
        die(json_encode($data));
    }

}

new HQ_Generator;

class Shortcodes_Ultimate_Generator extends HQ_Generator {

    function __construct() {
        parent::__construct();
    }

}
