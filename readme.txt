=== Hyper Security Mate ===
Contributors: ogichanchan
Tags: security, hardening, admin, utility, disable, wp-config, simple
Requires at least: 6.2
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Hyper Security Mate is a unique, PHP-only WordPress utility designed for simplicity and efficiency in securing your site. This lightweight plugin acts as a mate to enhance your WordPress security posture by providing essential hardening features without external files or complex configurations.

Key features include:
*   **Disable XML-RPC:** Prevents common attack vectors by deactivating the XML-RPC interface.
*   **Remove WordPress Version:** Hides your WordPress version number from public view, reducing information exposure to potential attackers.
*   **Disable Theme/Plugin Editor:** Protects your site from unauthorized code modifications by disabling the built-in theme and plugin editors in the WordPress admin dashboard.
*   **Intuitive Settings Page:** Manage all security options from a clean, user-friendly interface within your WordPress admin.
*   **Dashboard Security Status:** Get a quick overview of your site's security measures directly from your WordPress dashboard.
*   **Admin Notices & Tips:** Receive actionable security advice and suggestions directly in your admin area to further strengthen your site's defenses.

Hyper Security Mate focuses on core security enhancements, keeping your WordPress installation lean and secure with minimal overhead.

This plugin is open source. Report bugs at: https://github.com/ogichanchan/hyper-security-mate

== Installation ==
1. Upload `hyper-security-mate` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to 'Settings' > 'Hyper Security Mate' to configure your security options.

== Changelog ==
= 1.0.0 =
* Initial release.
    * Implemented core security features: disable XML-RPC, remove WP version, disable file editor.
    * Added admin settings page for configuration.
    * Included dashboard widget for security status overview.
    * Provided inline admin styles and security tips.
    * Handled activation and deactivation hooks.