<?php

/*
Plugin Name: Bloghosting (Module: Premium Themes)
*/

class ProSites_Module_PremiumThemes {

    static $user_label;
    static $user_description;

    public static function get_name() {
        return __('Premium Themes', 'psts');
    }

    public static function get_description() {
        return __('Ermöglicht den Zugriff auf ausgewählte Themes auf Bloghosting-Ebene.', 'psts');
    }

    public function __construct() {
        add_action('psts_page_after_modules', array($this, 'plug_network_page'));

        if (is_admin() || !is_main_site(get_current_blog_id())) {
            add_action('psts_withdraw', array($this, 'deactivate_theme'));
            add_action('psts_downgrade', array($this, 'deactivate_theme'));

            add_action('admin_enqueue_scripts', array($this, 'themes_styles'));
            add_action('admin_footer', array($this, 'themes_scripts'));

            add_action('customize_controls_print_footer_scripts', array($this, 'customize_controls_print_footer_scripts'));

            add_filter('theme_action_links', array($this, 'theme_action_links'), 100, 2);
            add_filter('wp_prepare_themes_for_js', array($this, 'theme_action_links_js'), 100);

            add_filter('site_option_allowedthemes', array($this, 'site_option_allowedthemes'), 100);
        }

        self::$user_label = __('Premium Themes', 'psts');
        self::$user_description = __('Beinhaltet den Zugriff auf Premium-Themes', 'psts');
    }

    public function plug_network_page() {
        $page = add_submenu_page('psts', __('Bloghosting Premium Themes', 'psts'), __('Premium Themes', 'psts'), 'manage_network_options', 'psts-themes', array($this, 'admin_page'));
    }

    public function theme_action_links($actions, $theme) {
        global $psts, $blog_id;

        if (is_network_admin()) {
            return $actions;
        }

        $ct = wp_get_theme();

        $allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        if (empty($allowed_themes)) {
            $allowed_themes = array();
        }

        $override_themes = get_option('allowedthemes', array());
        if (is_array($override_themes) && isset($override_themes[$theme['Stylesheet']])) {
            return $actions;
        }

        if (!isset($allowed_themes[esc_html($ct->stylesheet)]) || !$allowed_themes[esc_html($ct->stylesheet)]) {
            $allowed_themes[esc_html($ct->stylesheet)] = true;
        }

        if (isset($allowed_themes[esc_html($theme['Stylesheet'])]) && $allowed_themes[esc_html($theme['Stylesheet'])] &&
            !is_pro_site($blog_id, $allowed_themes[$theme['Stylesheet']]) && !$this->ads_theme()
        ) {
            $rebrand = sprintf(__('Nur für %s', 'psts'), $psts->get_level_setting($allowed_themes[$theme['Stylesheet']], 'name'));
            $upgrade_notice = str_replace('LEVEL', $psts->get_level_setting($allowed_themes[$theme['Stylesheet']], 'name'), $psts->get_setting('pt_text'));
            $actions['activate'] = '<a href="' . $psts->checkout_url($blog_id) . '" class="activatelink nonpsts" data-level="' . $allowed_themes[$theme['Stylesheet']] . '" title="' . esc_attr($upgrade_notice) . '">' . $rebrand . '</a>';
        }

        return $actions;
    }

    public function site_option_allowedthemes($themes) {
        global $psts;

        if (is_network_admin()) {
            return $themes;
        }

        $blog_id = get_current_blog_id();

        $visible_pro_only = apply_filters('prosites_show_themes_prosites_only', false, is_pro_site(get_current_blog_id()));

        if ($visible_pro_only || (defined('PSTS_THEMES_PRO_ONLY') && PSTS_THEMES_PRO_ONLY === true)) {
            update_blog_option($blog_id, 'psts_blog_allowed_themes', $themes);
            return $themes;
        }

        $allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        if (empty($allowed_themes)) {
            $allowed_themes = array();
        }

        if (!is_array($themes)) {
            $themes = array();
        }

        foreach ($allowed_themes as $key => $allowed_theme) {
            $themes[$key] = $allowed_theme;
        }

        update_blog_option($blog_id, 'psts_blog_allowed_themes', $themes);

        return $themes;
    }

    public function themes_styles() {
        echo '<style type="text/css">
            a.nonpsts {color:red;}
            div.level-1 a.screenshot {box-shadow: 0 43px 30px -30px #EAFFEF;}
            div.level-2 a.screenshot {box-shadow: 0 43px 30px -30px #E6FCFF;}
            div.level-3 a.screenshot {box-shadow: 0 43px 30px -30px #EEEEFF;}
            div.level-4 a.screenshot {box-shadow: 0 43px 30px -30px #FCFCE9;}
            div.level-5 a.screenshot {box-shadow: 0 43px 30px -30px #FFECFF;}
            div.level-6 a.screenshot {box-shadow: 0 43px 30px -30px #DBF0F7;}
            div.level-7 a.screenshot {box-shadow: 0 43px 30px -30px #FFECEC;}
        </style>';
    }

    public function theme_action_links_js($prepared_themes) {
        global $psts, $blog_id;

        $allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        if (empty($allowed_themes)) {
            $allowed_themes = array();
        }

        $override_themes = get_option('allowedthemes', array());

        foreach ($prepared_themes as $slug => $theme) {
            if (isset($override_themes[$slug])) {
                continue;
            }

            if ($theme['active']) {
                continue;
            }

            if (isset($allowed_themes[$slug]) && $allowed_themes[$slug] &&
                !is_pro_site($blog_id, $allowed_themes[$slug]) && !$this->ads_theme()
            ) {
                $rebrand = sprintf(__('Nur für %s', 'psts'), $psts->get_level_setting($allowed_themes[$slug], 'name'));
                $upgrade_notice = str_replace('LEVEL', $psts->get_level_setting($allowed_themes[$slug], 'name'), $psts->get_setting('pt_text'));
                $prepared_themes[$slug]['actions']['activate'] = '<a href="' . $psts->checkout_url($blog_id) . '" class="button button-secondary activate nonpsts" data-level="' . $allowed_themes[$slug] . '" title="' . esc_attr($upgrade_notice) . '">' . $rebrand . '</a>';
            }
        }

        return $prepared_themes;
    }

    public function themes_scripts() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var specialThemes = jQuery("a[data-level]");
                jQuery.each(specialThemes, function (index, value) {
                    jQuery(value).parents(".available-theme").addClass("level-" + jQuery(value).attr('data-level'));
                });
            });
        </script>
        <?php
    }

    public function deactivate_theme($blog_id) {
        global $psts;

        $current_theme = get_blog_option($blog_id, 'stylesheet');
        $psts_allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        $blog_allowed_themes = get_blog_option($blog_id, 'psts_blog_allowed_themes', array());

        $is_pro_site = is_pro_site($blog_id);

        if (!$is_pro_site) {
            foreach ($psts_allowed_themes as $key => $value) {
                if (isset($blog_allowed_themes[$key])) {
                    unset($blog_allowed_themes[$key]);
                }
            }
            update_blog_option($blog_id, 'psts_blog_allowed_themes', $blog_allowed_themes);
        }

        if (!isset($psts_allowed_themes[$current_theme])) {
            return;
        }

        if (is_pro_site($blog_id, $psts_allowed_themes[$current_theme]) || $this->ads_theme()) {
            return;
        }

        if (function_exists('default_theme_switch_theme')) {
            default_theme_switch_theme($blog_id);
        } else {
            switch_to_blog($blog_id);

            if (defined('WP_DEFAULT_THEME') && !empty($blog_allowed_themes[WP_DEFAULT_THEME])) {
                switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
            } else {
                $theme = key($blog_allowed_themes);
                switch_theme($theme, $theme);
            }

            restore_current_blog();
        }
    }

    public function ads_theme() {
        global $psts;

        if (function_exists('psts_hide_ads') && $psts->get_setting('ads_themes') && psts_hide_ads()) {
            return true;
        } else {
            return false;
        }
    }

    public function settings() {
        global $psts;
        ?>
        <h3><?php _e('Premium Themes', 'psts') ?></h3>
        <span class="description"><?php _e('Ermöglicht den Zugriff auf ausgewählte Themes auf Bloghosting-Ebene.', 'psts') ?></span>

        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="psts-help-div psts-rename-feature">
                    <?php echo esc_html__('Funktion umbenennen', 'psts') . $psts->help_text(esc_html__('Erforderlich - Kein HTML! - Mach das kurz und bündig.', 'psts')); ?>
                </th>
                <td>
				<input type="text" name="psts[pt_name]" value="<?php echo esc_attr($psts->get_setting('pt_name', esc_html__('Premium Themes', 'psts'))); ?>" size="30"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="psts-help-div psts-theme-preview">
                    <?php echo esc_html__('Themevorschau-Nachricht', 'psts') . $psts->help_text(esc_html__('Erforderlich - Kein HTML! - Diese Meldung wird angezeigt, wenn auf der Webseite der falschen Ebene eine Vorschau eines Premium-Themas angezeigt wird. "LEVEL" wird durch den erforderlichen Levelnamen für dieses Thema ersetzt.', 'psts')); ?>
                </th>
                <td>
                    <input type="text" name="psts[pt_text]" value="<?php echo esc_attr($psts->get_setting('pt_text', esc_html__('Aktualisiere auf LEVEL, um dieses Premium-Theme &raquo zu aktivieren;', 'psts'))); ?>" style="width: 95%"/>
                </td>
            </tr>
        </table>
        <?php
    }

    public function admin_page() {
        global $psts;

        if (isset($_POST['save_themes'])) {
            check_admin_referer('psts_themes');

            $psts_allowed_themes = array();

            if (is_array($_POST['theme'])) {
                foreach ($_POST['theme'] as $theme => $value) {
                    if ($value) {
                        $psts_allowed_themes[$theme] = $value;
                    }
                }
                $psts->update_setting('pt_allowed_themes', $psts_allowed_themes);
            } else {
                $psts->update_setting('pt_allowed_themes', array(0));
            }

            echo '<div id="message" class="updated fade"><p>' . esc_html__('Einstellungen gespeichert!', 'psts') . '</p></div>';
        }

        $themes = wp_get_themes();
        $psts_allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        $allowed_themes = get_site_option("allowedthemes", array());
        $levels = (array) get_site_option('psts_levels');
        ?>
        <div class="wrap">
            <h1><?php _e('Premium Themes', 'psts'); ?></h1>

            <p><?php _e('Wähle die Mindest-Bloghosting-Stufe für Premium-Themes aus, die Du für Webseiten dieser Stufe oder höher aktivieren möchtest. In dieser Liste werden nur <a href="themes.php?theme_status=disabled">Netzwerkdeaktvierte Themes</a> angezeigt.', 'psts'); ?></p>

            <form method="post" action="">
                <?php wp_nonce_field('psts_themes') ?>

                <table class="widefat">
                    <thead>
                    <tr>
                        <th style="width:15%;"><?php _e('Minimum Level', 'psts') ?></th>
                        <th style="width:25%;"><?php _e('Theme', 'psts') ?></th>
                        <th style="width:10%;"><?php _e('Version', 'psts') ?></th>
                        <th style="width:60%;"><?php _e('Beschreibung', 'psts') ?></th>
                    </tr>
                    </thead>
                    <tbody id="plugins">
                    <?php
                    $class = '';
                    foreach ((array) $themes as $key => $theme) {
                        $theme_key = esc_html($theme['Stylesheet']);
                        $class = ($class === 'alt') ? '' : 'alt';

                        if (!isset($allowed_themes[$theme_key])) {
                            ?>
                            <tr valign="top" class="<?php echo esc_attr($class); ?>">
                                <td>
                                    <select name="theme[<?php echo esc_attr($theme_key) ?>]">
                                        <option value="0"><?php _e('Keines', 'psts') ?></option>
                                        <?php
                                        foreach ($levels as $key => $value) {
                                            ?>
                                            <option value="<?php echo esc_attr($key); ?>"<?php selected(@$psts_allowed_themes[$theme_key], $key) ?>><?php echo esc_html($key) . ': ' . esc_attr($value['name']); ?></option><?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><?php echo esc_html($theme['Name']); ?></td>
                                <td><?php echo esc_html($theme['Version']); ?></td>
                                <td><?php echo esc_html($theme['Description']); ?></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="save_themes" class="button-primary" value="<?php _e('Änderungen speichern', 'psts') ?>"/>
                </p>
            </form>
        </div>
        <?php
    }

    public function customize_controls_print_footer_scripts() {
        global $psts, $blog_id;

        $theme = wp_get_theme($_REQUEST['theme']);

        $allowed_themes = $psts->get_setting('pt_allowed_themes', array());

        if (isset($allowed_themes[esc_html($theme['Stylesheet'])]) && $allowed_themes[esc_html($theme['Stylesheet'])] && !is_pro_site($blog_id, $allowed_themes[$theme['Stylesheet']]) && !$this->ads_theme()) {
            $rebrand = sprintf(__('Nur für %s', 'psts'), $psts->get_level_setting($allowed_themes[$theme['Stylesheet']], 'name'));
            $upgrade_notice = str_replace('LEVEL', $psts->get_level_setting($allowed_themes[$theme['Stylesheet']], 'name'), $psts->get_setting('pt_text'));
            $upgrade_link = '<a href="' . $psts->checkout_url($blog_id) . '" target="_parent" class="activatelink nonpsts button-primary" title="' . esc_attr($upgrade_notice) . '">' . $rebrand . '</a>';
            ?>
            <script type="text/javascript">
                jQuery('#save').remove();
                jQuery('#customize-header-actions').prepend('<?php echo $upgrade_link; ?>');
            </script>
            <?php
        }
    }

    public static function is_included($level_id) {
        switch ($level_id) {
            default:
                return false;
        }
    }

    public static function required_level() {
        global $psts;

        $levels = (array) get_site_option('psts_levels');

        return !empty($levels) ? key($levels) : false;

    }

    public static function get_level_status($level_id) {
        global $psts;

        $allowed_themes = $psts->get_setting('pt_allowed_themes', array());
        $access = false;

        if (!empty($allowed_themes) && count($allowed_themes) > 0) {
            foreach ($allowed_themes as $theme => $level) {
                if ($level_id == $level) {
                    $access = true;
                }
            }
        }

        if ($access) {
            return 'tick';
        } else {
            return 'cross';
        }
    }
}
