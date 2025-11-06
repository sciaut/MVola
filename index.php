<?php
/*
Plugin Name: Mvola Sandbox Payment
Plugin URI: https://www.mvola.mg
Description: Plugin d’intégration du paiement MVola (sandbox) pour Osclass.
Version: 1.0.0
Author: Fitia
Author URI: https://ton-site.com
Short Name: mvola_sandbox
*/

if (!defined('OC_ADMIN')) {
    exit('Direct access is not allowed.');
}

// === Installation du plugin ===
function mvola_sandbox_install() {
    osc_set_preference('mvola_access_token', '', 'plugin-mvola', 'STRING');
}
osc_register_plugin(osc_plugin_path(__FILE__), 'mvola_sandbox_install');

// === Désinstallation du plugin ===
function mvola_sandbox_uninstall() {
    osc_delete_preference('mvola_access_token', 'plugin-mvola');
}
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'mvola_sandbox_uninstall');

// === Page de configuration ===
function mvola_sandbox_config() {
    echo '<h2>Configuration du plugin MVola Sandbox</h2>';
    echo '<p><a class="btn btn-primary" href="' . osc_admin_render_plugin_url("mvola_payment/get_token.php") . '">🪙 Obtenir un Access Token</a></p>';
    echo '<p><a class="btn btn-primary" href="' . osc_admin_render_plugin_url("mvola_payment/init_payment.php") . '">🪙 Initier Paiement</a></p>';
    echo '<p><a class="btn btn-primary" href="' . osc_admin_render_plugin_url("mvola_payment/get_status.php") . '">🪙 Voir Status</a></p>';
    echo '<p><a class="btn btn-primary" href="' . osc_admin_render_plugin_url("mvola_payment/get_details.php") . '">🪙 Voir Details</a></p>';
}

// === Lien “Configure” ===
osc_add_hook(osc_plugin_path(__FILE__) . "_configure", 'mvola_sandbox_config');
