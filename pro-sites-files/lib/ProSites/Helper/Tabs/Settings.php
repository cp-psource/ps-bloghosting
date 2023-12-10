<?php
/**
 * @copyright WMS N@W (https://n3rds.work/)
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

if ( ! class_exists( 'ProSites_Helper_Tabs_Settings' ) ) {
	class ProSites_Helper_Tabs_Settings extends ProSites_Helper_Tabs {

		public static function render( $callback_parent = 'ProSites_Helper_Tabs', $settings_header = array(), $options = array(), $persistent = array() ) {
			parent::render_child( get_class(), $callback_parent, $settings_header, $options, $persistent );
		}

		public static function get_active_tab() {
			return parent::get_active_tab_child( get_class() );
		}

		public static function get_tabs() {

			$section_options = array(
				'header_save_button' => true,
				'button_name'        => 'settings',
			);

			$tabs = array(
				'general'             => array_merge( $section_options, array(
					'title' => __( 'Allgemeine Einstellungen', 'psts' ),
					'desc'  => array(
						__( 'Richte die Grundeinstellungen für Dein Bloghosting-Netzwerk ein.', 'psts' ),
					),
				) ),
				'email'               => array_merge( $section_options, array(
					'title' => __( 'E-Mail Benachrichtigungen', 'psts' ),
					'desc'  => array(
						__( '"LEVEL", "SITENAME", "SITEURL" und "CHECKOUTURL" werden durch die zugehörigen Werte ersetzt. Kein HTML erlaubt.', 'psts' ),
					),
				) ),
				'payment'             => array_merge( $section_options, array(
					'title' => __( 'Währungseinstellungen', 'psts' ),
					'desc'  => array(
						__( 'Diese Einstellungen wirken sich nur auf die Anzeige aus. Das Zahlungsgateway Deiner Wahl unterstützt möglicherweise nicht alle hier aufgeführten Währungen.', 'psts' ),
					),
				) ),
				'taxes'               => array_merge( $section_options, array(
					'title' => __( 'Steuer-Einstellungen', 'psts' ),
					'desc'  => array(
						__( 'Einrichten der Steuer für Compliance und gesetzliche Anforderungen.', 'psts' ),
					),
				) ),
				'ads'                 => array_merge( $section_options, array(
					'title' => __( 'Werbung', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Deaktivieren von Anzeigen für eine Bloghosting-Ebene oder das Deaktivieren von Anzeigen auf einer Reihe anderer Webseiten für eine Bloghosting-Ebene.', 'psts' ),
					),
				) ),
				'prowidget'           => array_merge( $section_options, array(
					'title' => __( 'Bloghosting Widget', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht Bloghosting, ein Widget in die Seitenleiste einzufügen, um stolz die Bloghosting-Stufe anzuzeigen.', 'psts' ),
					),
				) ),
				'buddypress'          => array_merge( $section_options, array(
					'title' => __( 'Begrenze BuddyPress-Funktionen', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht es, die Erstellung von BuddyPress-Gruppen und das Versenden privater Nachrichten auf Benutzer einer Bloghosting Seite zu beschränken.', 'psts' ),
					),
				) ),
				'bulkupgrades'        => array_merge( $section_options, array(
					'title' => __( 'Massen-Upgrades', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht den Verkauf von Upgrades auf Bloghosting-Ebene in Massenpaketen.', 'psts' ),
					),
				) ),
				'paytoblog'           => array_merge( $section_options, array(
					'title' => __( 'Zahlen zum Bloggen', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das vollständige Deaktivieren einer Webseite sowohl im Front-End als auch im Back-End bis zur Bezahlung.', 'psts' ),
					),
				) ),
				'throttling'          => array_merge( $section_options, array(
					'title' => __( 'Beiträge/Seiten Drosselung', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht die Anzahl der Beiträge/Seiten zu begrenzen, die täglich/stündlich pro Webseite veröffentlicht werden dürfen.', 'psts' ),
					),
				) ),
				'quotas'              => array_merge( $section_options, array(
					'title' => __( 'Beiträge/Seiten Kontingent', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Begrenzen der Anzahl der Beitragstypen für die ausgewählte Mindeststufe für Webseiten.', 'psts' ),
					),
				) ),
				'renaming'            => array_merge( $section_options, array(
					'title' => __( 'Plugin/Theme-Funktionen umbenennen', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Umbenennen Deiner Premium Themes- und Premium Plugins-Pakete.', 'psts' ),
					),
				) ),
				'support'             => array_merge( $section_options, array(
					'title' => __( 'Premium Support', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht die Bereitstellung einer Premium-Direkt-E-Mail-Support-Seite für ausgewählte Bloghosting-Ebenen.', 'psts' ),
					),
				) ),
				'upload_quota'        => array_merge( $section_options, array(
					'title' => __( 'Speicherplatz Kontingente', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht es Bloghosting Seiten zusätzlichen Upload-Speicherplatz zuzuweisen.', 'psts' ),
					),
				) ),
				'upgrade_admin_links' => array_merge( $section_options, array(
					'title' => __( 'Upgrade Admin Menü Links', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Hinzufügen von benutzerdefinierten Menüelementen im Admin-Bereich, die Administratoren dazu ermutigen, eine höhere Ebene zu erreichen, indem sie zur Upgrade-Seite umleiten.', 'psts' ),
					),
				) ),
				'filters'             => array_merge( $section_options, array(
					'title' => __( 'Ungefiltertes HTML', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht die Bereitstellung der Berechtigung "unfiltered_html" für bestimmte Benutzertypen für ausgewählte Bloghosting-Ebenen.', 'psts' ),
					),
				) ),
				'writing'             => array_merge( $section_options, array(
					'title' => __( 'Beschränke das Veröffentlichen', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Aktivieren des Schreibens von Beiträgen und/oder Seiten nur für ausgewählte Bloghosting-Ebenen.', 'psts' ),
					),
				) ),
				'xmlrpc'              => array_merge( $section_options, array(
					'title' => __( 'Beschränke XML-RPC', 'psts' ),
					'desc'  => array(
						__( 'Ermöglicht das Aktivieren von XML-RPC nur für ausgewählte Bloghosting-Ebenen.', 'psts' ),
					),
				) ),
			);

			$page = sanitize_html_class( @$_GET['page'], 'general' );

			foreach ( $tabs as $key => $tab ) {
				$tabs[ $key ]['url'] = sprintf(
					'admin.php?page=%1$s&tab=%2$s',
					esc_attr( $page ),
					esc_attr( $key )
				);
			}

			$tabs = self::remove_disabled_module_tabs( $tabs );

			return apply_filters( 'prosites_settings_tabs', $tabs );
		}

		public static function remove_disabled_module_tabs( $tabs ) {
			global $psts;

			$modules = $psts->get_setting( 'modules_enabled' );
			$modules = ! empty( $modules ) ? $modules : array();

			if ( ! in_array( 'ProSites_Module_Ads', $modules ) ) {
				unset( $tabs['ads'] );
			}
			if ( ! in_array( 'ProSites_Module_BulkUpgrades', $modules ) ) {
				unset( $tabs['bulkupgrades'] );
			}
			if ( ! in_array( 'ProSites_Module_BP', $modules ) ) {
				unset( $tabs['buddypress'] );
			}
			if ( ! in_array( 'ProSites_Module_Writing', $modules ) ) {
				unset( $tabs['writing'] );
			}
			if ( ! in_array( 'ProSites_Module_PayToBlog', $modules ) ) {
				unset( $tabs['paytoblog'] );
			}
			if ( ! in_array( 'ProSites_Module_PostThrottling', $modules ) ) {
				unset( $tabs['throttling'] );
			}
			if ( ! in_array( 'ProSites_Module_PostingQuota', $modules ) ) {
				unset( $tabs['quotas'] );
			}
			if ( ! in_array( 'ProSites_Module_Support', $modules ) ) {
				unset( $tabs['support'] );
			}
			if ( ! in_array( 'ProSites_Module_ProWidget', $modules ) ) {
				unset( $tabs['prowidget'] );
			}
			if ( ! in_array( 'ProSites_Module_XMLRPC', $modules ) ) {
				unset( $tabs['xmlrpc'] );
			}
			if ( ! in_array( 'ProSites_Module_UnfilterHtml', $modules ) ) {
				unset( $tabs['filters'] );
			}
			if ( ! in_array( 'ProSites_Module_Quota', $modules ) ) {
				unset( $tabs['upload_quota'] );
			}
			if ( ! in_array( 'ProSites_Module_PremiumThemes', $modules ) && ! in_array( 'ProSites_Module_Plugins', $modules ) ) {
				unset( $tabs['renaming'] );
			}
			if ( ! in_array( 'ProSites_Module_UpgradeAdminLinks', $modules ) ) {
				unset( $tabs['upgrade_admin_links'] );
			}


			$modules = array(
				'ProSites_Module_Ads',
				'ProSites_Module_BulkUpgrades',
				'ProSites_Module_BP',
				'ProSites_Module_Writing',
				'ProSites_Module_PayToBlog',
				'ProSites_Module_PostThrottling',
				'ProSites_Module_PostingQuota',
				'ProSites_Module_Plugins',
				'ProSites_Module_Support',
				'ProSites_Module_PremiumThemes',
				'ProSites_Module_ProWidget',
				'ProSites_Module_XMLRPC',
				'ProSites_Module_UnfilterHtml',
				'ProSites_Module_Quota',
				'ProSites_Module_UpgradeAdminLinks',
			);


			return $tabs;
		}

	}

}
