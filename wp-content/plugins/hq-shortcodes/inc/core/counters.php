<?php

class HQ_Counter_Extra_Addon {

    static $option = 'hq_counter_extra_addon';

    function __construct() {
        add_filter('hq/menu/shortcodes', array(__CLASS__, 'display'));
        add_filter('hq/menu/addons', array(__CLASS__, 'display'));
        add_action('sunrise/page/before', array(__CLASS__, 'disable'));
    }

    public static function display($title) {
        if (get_option(self::$option))
            return $title;
        return sprintf(
                '%s <span class="update-plugins count-1" title="%s"><span class="update-count">%s</span></span>', $title, __('1 new add-on for Shortcodes Ultimate', 'su'), '1'
        );
    }

    public static function disable() {
        if ($_GET['page'] === 'shortcodes-ultimate-addons')
            update_option(self::$option, true);
    }

}

// new HQ_Counter_Extra_Addon;

class HQ_Counter_Bundle {

    static $option = 'hq_counter_bundle';

    function __construct() {
        add_filter('hq/menu/shortcodes', array(__CLASS__, 'display'));
        add_filter('hq/menu/addons', array(__CLASS__, 'display'));
        add_action('sunrise/page/before', array(__CLASS__, 'disable'));
    }

    public static function display($title) {
        if (get_option(self::$option))
            return $title;
        return sprintf(
                '%s <span class="update-plugins count-1" title="%s"><span class="update-count">%s</span></span>', $title, __('1 new add-on for Shortcodes Ultimate', 'su'), '1'
        );
    }

    public static function disable() {
        if ($_GET['page'] === 'shortcodes-ultimate-addons')
            update_option(self::$option, true);
    }

}

// new HQ_Counter_Bundle;
