<?php
/**
 * @copyright PSOURCE (https://github.com/cp-psource)
 *
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA 02110-1301 USA
 *
 */

 if ( ! class_exists( 'ProSites_Helper_Tabs_Gateways' ) ) {
    class ProSites_Helper_Tabs_Gateways extends ProSites_Helper_Tabs {

        public static function render( $callback_parent = 'ProSites_Helper_Tabs', $settings_header = array(), $options = array(), $persistent = array() ) {
            parent::render_child( __CLASS__, $callback_parent, $settings_header, $options, $persistent );
        }

        public static function get_active_tab() {
            return parent::get_active_tab_child( __CLASS__ );
        }

        public static function get_tabs() {

            $section_options = array(
                'header_save_button' => true,
                'button_name'        => 'gateways',
            );

            $tabs = array(
                'gateway_prefs' => array_merge( $section_options, array(
                    'title' => __( 'Gateway-Einstellungen', 'psts' ),
                    'desc'               => array(
                        __( 'Wähle aus wie Bloghosting mehrere aktive Zahlungsgateways verarbeiten soll', 'psts' ),
                    ),
                    'class' => 'prosites-gateway-pref',
                ) ),
                'paypal' => array_merge( $section_options, array(
                    'title' => __( 'PayPal Express', 'psts' ),
                    'desc'               => array(
                        __( 'Express Checkout ist die führende Checkout-Lösung von PayPal, die den Checkout-Prozess für Käufer rationalisiert und sie nach dem Kauf auf Deiner Website hält.', 'psts' ),
                    ),
                ) ),
                'stripe' => array_merge( $section_options, array(
                    'title' => __( 'Stripe', 'psts' ),
                    'desc'               => array(
                        __( 'Mit Stripe kannst Du ganz einfach Kreditkarten direkt auf Deiner Webseite mit vollständiger PCI-Konformität akzeptieren', 'psts' ),
                    ),
                ) ),
                'manual' => array_merge( $section_options, array(
                    'title' => __( 'Manuelle Zahlungen', 'psts' ),
                    'desc'               => array(
                        __( 'Erfasse Zahlungen manuell, z.B. per Bargeld, Scheck, Überweisung oder einem nicht unterstützten Gateway.', 'psts' ),
                    ),
                ) ),
            );

            $page = sanitize_html_class( @$_GET['page'], 'gateway_prefs' );

            foreach ( $tabs as $key => $tab ) {
                $tabs[ $key ]['url'] = sprintf(
                    'admin.php?page=%1$s&tab=%2$s',
                    esc_attr( $page ),
                    esc_attr( $key )
                );
            }

            return apply_filters( 'prosites_gateways_tabs', $tabs );

        }

    }
}