<?php

/*
Plugin Name: Bloghosting (Feature: Begrenze BuddyPress-Funktionen)
*/

class ProSites_Module_BP {

	static $user_label;
	static $user_description;

	// Module name for registering
	public static function get_name() {
		return __('Begrenze BuddyPress-Funktionen', 'psts');
	}

	// Module description for registering
	public static function get_description() {
		return __('Ermöglicht es die Erstellung von BuddyPress-Gruppen und das Versenden von Nachrichten an Benutzer auf Bloghosting-Level zu beschränken.', 'psts');
	}

	function __construct() {
		if( ! is_admin() && is_main_site( get_current_blog_id() ) ) {
			return;
		}
//		add_action( 'psts_settings_page', array( &$this, 'settings' ) );
		add_filter( 'psts_settings_filter', array( &$this, 'settings_process' ), 10, 2 );
		self::$user_label       = __( 'Buddy Press', 'psts' );
		self::$user_description = __( 'Begrenzte Gruppenerstellung und Messaging', 'psts' );

		if ( ! is_main_site( get_current_blog_id() ) ) {
			add_filter( 'messages_template_compose', array( &$this, 'messages_template' ) );
			add_filter( 'bp_user_can_create_groups', array( &$this, 'create_groups' ) );
			add_filter( 'bp_blogs_is_blog_recordable', array( &$this, 'prosites_filter_blogs' ), 10, 2 );
			add_filter( 'bp_blogs_is_blog_recordable_for_user', array( &$this, 'prosites_filter_blogs' ), 10, 3 );
			add_filter( 'psts_downgrade', array( &$this, 'downgrade_blog' ), 10, 3 );
			add_filter( 'psts_upgrade', array( &$this, 'upgrade_blog' ), 10, 3 );
			add_action( 'wp_head', array( &$this, 'css_output' ) );
		}
	}

	/**
	 * Prevents Buddypress from displaying non-pro sites in activities or anything
	 * If third parameter is null, the funcion has been called via bp_blogs_is_blog_recordable_for_user filter
	 * otherwise via bp_blogs_is_blog_recordable
	 *
	 * @uses is_pro_site()
	 *
	 * @param int $recordable_globally previous value for recorded globally
	 * @param int $blog_id ID of the blog being checked.
	 * @param int $user_id (Optional) ID of the user for whom access is being checked.
	 *
	 * @return bool True if site is pro or originally was recorable False if filtering is on plus it's a non-pro site
	 **/
	function prosites_filter_blogs( $recordable_globally = null, $blog_id = false, $user_id = null ) {
		global $bp, $psts;
		// If related feature is off simply return original value
		if ( ! $psts->get_setting( 'bp_hide_unpaid' ) ) {
			return $recordable_globally;
		}

		// Otherwise check if site is pro
		return is_pro_site( $blog_id );
	}

	/**
	 * Downgrade blog. Remove related entry from Buddypress blog cache.
	 * This func
	 *
	 * @param int $blog_id
	 * @param int $level
	 * @param int $old_level
	 **/
	function downgrade_blog( $blog_id = null, $level = null, $old_level = null ) {
		if ( empty( $blog_id ) ) {
			return;
		}
		if( function_exists( 'bp_blogs_remove_blog' ) ) {
			bp_blogs_remove_blog( $blog_id );
		}
	}

	/**
	 * Original Function is deprecated, so we had to define ours
	 *
	 * @param string $sitedomain
	 * @param string $path
	 *
	 * @return array|bool|null|object
	 */
	function psts_get_admin_users_for_domain( $sitedomain = '', $path = '' ) {
		global $wpdb;

		if ( ! $sitedomain )
			$site_id = $wpdb->siteid;
		else
			$site_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path ) );

		if ( $site_id )
			return $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, u.user_login, u.user_pass FROM $wpdb->users AS u, $wpdb->sitemeta AS sm WHERE sm.meta_key = 'admin_user_id' AND u.ID = sm.meta_value AND sm.site_id = %d", $site_id ), ARRAY_A );

		return false;
	}

	/**
	 * Upgrade blog. Basically add removed blog entry to BP cache
	 *
	 * @param int $blog_id
	 * @param int $level
	 * @param int $old_level
	 *
	 * @return null
	 **/
	function upgrade_blog( $blog_id = null, $level = null, $old_level = null ) {
		if ( empty( $blog_id ) ) {
			return;
		}

		// Get user ID
		switch_to_blog( $blog_id );
		$user = $this->psts_get_admin_users_for_domain();
		restore_current_blog();

		if( function_exists( 'bp_blogs_record_blog' ) ) {
			bp_blogs_record_blog( $blog_id, $user['ID'] );
		}

	}

	function settings_process( $settings, $active_tab ) {

		if ( 'buddypress' == $active_tab ) {
			global $psts;
			$settings['bp_group']       = isset( $settings['bp_group'] ) ? 1 : 0;
			$settings['bp_compose']     = isset( $settings['bp_compose'] ) ? 1 : 0;
			$settings['bp_hide_unpaid'] = isset( $settings['bp_hide_unpaid'] ) ? 1 : 0;
		}

		return $settings;
	}

	function settings() {
		global $psts, $wpdb;

		?>
<!--		<div class="postbox">-->
<!--			<h3 class="hndle" style="cursor:auto;"><span>--><?php //_e( 'Begrenze BuddyPress-Funktionen', 'psts' ) ?><!--</span> --->
<!--				<span class="description">--><?php //_e( 'Ermöglicht es die Erstellung von BuddyPress-Gruppen und das Versenden von Nachrichten an Benutzer auf Bloghosting-Level zu beschränken.', 'psts' ) ?><!--</span>-->
<!--			</h3>-->

			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Gruppenerstellung begrenzen', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[bp_group]" value="1"<?php checked( $psts->get_setting( 'bp_group' ) ); ?> /> <?php _e( 'Nur für Pro Webseiten Inhaber', 'psts' ); ?>
							</label></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Beschränke das Verfassen von Nachrichten', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[bp_compose]" value="1"<?php checked( $psts->get_setting( 'bp_compose' ) ); ?> /> <?php _e( 'Nur für Pro Webseiten Inhaber', 'psts' ); ?>
							</label></td>
					</tr>
					<tr valign="top">
						<th scope="row" class="psts-help-div psts-buddypress-restricted"><?php echo __( 'Eingeschränkt Hinweis', 'psts' ) . $psts->help_text( __( 'Erforderlich - HTML zulässig - Diese Meldung wird angezeigt, wenn in BuddyPress auf eine Funktion nur für Pro Webseite-Benutzer zugegriffen wird. "LEVEL" wird durch den Namen der ersten Ebene ersetzt.', 'psts' ) ); ?></th>
						<td>
							<input type="text" name="psts[bp_notice]" id="bp_notice" value="<?php echo esc_attr( $psts->get_setting( 'bp_notice' ) ); ?>" style="width: 95%"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="psts-help-div psts-limit-blog-tracking"><?php echo __( 'Blog-Tracking einschränken', 'psts' ) . $psts->help_text( __( 'Beachte: Das Ändern dieser Einstellung wirkt sich rückwirkend auf BuddyPress aus.', 'psts' ) ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[bp_hide_unpaid]" id="bp_hide_unpaid" <?php checked( $psts->get_setting( 'bp_hide_unpaid' ) ); ?> /> <?php _e( 'Kein Blog-Tracking für unbezahlte Webseiten', 'psts' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>
<!--		</div>-->
	<?php
	}

	function create_groups( $can_create ) {
		global $bp, $psts;
		if ( ! $psts->get_setting( 'bp_group' ) ) {
			return $can_create;
		}

		//don't mess with pro_sites
		if ( is_pro_user() ) {
			return $can_create;
		}

		$can_create = false;
		add_action( 'template_notices', array( &$this, 'message' ) );

		return $can_create;
	}

	function messages_template( $template ) {
		global $psts;

		if ( ! $psts->get_setting( 'bp_compose' ) ) {
			return $template;
		}

		//don't mess with pro_sites
		if ( is_pro_user() ) {
			return $template;
		}

		add_action( 'bp_template_content', array( &$this, 'message' ) );

		return 'members/single/plugins';
	}

	function message() {
		global $psts;

		//link to the primary blog
		$blog_id = get_user_meta( get_current_user_id(), 'primary_blog', true );
		if ( ! $blog_id ) {
			$blog_id = false;
		}

		$notice = str_replace( 'LEVEL', $psts->get_level_setting( 1, 'name' ), $psts->get_setting( 'bp_notice' ) );
		echo '<div id="message" class="error"><p><a href="' . $psts->checkout_url( $blog_id ) . '">' . $notice . '</a></p></div>';
	}

	function css_output() {
		//display css for error messages
		?>
		<style type="text/css">#message.error p a {
				color: #FFFFFF;
			}</style>
	<?php

	}

	public static function is_included( $level_id ) {
		switch ( $level_id ) {
			default:
				return false;
		}
	}

	public static function is_active() {
		global $psts;

		$active = $psts->get_setting( 'bp_group' ) || $psts->get_setting( 'bp_compose' );

		return $active;
	}
}
