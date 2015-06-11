<?php

class HQ_Vote {

    function __construct() {
        return;
        add_action('load-plugins.php', array(__CLASS__, 'init'));
        add_action('wp_ajax_hq_vote', array(__CLASS__, 'vote'));
    }

    public static function init() {
        Shortcodes_Ultimate::timestamp();
        $vote = get_option('hq_vote');
        $timeout = time() > ( get_option('hq_installed') + 60 * 60 * 24 * 3 );
        if (in_array($vote, array('yes', 'no', 'tweet')) || !$timeout)
            return;
        add_action('in_admin_footer', array(__CLASS__, 'message'));
        add_action('admin_head', array(__CLASS__, 'register'));
        add_action('admin_footer', array(__CLASS__, 'enqueue'));
    }

    public static function register() {
        wp_register_style('hq-vote', plugins_url('assets/css/vote.css', HQ_PLUGIN_FILE), false, HQ_PLUGIN_VERSION, 'all');
        wp_register_script('hq-vote', plugins_url('assets/js/vote.js', HQ_PLUGIN_FILE), array('jquery'), HQ_PLUGIN_VERSION, true);
    }

    public static function enqueue() {
        wp_enqueue_style('hq-vote');
        wp_enqueue_script('hq-vote');
    }

    public static function vote() {
        $vote = sanitize_key($_GET['vote']);
        if (!is_user_logged_in() || !in_array($vote, array('yes', 'no', 'later', 'tweet')))
            die('error');
        update_option('hq_vote', $vote);
        if ($vote === 'later')
            update_option('hq_installed', time());
        die('OK: ' . $vote);
    }

    public static function message() {
        ?>
        <div class="hq-vote" style="display:none">
            <div class="hq-vote-wrap">
                <div class="hq-vote-gravatar"><a href="http://profiles.wordpress.org/gn_themes" target="_blank"><img src="http://www.gravatar.com/avatar/54fda46c150e45d18d105b9185017aea.png" alt="<?php _e('Vladimir Anokhin', 'su'); ?>" width="50" height="50"></a></div>
                <div class="hq-vote-message">
                    <p><?php _e('Hello, my name is Vladimir Anokhin, and I am developer of plugin <b>Shortcodes Ultimate</b>.<br>If you like this plugin, please write a few words about it at the wordpress.org or twitter. It will help other people find this useful plugin more quickly.<br><b>Thank you!</b>', 'su'); ?></p>
                    <p>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=hq_vote&amp;vote=yes" class="hq-vote-action button button-small button-primary" data-action="http://wordpress.org/support/view/plugin-reviews/shortcodes-ultimate?rate=5#postform"><?php _e('Rate plugin', 'su'); ?></a>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=hq_vote&amp;vote=tweet" class="hq-vote-action button button-small" data-action="http://twitter.com/share?url=http://bit.ly/1blZb7u&amp;text=<?php echo urlencode(__('Shortcodes Ultimate - must have WordPress plugin #shortcodesultimate', 'su')); ?>"><?php _e('Tweet', 'su'); ?></a>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=hq_vote&amp;vote=no" class="hq-vote-action button button-small"><?php _e('No, thanks', 'su'); ?></a>
                        <span><?php _e('or', 'su'); ?></span>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=hq_vote&amp;vote=later" class="hq-vote-action button button-small"><?php _e('Remind me later', 'su'); ?></a>
                    </p>
                </div>
                <div class="hq-vote-clear"></div>
            </div>
        </div>
        <?php
    }

}

new HQ_Vote;
