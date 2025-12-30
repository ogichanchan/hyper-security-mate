<?php
/**
 * Plugin Name: Hyper Security Mate
 * Plugin URI: https://github.com/ogichanchan/hyper-security-mate
 * Description: A unique PHP-only WordPress utility. A hyper style security plugin acting as a mate. Focused on simplicity and efficiency.
 * Version: 1.0.0
 * Author: ogichanchan
 * Author URI: https://github.com/ogichanchan
 * License: GPLv2 or later
 * Text Domain: hyper-security-mate
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The main plugin class for Hyper Security Mate.
 * Handles plugin setup, settings, and core security features.
 */
class HSM_Hyper_Security_Mate {

    /**
     * Constructor.
     * Registers all necessary hooks and filters.
     */
    public function __construct() {
        // Core plugin setup hooks.
        add_action( 'plugins_loaded', array( $this, 'hsm_setup_plugin' ) );

        // Admin functionality hooks.
        add_action( 'admin_init', array( $this, 'hsm_register_settings' ) );
        add_action( 'admin_menu', array( $this, 'hsm_add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'hsm_admin_inline_styles' ) );
        add_action( 'admin_notices', array( $this, 'hsm_show_admin_notices' ) );
        add_action( 'dashboard_setup', array( $this, 'hsm_add_dashboard_widgets' ) );

        // Apply security filters based on stored options.
        add_action( 'init', array( $this, 'hsm_apply_security_filters' ) );
    }

    /**
     * Set up plugin locale and other initializations.
     * WordPress automatically handles `load_plugin_textdomain` for the main plugin file
     * if the 'Text Domain' header is present and translations are in the standard location.
     */
    public function hsm_setup_plugin() {
        // No specific action needed here for text domain as per requirement #4.
    }

    /**
     * Register plugin settings for our options page.
     */
    public function hsm_register_settings() {
        register_setting(
            'hsm_security_options', // Option group
            'hsm_disable_xmlrpc',   // Option name
            array(
                'type'              => 'boolean',
                'sanitize_callback' => array( $this, 'hsm_sanitize_checkbox' ),
                'default'           => true, // Default to enabled.
            )
        );
        register_setting(
            'hsm_security_options',
            'hsm_remove_wp_version',
            array(
                'type'              => 'boolean',
                'sanitize_callback' => array( $this, 'hsm_sanitize_checkbox' ),
                'default'           => true, // Default to enabled.
            )
        );
        register_setting(
            'hsm_security_options',
            'hsm_disable_file_edit',
            array(
                'type'              => 'boolean',
                'sanitize_callback' => array( $this, 'hsm_sanitize_checkbox' ),
                'default'           => true, // Default to enabled.
            )
        );
    }

    /**
     * Sanitize checkbox input to a boolean.
     *
     * @param string $input The input value from the checkbox.
     * @return bool True if input is '1' or 'true', false otherwise.
     */
    public function hsm_sanitize_checkbox( $input ) {
        return ( '1' === $input || 'true' === $input ) ? true : false;
    }

    /**
     * Add the plugin's settings page to the WordPress admin menu.
     */
    public function hsm_add_admin_menu() {
        add_options_page(
            esc_html__( 'Hyper Security Mate Settings', 'hyper-security-mate' ), // Page title
            esc_html__( 'Hyper Security Mate', 'hyper-security-mate' ),          // Menu title
            'manage_options',                                                    // Capability required to access
            'hyper-security-mate',                                               // Menu slug
            array( $this, 'hsm_settings_page_html' )                             // Callback function to render content
        );
    }

    /**
     * Renders the HTML content for the plugin's settings page.
     */
    public function hsm_settings_page_html() {
        // Check user capabilities.
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Display settings errors/updates.
        settings_errors( 'hsm_security_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // Output security fields for the registered setting.
                settings_fields( 'hsm_security_options' );
                // Output settings sections (though we don't have explicit sections,
                // this is needed for the settings fields to appear).
                do_settings_sections( 'hsm_security_options' );
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Disable XML-RPC', 'hyper-security-mate' ); ?></th>
                        <td>
                            <label for="hsm_disable_xmlrpc">
                                <input type="checkbox" id="hsm_disable_xmlrpc" name="hsm_disable_xmlrpc" value="1" <?php checked( true, get_option( 'hsm_disable_xmlrpc', true ) ); ?> />
                                <?php esc_html_e( 'Prevents XML-RPC access, which can be a common attack vector.', 'hyper-security-mate' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Remove WordPress Version', 'hyper-security-mate' ); ?></th>
                        <td>
                            <label for="hsm_remove_wp_version">
                                <input type="checkbox" id="hsm_remove_wp_version" name="hsm_remove_wp_version" value="1" <?php checked( true, get_option( 'hsm_remove_wp_version', true ) ); ?> />
                                <?php esc_html_e( 'Hides the WordPress version number from public view (head, RSS feeds).', 'hyper-security-mate' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Disable Theme/Plugin Editor', 'hyper-security-mate' ); ?></th>
                        <td>
                            <label for="hsm_disable_file_edit">
                                <input type="checkbox" id="hsm_disable_file_edit" name="hsm_disable_file_edit" value="1" <?php checked( true, get_option( 'hsm_disable_file_edit', true ) ); ?> />
                                <?php esc_html_e( 'Prevents editing of theme and plugin files from the WordPress admin dashboard.', 'hyper-security-mate' ); ?>
                                <p class="description"><?php esc_html_e( 'Note: This setting can be stronger if defined directly in your wp-config.php file (e.g., ', 'hyper-security-mate' ); ?><code>define( 'DISALLOW_FILE_EDIT', true );</code><?php esc_html_e( ').', 'hyper-security-mate' ); ?></p>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button( esc_html__( 'Save Changes', 'hyper-security-mate' ) ); ?>
            </form>

            <div class="hsm-card hsm-card-info">
                <h2><?php esc_html_e( 'Hyper Security Tips', 'hyper-security-mate' ); ?></h2>
                <ul>
                    <li><strong><?php esc_html_e( 'Strong Passwords:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Always use long, complex, and unique passwords for all user accounts, especially administrators.', 'hyper-security-mate' ); ?></li>
                    <li><strong><?php esc_html_e( 'Regular Backups:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Implement a consistent and reliable backup strategy for your entire WordPress site.', 'hyper-security-mate' ); ?></li>
                    <li><strong><?php esc_html_e( 'Keep Everything Updated:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Ensure WordPress core, all themes, and plugins are updated to their latest versions promptly.', 'hyper-security-mate' ); ?></li>
                    <li><strong><?php esc_html_e( 'Limit Login Attempts:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Consider using a security plugin to limit the number of failed login attempts, thwarting brute-force attacks.', 'hyper-security-mate' ); ?></li>
                    <li><strong><?php esc_html_e( 'Use HTTPS:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Always secure your website with an SSL/TLS certificate (HTTPS) to encrypt data transmission.', 'hyper-security-mate' ); ?></li>
                    <li><strong><?php esc_html_e( 'Two-Factor Authentication:', 'hyper-security-mate' ); ?></strong> <?php esc_html_e( 'Enable 2FA for all administrator accounts for an extra layer of security.', 'hyper-security-mate' ); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Adds inline styles to the admin pages where the plugin UI is displayed.
     * This adheres to the "PHP ONLY" and "NO EXTERNAL FILES" requirements.
     */
    public function hsm_admin_inline_styles() {
        $screen = get_current_screen();
        // Only output styles on our settings page or the dashboard.
        if ( 'settings_page_hyper-security-mate' === $screen->id || 'dashboard' === $screen->id ) {
            ?>
            <style type="text/css">
                /* Hyper Security Mate specific styles */
                .hsm-card {
                    background: #fff;
                    border-left: 4px solid #007cba;
                    box-shadow: 0 1px 1px rgba(0,0,0,.04);
                    margin-top: 20px;
                    padding: 1px 12px;
                }
                .hsm-card-success {
                    border-left-color: #46b450;
                }
                .hsm-card-warning {
                    border-left-color: #ffb900;
                }
                .hsm-card-error {
                    border-left-color: #dc3232;
                }
                .hsm-card-info {
                    border-left-color: #007cba;
                }
                .hsm-card h2 {
                    margin-top: 10px;
                    margin-bottom: 5px;
                    font-size: 1.2em;
                    color: #23282d;
                }
                .hsm-card ul {
                    list-style-type: disc;
                    padding-left: 20px;
                    margin-top: 0;
                    margin-bottom: 10px;
                }
                .hsm-card li {
                    margin-bottom: 5px;
                }
                .hsm-dashboard-widget h4 {
                    margin-bottom: 5px;
                }
                .hsm-dashboard-widget ul {
                    margin-top: 0;
                    margin-bottom: 10px;
                    list-style: none;
                    padding-left: 0;
                }
                .hsm-dashboard-widget ul li {
                    padding: 5px 0;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .hsm-dashboard-widget ul li:last-child {
                    border-bottom: none;
                }
                .hsm-status-ok::before {
                    content: "\f147"; /* Dashicon: yes */
                    font-family: 'dashicons';
                    vertical-align: middle;
                    margin-right: 5px;
                    color: #46b450;
                }
                .hsm-status-warning::before {
                    content: "\f534"; /* Dashicon: warning */
                    font-family: 'dashicons';
                    vertical-align: middle;
                    margin-right: 5px;
                    color: #ffb900;
                }
                .hsm-status-error::before {
                    content: "\f153"; /* Dashicon: no */
                    font-family: 'dashicons';
                    vertical-align: middle;
                    margin-right: 5px;
                    color: #dc3232;
                }
                .hsm-status-info::before {
                    content: "\f348"; /* Dashicon: info */
                    font-family: 'dashicons';
                    vertical-align: middle;
                    margin-right: 5px;
                    color: #007cba;
                }
            </style>
            <?php
        }
    }

    /**
     * Displays admin notices for security suggestions or warnings.
     */
    public function hsm_show_admin_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Suggest stronger file editor disable if not already in wp-config.php.
        if ( get_option( 'hsm_disable_file_edit', true ) && ! ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'Hyper Security Mate:', 'hyper-security-mate' ); ?></strong>
                    <?php esc_html_e( 'For enhanced security, consider adding ', 'hyper-security-mate' ); ?>
                    <code>define( 'DISALLOW_FILE_EDIT', true );</code>
                    <?php esc_html_e( ' to your ', 'hyper-security-mate' ); ?>
                    <code>wp-config.php</code>
                    <?php esc_html_e( ' file. This provides a more robust way to disable the theme and plugin editor.', 'hyper-security-mate' ); ?>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=hyper-security-mate' ) ); ?>"><?php esc_html_e( 'Manage settings', 'hyper-security-mate' ); ?></a>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Adds the Hyper Security Mate dashboard widget.
     */
    public function hsm_add_dashboard_widgets() {
        wp_add_dashboard_widget(
            'hsm_security_status_widget',
            esc_html__( 'Hyper Security Mate Status', 'hyper-security-mate' ),
            array( $this, 'hsm_dashboard_widget_content' )
        );
    }

    /**
     * Renders the content for the Hyper Security Mate dashboard widget.
     */
    public function hsm_dashboard_widget_content() {
        ?>
        <div class="hsm-dashboard-widget">
            <h4><?php esc_html_e( 'Current Security Checks:', 'hyper-security-mate' ); ?></h4>
            <ul>
                <li>
                    <span class="hsm-status-<?php echo get_option( 'hsm_disable_xmlrpc', true ) ? 'ok' : 'warning'; ?>">
                        <?php esc_html_e( 'XML-RPC Disabled', 'hyper-security-mate' ); ?>
                    </span>
                    <span><?php echo get_option( 'hsm_disable_xmlrpc', true ) ? esc_html__( 'Active', 'hyper-security-mate' ) : esc_html__( 'Inactive', 'hyper-security-mate' ); ?></span>
                </li>
                <li>
                    <span class="hsm-status-<?php echo get_option( 'hsm_remove_wp_version', true ) ? 'ok' : 'warning'; ?>">
                        <?php esc_html_e( 'WP Version Hidden', 'hyper-security-mate' ); ?>
                    </span>
                    <span><?php echo get_option( 'hsm_remove_wp_version', true ) ? esc_html__( 'Active', 'hyper-security-mate' ) : esc_html__( 'Inactive', 'hyper-security-mate' ); ?></span>
                </li>
                <li>
                    <span class="hsm-status-<?php echo ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ? 'ok' : ( get_option( 'hsm_disable_file_edit', true ) ? 'ok' : 'warning' ); ?>">
                        <?php esc_html_e( 'File Editor Disabled', 'hyper-security-mate' ); ?>
                    </span>
                    <span>
                        <?php
                        if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
                            esc_html_e( 'Active (wp-config.php)', 'hyper-security-mate' );
                        } elseif ( get_option( 'hsm_disable_file_edit', true ) ) {
                            esc_html_e( 'Active (plugin setting)', 'hyper-security-mate' );
                        } else {
                            esc_html_e( 'Inactive', 'hyper-security-mate' );
                        }
                        ?>
                    </span>
                </li>
                <li>
                    <span class="hsm-status-<?php echo ( is_ssl() ) ? 'ok' : 'warning'; ?>">
                        <?php esc_html_e( 'HTTPS/SSL Active', 'hyper-security-mate' ); ?>
                    </span>
                    <span><?php echo ( is_ssl() ) ? esc_html__( 'Yes', 'hyper-security-mate' ) : esc_html__( 'No', 'hyper-security-mate' ); ?></span>
                </li>
            </ul>
            <p><a href="<?php echo esc_url( admin_url( 'options-general.php?page=hyper-security-mate' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'View Settings', 'hyper-security-mate' ); ?></a></p>
        </div>
        <?php
    }

    /**
     * Applies security filters based on the current plugin settings.
     * This method is hooked to 'init' to ensure settings are loaded.
     */
    public function hsm_apply_security_filters() {
        // Disable XML-RPC if the option is active.
        if ( get_option( 'hsm_disable_xmlrpc', true ) ) {
            add_filter( 'xmlrpc_enabled', '__return_false' );
        }

        // Remove WordPress version if the option is active.
        if ( get_option( 'hsm_remove_wp_version', true ) ) {
            remove_action( 'wp_head', 'wp_generator' ); // From the <head>
            add_filter( 'the_generator', '__return_empty_string' ); // From RSS feeds
        }

        // Disable theme/plugin file editor if the option is active AND
        // it's not already forcefully disabled by wp-config.php.
        if ( get_option( 'hsm_disable_file_edit', true ) && ! ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ) {
            add_filter( 'user_can_edit_files', '__return_false' );
        }
    }
}

/**
 * Instantiates the Hyper Security Mate plugin class.
 * This is the entry point for the plugin's execution.
 */
function hsm_run_hyper_security_mate() {
    new HSM_Hyper_Security_Mate();
}
add_action( 'plugins_loaded', 'hsm_run_hyper_security_mate' );


/**
 * Handles plugin activation tasks.
 * Sets default options to true (active) upon initial activation.
 */
function hsm_activate_plugin() {
    // Set default options if they don't already exist.
    // 'add_option' will only add if the option doesn't exist.
    add_option( 'hsm_disable_xmlrpc', true );
    add_option( 'hsm_remove_wp_version', true );
    add_option( 'hsm_disable_file_edit', true );
}
register_activation_hook( __FILE__, 'hsm_activate_plugin' );

/**
 * Handles plugin deactivation tasks.
 * No specific cleanup is needed for simple boolean options; WordPress handles hook removal.
 */
function hsm_deactivate_plugin() {
    // No specific cleanup needed for these options upon deactivation.
    // WordPress handles the removal of registered hooks automatically.
}
register_deactivation_hook( __FILE__, 'hsm_deactivate_plugin' );