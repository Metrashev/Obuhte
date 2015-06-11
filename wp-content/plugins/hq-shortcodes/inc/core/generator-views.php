<?php

/**
 * Shortcode Generator
 */
class HQ_Generator_Views {

    /**
     * Constructor
     */
    function __construct() {
        
    }

    public static function text($id, $field) {
        $field = wp_parse_args($field, array(
            'default' => ''
        ));
        $return = '<input type="text" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr" />';
        return $return;
    }

    public static function textarea($id, $field) {
        $field = wp_parse_args($field, array(
            'rows' => 3,
            'default' => ''
        ));
        $return = '<textarea name="' . $id . '" id="hq-generator-attr-' . $id . '" rows="' . $field['rows'] . '" class="hq-generator-attr">' . esc_textarea($field['default']) . '</textarea>';
        return $return;
    }

    public static function select($id, $field) {
        // Multiple selects
        $multiple = ( isset($field['multiple']) ) ? ' multiple' : '';
        $return = '<select name="' . $id . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr"' . $multiple . '>';
        // Create options
        foreach ($field['values'] as $option_value => $option_title) {
            // Is this option selected
            $selected = ( $field['default'] === $option_value ) ? ' selected="selected"' : '';
            // Create option
            $return .= '<option value="' . $option_value . '"' . $selected . '>' . $option_title . '</option>';
        }
        $return .= '</select>';
        return $return;
    }

    public static function bool($id, $field) {
        $return = '<span class="hq-generator-switch hq-generator-switch-' . $field['default'] . '"><span class="hq-generator-yes">' . __('Yes', 'su') . '</span><span class="hq-generator-no">' . __('No', 'su') . '</span></span><input type="hidden" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr hq-generator-switch-value" />';
        return $return;
    }

    public static function upload($id, $field) {
        $return = '<input type="text" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr hq-generator-upload-value" /><div class="hq-generator-field-actions"><a href="javascript:;" class="button hq-generator-upload-button"><img src="' . admin_url('/images/media-button.png') . '" alt="' . __('Media manager', 'su') . '" />' . __('Media manager', 'su') . '</a></div>';
        return $return;
    }

    public static function icon($id, $field) {
        $return = '<input type="text" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr hq-generator-icon-picker-value" /><div class="hq-generator-field-actions"><a href="javascript:;" class="button hq-generator-upload-button hq-generator-field-action"><img src="' . admin_url('/images/media-button.png') . '" alt="' . __('Media manager', 'su') . '" />' . __('Media manager', 'su') . '</a> <a href="javascript:;" class="button hq-generator-icon-picker-button hq-generator-field-action"><img src="' . admin_url('/images/media-button-other.gif') . '" alt="' . __('Icon picker', 'su') . '" />' . __('Icon picker', 'su') . '</a></div><div class="hq-generator-icon-picker hq-generator-clearfix"><input type="text" class="widefat" placeholder="' . __('Filter icons', 'su') . '" /></div>';
        return $return;
    }

    public static function color($id, $field) {
        $return = '<span class="hq-generator-select-color"><span class="hq-generator-select-color-wheel"></span><input type="text" name="' . $id . '" value="' . $field['default'] . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr hq-generator-select-color-value" /></span>';
        return $return;
    }

    public static function gallery($id, $field) {
        $shult = shortcodes_ultimate();
        // Prepare galleries list
        $galleries = $shult->get_option('galleries');
        $created = ( is_array($galleries) && count($galleries) ) ? true : false;
        $return = '<select name="' . $id . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr" data-loading="' . __('Please wait', 'su') . '">';
        // Check that galleries is set
        if ($created) // Create options
            foreach ($galleries as $g_id => $gallery) {
                // Is this option selected
                $selected = ( $g_id == 0 ) ? ' selected="selected"' : '';
                // Prepare title
                $gallery['name'] = ( $gallery['name'] == '' ) ? __('Untitled gallery', 'su') : stripslashes($gallery['name']);
                // Create option
                $return .= '<option value="' . ( $g_id + 1 ) . '"' . $selected . '>' . $gallery['name'] . '</option>';
            }
        // Galleries not created
        else
            $return .= '<option value="0" selected>' . __('Galleries not found', 'su') . '</option>';
        $return .= '</select><small class="description"><a href="' . $shult->admin_url . '#tab-3" target="_blank">' . __('Manage galleries', 'su') . '</a>&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="hq-generator-reload-galleries">' . __('Reload galleries', 'su') . '</a></small>';
        return $return;
    }

    public static function number($id, $field) {
        $return = '<input type="number" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" min="' . $field['min'] . '" max="' . $field['max'] . '" step="' . $field['step'] . '" class="hq-generator-attr" />';
        return $return;
    }

    public static function slider($id, $field) {
        $return = '<div class="hq-generator-range-picker hq-generator-clearfix"><input type="number" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" min="' . $field['min'] . '" max="' . $field['max'] . '" step="' . $field['step'] . '" class="hq-generator-attr" /></div>';
        return $return;
    }

    public static function shadow($id, $field) {
        $defaults = ( $field['default'] === 'none' ) ? array('0', '0', '0', '#000000') : explode(' ', str_replace('px', '', $field['default']));
        $return = '<div class="hq-generator-shadow-picker"><span class="hq-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[0] . '" class="hq-generator-sp-hoff" /><small>' . __('Horizontal offset', 'su') . ' (px)</small></span><span class="hq-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[1] . '" class="hq-generator-sp-voff" /><small>' . __('Vertical offset', 'su') . ' (px)</small></span><span class="hq-generator-shadow-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[2] . '" class="hq-generator-sp-blur" /><small>' . __('Blur', 'su') . ' (px)</small></span><span class="hq-generator-shadow-picker-field hq-generator-shadow-picker-color"><span class="hq-generator-shadow-picker-color-wheel"></span><input type="text" value="' . $defaults[3] . '" class="hq-generator-shadow-picker-color-value" /><small>' . __('Color', 'su') . '</small></span><input type="hidden" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr" /></div>';
        return $return;
    }

    public static function border($id, $field) {
        $defaults = ( $field['default'] === 'none' ) ? array('0', 'solid', '#000000') : explode(' ', str_replace('px', '', $field['default']));
        $borders = HQ_Tools::select(array(
                    'options' => HQ_Data::borders(),
                    'class' => 'hq-generator-bp-style',
                    'selected' => $defaults[1]
        ));
        $return = '<div class="hq-generator-border-picker"><span class="hq-generator-border-picker-field"><input type="number" min="-1000" max="1000" step="1" value="' . $defaults[0] . '" class="hq-generator-bp-width" /><small>' . __('Border width', 'su') . ' (px)</small></span><span class="hq-generator-border-picker-field">' . $borders . '<small>' . __('Border style', 'su') . '</small></span><span class="hq-generator-border-picker-field hq-generator-border-picker-color"><span class="hq-generator-border-picker-color-wheel"></span><input type="text" value="' . $defaults[2] . '" class="hq-generator-border-picker-color-value" /><small>' . __('Border color', 'su') . '</small></span><input type="hidden" name="' . $id . '" value="' . esc_attr($field['default']) . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr" /></div>';
        return $return;
    }

    public static function image_source($id, $field) {
        $field = wp_parse_args($field, array(
            'default' => 'none'
        ));
        $sources = HQ_Tools::select(array(
                    'options' => array(
                        'media' => __('Media library', 'su'),
                        'posts: recent' => __('Recent posts', 'su'),
                        'category' => __('Category', 'su'),
                        'taxonomy' => __('Taxonomy', 'su')
                    ),
                    'selected' => '0',
                    'none' => __('Select images source', 'su') . '&hellip;',
                    'class' => 'hq-generator-isp-sources'
        ));
        $categories = HQ_Tools::select(array(
                    'options' => HQ_Tools::get_terms('category'),
                    'multiple' => true,
                    'size' => 10,
                    'class' => 'hq-generator-isp-categories'
        ));
        $taxonomies = HQ_Tools::select(array(
                    'options' => HQ_Tools::get_taxonomies(),
                    'none' => __('Select taxonomy', 'su') . '&hellip;',
                    'selected' => '0',
                    'class' => 'hq-generator-isp-taxonomies'
        ));
        $terms = HQ_Tools::select(array(
                    'class' => 'hq-generator-isp-terms',
                    'multiple' => true,
                    'size' => 10,
                    'disabled' => true,
                    'style' => 'display:none'
        ));
        $return = '<div class="hq-generator-isp">' . $sources . '<div class="hq-generator-isp-source hq-generator-isp-source-media"><div class="hq-generator-clearfix"><a href="javascript:;" class="button button-primary hq-generator-isp-add-media"><i class="fa fa-plus"></i>&nbsp;&nbsp;' . __('Add images', 'su') . '</a></div><div class="hq-generator-isp-images hq-generator-clearfix"><em class="description">' . __('Click the button above and select images.<br>You can select multimple images with Ctrl (Cmd) key', 'su') . '</em></div></div><div class="hq-generator-isp-source hq-generator-isp-source-category"><em class="description">' . __('Select categories to retrieve posts from.<br>You can select multiple categories with Ctrl (Cmd) key', 'su') . '</em>' . $categories . '</div><div class="hq-generator-isp-source hq-generator-isp-source-taxonomy"><em class="description">' . __('Select taxonomy and it\'s terms.<br>You can select multiple terms with Ctrl (Cmd) key', 'su') . '</em>' . $taxonomies . $terms . '</div><input type="hidden" name="' . $id . '" value="' . $field['default'] . '" id="hq-generator-attr-' . $id . '" class="hq-generator-attr" /></div>';
        return $return;
    }

}
