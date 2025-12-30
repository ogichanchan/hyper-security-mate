1. Plugin Name: Hyper Security Mate
2. Short Description: A unique PHP-only WordPress utility. A hyper style security plugin acting as a mate. Focused on simplicity and efficiency.
3. Detailed Description:
Hyper Security Mate is a lightweight, PHP-only security plugin for WordPress, designed for simplicity and efficiency. It offers essential hardening features through an intuitive admin interface without relying on external files (all CSS is inline).

Key Features:
*   **Disable XML-RPC:** Easily disable XML-RPC functionality, which can be a common attack vector, directly from the plugin settings.
*   **Remove WordPress Version:** Hides your WordPress version number from public view (head and RSS feeds) to prevent attackers from identifying potential vulnerabilities based on your WordPress version.
*   **Disable Theme/Plugin Editor:** Prevents editing of theme and plugin files from the WordPress admin dashboard, adding a crucial layer of security. The plugin also provides an admin notice recommending the use of `define( 'DISALLOW_FILE_EDIT', true );` in `wp-config.php` for stronger protection.

User Experience:
*   **Admin Settings Page:** A dedicated settings page found under "Settings > Hyper Security Mate" allows you to toggle the security features on or off. This page also includes valuable "Hyper Security Tips" for overall site protection.
*   **Dashboard Widget:** A "Hyper Security Mate Status" widget is added to your WordPress dashboard, offering a quick overview of active security measures, including the status of XML-RPC, WordPress version visibility, file editor status (distinguishing between plugin setting and `wp-config.php` constant), and HTTPS/SSL usage.
*   **Admin Notices:** Informative notices guide users toward implementing stronger security practices, such as using `wp-config.php` for file editor disabling.
*   **Self-Contained:** The plugin is entirely PHP-based, with all styling and functionality embedded directly in the PHP code, ensuring a streamlined and efficient footprint without external CSS or JavaScript files.

Upon activation, all core security features (Disable XML-RPC, Remove WordPress Version, Disable Theme/Plugin Editor) are enabled by default, providing immediate protection.

4. GitHub URL: https://github.com/ogichanchan/hyper-security-mate