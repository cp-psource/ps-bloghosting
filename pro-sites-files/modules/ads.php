<?php

/*
Plugin Name: Bloghosting (Feature: Werbeanzeigen)
*/

class ProSites_Module_Ads {

	var $ad_counter;
	static $user_label;
	static $user_description;

	// Module name for registering
	public static function get_name() {
		return __('Werbung', 'psts' );
	}

	// Module description for registering
	public static function get_description() {
		return __( 'Ermöglicht das Deaktivieren von Anzeigen für eine Bloghosting-Ebene oder das Deaktivieren von Anzeigen auf einer Reihe anderer Webseiten für eine Bloghosting-Ebene.', 'psts' );
	}

	function __construct() {
		global $psts;

		add_filter( 'psts_settings_filter', array( &$this, 'settings_process' ), 10, 2 );
		add_action( 'admin_menu', array( &$this, 'plug_page' ), 100 );
		add_action( 'psts_extend', array( &$this, 'extend' ), 10, 2 );
		add_action( 'psts_withdraw', array( &$this, 'withdraw' ), 10, 2 );
		add_filter( 'the_content', array( &$this, 'advertising_output' ) );

		self::$user_label       = __( 'Werbeanzeigen', 'psts' );
		self::$user_description = __( 'Werbemodul', 'psts' );

		//update install script if necessary
		if ( $psts->get_setting( 'ads_version' ) != $psts->version ) {
			$this->install();
		}
	}

	function install() {
		global $wpdb, $psts;

		$table_name = $wpdb->base_prefix . 'supporter_ads';
		$charset_collate = $wpdb->get_charset_collate();

		$table1 = "CREATE TABLE $table_name (
		  supporter_ads_ID bigint(20) unsigned NOT NULL auto_increment,
		  supporter_blog_ID bigint(20) NOT NULL default '0',
		  blog_ID bigint(20) NOT NULL default '0',
		  expire bigint(20) NOT NULL default '0',
		  PRIMARY KEY  (supporter_ads_ID)
		) $charset_collate;";

		if ( ! defined( 'DO_NOT_UPGRADE_GLOBAL_TABLES' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $table1 );
		}

		$psts->update_setting( 'ads_version', $psts->version );
	}

	function update_cache( $blog_id ) {
		global $wpdb;
		$expirations = $wpdb->get_results( "SELECT blog_ID, expire FROM {$wpdb->base_prefix}supporter_ads WHERE supporter_blog_ID = '$blog_id'" );

		if ( null === $expirations ) {
			return;
		}

		foreach ( $expirations as $expiration ) {
			ProSites_Helper_Cache::set_cache( "supporter_ads:{$expiration->blog_ID}:expire", $expiration->expire, 'blog-details' );
		}
	}

	function plug_page() {
		global $psts;
		//add it under the pro blogs menu
		if ( ! is_main_site() && $psts->get_setting( 'ads_enable_blogs' ) ) {
			add_submenu_page( 'psts-checkout', $psts->get_setting( 'ads_name' ), $psts->get_setting( 'ads_name' ), 'manage_options', 'psts-ads', array(
				&$this,
				'ads_page'
			) );
		}
	}

	function extend( $blog_id, $new_expire ) {
		global $wpdb;
		$max = $this->max_ad_free( $blog_id ); //only extend the number of blogs for their level
		$wpdb->query( "UPDATE {$wpdb->base_prefix}supporter_ads SET expire = '$new_expire' WHERE supporter_blog_ID = '$blog_id' LIMIT $max" );
		$this->update_cache( $blog_id );
	}

	function withdraw( $blog_id, $new_expire ) {
		global $wpdb;
		$wpdb->query( "UPDATE {$wpdb->base_prefix}supporter_ads SET expire = '$new_expire' WHERE supporter_blog_ID = '$blog_id'" );
		$this->update_cache( $blog_id );
	}

	function check( $blog_id = null ) {
		global $wpdb;

		if ( empty( $blog_id ) ) {
			$blog_id = $wpdb->blogid;
		}

		$expire = ProSites_Helper_Cache::get_cache( "supporter_ads:$blog_id:expire", 'blog-details' );

		if ( false === $expire ) {
			$expire = $wpdb->get_var( "SELECT expire FROM {$wpdb->base_prefix}supporter_ads WHERE blog_ID = '$blog_id'" );

			if ( null === $expire ) {
				$expire = - 1;
			}

			ProSites_Helper_Cache::set_cache( "supporter_ads:$blog_id:expire", $expire, 'blog-details' );
		}

		return ( $expire > time() );
	}

	function show_ads( $blog_id = null ) {
		global $wpdb, $psts;

		if ( empty( $blog_id ) ) {
			$blog_id = $wpdb->blogid;
		}

		$show_ads = apply_filters( 'psts_show_ads', null, $blog_id );
		if ( ! is_null( $show_ads ) ) {
			return $show_ads;
		}

		if ( is_main_site( $blog_id ) ) {
			return false;
		} else {
			if ( is_pro_site( $blog_id, $psts->get_setting( 'ads_level' ) ) || $this->check( $blog_id ) ) {
				return false;
			} else {
				return true;
			}
		}
	}

	function hide_ads( $blog_id = null ) {
		global $wpdb, $psts;

		if ( empty( $blog_id ) ) {
			$blog_id = $wpdb->blogid;
		}

		$hide_ads = apply_filters( 'psts_hide_ads', null, $blog_id );
		if ( ! is_null( $hide_ads ) ) {
			return $hide_ads;
		}

		if ( is_main_site( $blog_id ) ) {
			return true;
		} else {
			if ( is_pro_site( $blog_id, $psts->get_setting( 'ads_level' ) ) || $this->check( $blog_id ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	function max_ad_free( $blog_id = null ) {
		global $wpdb, $psts;

		if ( empty( $blog_id ) ) {
			$blog_id = $wpdb->blogid;
		}

		$ads = is_pro_site( $blog_id ) ? $psts->get_level_setting( $psts->get_level( $blog_id ), 'ads' ) : 0;

		return intval( $ads );
	}

	function message() {
		global $psts, $blog_id;

		$level = $psts->get_level() + 1;
		if ( $name = $psts->get_level_setting( $level, 'name' ) ) { //only show if there is a higher level
			$ads = $psts->get_level_setting( $level, 'ads' );
			$msg = str_replace( 'LEVEL', $name, $psts->get_setting( 'ads_message' ) );
			$msg = str_replace( 'NUM', $ads, $msg );
			echo '<div style="background-color: #FFFFE0;border-color: #E6DB55;border-radius: 3px;border-style: solid;border-width: 1px;margin: 10px 0;padding: 0 1em;"><p><strong><a href="' . $psts->checkout_url( $blog_id ) . '">' . $msg . '</a></strong></p></div>';
		}
	}

	function advertising_output( $content ) {
		global $psts;

		if (!in_the_loop () || !is_main_query ()) {
			return $content;
	    }
		if ( $this->show_ads() && ! is_feed() ) {
			$per_page = $psts->get_setting( 'ads_count', 3 );

			if ( is_page() ) {
				if ( $psts->get_setting( 'ads_before_page' ) ) {
					if ( $this->ad_counter < $per_page ) {
						$content = $psts->get_setting( 'ads_before_code' ) . $content;
						$this->ad_counter ++;
					}
				}
				if ( $psts->get_setting( 'ads_after_page' ) ) {
					if ( $this->ad_counter < $per_page ) {
						$content = $content . $psts->get_setting( 'ads_after_code' );
						$this->ad_counter ++;
					}
				}
			} else {

				//check if post possibly an excerpt and is too short
				if ( is_home() || is_archive() ) {
					$text           = str_replace( ']]>', ']]&gt;', $content );
					$text           = strip_tags( $text );
					$excerpt_length = apply_filters( 'excerpt_length', 55 );
					$words          = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
					if ( count( $words ) <= $excerpt_length ) {
						return $content;
					} //skip
				}

				if ( $psts->get_setting( 'ads_before_post' ) ) {
					if ( $this->ad_counter < $per_page ) {
						$content = $psts->get_setting( 'ads_before_code' ) . $content;
						$this->ad_counter ++;
					}
				}
				if ( $psts->get_setting( 'ads_after_post' ) ) {
					if ( $this->ad_counter < $per_page ) {
						$content = $content . $psts->get_setting( 'ads_after_code' );
						$this->ad_counter ++;
					}
				}
			}
		}

		return $content;
	}

	function settings_process( $settings, $active_tab ) {
		if ( ! array_key_exists( 'ads_levels', $_POST ) || 'ads' != $active_tab ) {
			return $settings;
		}
		global $psts;

		foreach ( $_POST['ads_levels'] as $level => $num ) {
			$psts->update_level_setting( $level, 'ads', $num );
		}

		$settings['ads_before_post'] = isset( $settings['ads_before_post'] ) ? 1 : 0;
		$settings['ads_after_post']  = isset( $settings['ads_after_post'] ) ? 1 : 0;
		$settings['ads_before_page'] = isset( $settings['ads_before_page'] ) ? 1 : 0;
		$settings['ads_after_page']  = isset( $settings['ads_after_page'] ) ? 1 : 0;

		$settings['ads_enable_blogs'] = isset( $settings['ads_enable_blogs'] ) ? 1 : 0;

		$settings['ads_themes']   = isset( $settings['ads_themes'] ) ? 1 : 0;
		$settings['ads_xmlrpc']   = isset( $settings['ads_xmlrpc'] ) ? 1 : 0;
		$settings['ads_unfilter'] = isset( $settings['ads_unfilter'] ) ? 1 : 0;

		return $settings;
	}

	function settings() {
		global $psts;
		$levels = (array) get_site_option( 'psts_levels' );
		?>
		<!--		<div class="postbox">-->
		<!--			<h3 class="hndle" style="cursor:auto;"><span>--><?php //_e( 'Ads', 'psts' ) ?><!--</span> --->
		<!--				<span class="description">--><?php //_e( 'Ermöglicht das Deaktivieren von Anzeigen für eine Bloghosting-Ebene oder das Deaktivieren von Anzeigen auf einer Reihe anderer Websites für eine Bloghosting-Ebene.', 'psts' ) ?><!--</span>-->
		<!--			</h3>-->

		<div class="inside">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-ad-free"><?php echo __( 'Werbungsfreier Level', 'psts' ) . $psts->help_text( __( 'Wähle die Mindeststufe aus, die erforderlich ist, um keine Anzeigen auf der Webseite zu schalten.', 'psts', $psts ) ); ?></th>
					<td>
						<select name="psts[ads_level]" class="chosen">
							<?php
							foreach ( $levels as $level => $value ) {
								?>
								<option value="<?php echo $level; ?>"<?php selected( $psts->get_setting( 'ads_level', 1 ), $level ) ?>><?php echo $level . ': ' . esc_attr( $value['name'] ); ?></option><?php
							}
							?>
						</select><br/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Anzeigenplatzierung', 'psts' ) ?></th>
					<td>
						<label><input type="checkbox" name="psts[ads_before_post]"
						              value="1"<?php checked( $psts->get_setting( 'ads_before_post' ) ); ?> /> <?php _e( 'Vor Beitragsinhalten', 'psts' ); ?>
						</label><br/>
						<label><input type="checkbox" name="psts[ads_after_post]"
						              value="1"<?php checked( $psts->get_setting( 'ads_after_post' ) ); ?> /> <?php _e( 'Nach Beitragsinhalten', 'psts' ); ?>
						</label><br/>
						<label><input type="checkbox" name="psts[ads_before_page]"
						              value="1"<?php checked( $psts->get_setting( 'ads_before_page' ) ); ?> /> <?php _e( 'Vor Seiteninhalten', 'psts' ); ?>
						</label><br/>
						<label><input type="checkbox" name="psts[ads_after_page]"
						              value="1"<?php checked( $psts->get_setting( 'ads_after_page' ) ); ?> /> <?php _e( 'Nach Seiteninhalten', 'psts' ); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-ads-perpage"><?php echo __( 'Werbeanzeigen pro Seite', 'psts' ) . $psts->help_text( __( 'Maximale Anzahl von Anzeigen, die auf einer einzelnen Seite geschaltet werden sollen. Setze dies für Google Adsense auf "3".', 'psts' ) ); ?></th>
					<td>
						<select name="psts[ads_count]" class="chosen">
							<?php
							$per_page = $psts->get_setting( 'ads_count', 3 );
							for ( $counter = 1; $counter <= 10; $counter ++ ) {
								echo '<option value="' . $counter . '"' . ( $counter == $per_page ? ' selected' : '' ) . '>' . number_format_i18n( $counter ) . '</option>' . "\n";
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-before-ad"><?php echo __( '"Bevor" Anzeigecode', 'psts' ) . $psts->help_text( __( 'Wird vor dem Beitrags- und Seiteninhalt angezeigt.', 'psts' ) ); ?></th>
					<td>
						<textarea name="psts[ads_before_code]" type="text" rows="4" wrap="soft"
						          style="width: 95%"/><?php echo esc_textarea( $psts->get_setting( 'ads_before_code' ) ); ?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-after-ad"><?php echo __( '"Nach" Anzeigecode', 'psts' ) . $psts->help_text( __( 'Wird nach dem Inhalt von Beiträgen und Seiten angezeigt.', 'psts' ) ); ?></th>
					<td>
						<textarea name="psts[ads_after_code]" type="text" rows="4" wrap="soft"
						          style="width: 95%"/><?php echo esc_textarea( $psts->get_setting( 'ads_after_code' ) ); ?></textarea>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Aktiviere zusätzliche werbefreie Webseiten', 'psts' ); ?></th>
					<td>
						<label><input type="checkbox" name="psts[ads_enable_blogs]"
						              value="1"<?php checked( $psts->get_setting( 'ads_enable_blogs' ) ); ?> /> <?php _e( 'Deaktiviere Anzeigen auf anderen Webseiten', 'psts' ); ?>
						</label></td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-rename-ads"><?php echo __( 'Feature umbenennen', 'psts' ) . $psts->help_text( __( 'Erforderlich - Kein HTML! - Mach das kurz und bündig.', 'psts' ) ); ?></th>
					<td>
						<input type="text" name="psts[ads_name]" id="ads_name"
						       value="<?php echo esc_attr( $psts->get_setting( 'ads_name' ) ); ?>" size="30"/>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-adfree-sites"><?php echo __( 'Zusätzliche werbefreie Webseiten', 'psts' ) . $psts->help_text( __( 'Anzahl der Webseiten, auf denen zusätzlich zur Pro-Webseite Anzeigen deaktiviert werden können. Jedes Level sollte eine identische oder zunehmend höhere Nummer haben.', 'psts' ) ); ?></th>
					<td><?php
						foreach ( $levels as $level => $data ) {
							echo '<label>';
							$this->ads_select( $level, @$data['ads'] );
							echo ' ' . $level . ' - ' . $data['name'] . '</label><br />';
						}
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-ads-message"><?php echo __( 'Werbung Hinweis', 'psts' ) . $psts->help_text( __( 'Erforderlich - Diese Meldung wird auf der Seite Anzeigen deaktivieren als Werbung angezeigt, um auf die nächste Stufe zu aktualisieren. "LEVEL" wird durch den erforderlichen Ebenennamen ersetzt, und "NUM" wird durch die Anzahl der Seiten ersetzt, die in der nächsten Ebene deaktiviert werden können.', 'psts' ) ); ?></th>
					<td>
						<input type="text" name="psts[ads_message]" id="ads_message"
						       value="<?php echo esc_attr( $psts->get_setting( "ads_message" ) ); ?>"
						       style="width: 95%"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"
					    class="psts-help-div psts-enable-other-modules"><?php echo __( 'Andere Module aktivieren', 'psts' ) . $psts->help_text( __( 'Dies überschreibt alle Level-Limitierungen, die Du im Modul hinzufügst. Du solltest den Nutzern dies in der obigen Anzeigenmeldung mitteilen.', 'psts' ) ); ?></th>
					<td>
						<?php if ( class_exists( 'ProSites_Module_PremiumThemes' ) ) { ?>
							<label><input type="checkbox" name="psts[ads_themes]"
							              value="1"<?php checked( $psts->get_setting( 'ads_themes' ) ); ?> /> <?php _e( 'Aktiviere alle Premium-Designs', 'psts' ) ?>
							</label><br/>
						<?php } ?>
						<?php if ( class_exists( 'ProSites_Module_XMLRPC' ) ) { ?>
							<label><input type="checkbox" name="psts[ads_xmlrpc]"
							              value="1"<?php checked( $psts->get_setting( 'ads_xmlrpc' ) ); ?> /> <?php _e( 'Aktiviere XML-RPC', 'psts' ) ?>
							</label><br/>
						<?php } ?>
						<?php if ( class_exists( 'ProSites_Module_UnfilterHtml' ) ) { ?>
							<label><input type="checkbox" name="psts[ads_unfilter]"
							              value="1"<?php checked( $psts->get_setting( 'ads_unfilter' ) ); ?> /> <?php _e( 'Aktiviere ungefiltertes HTML', 'psts' ) ?>
							</label><br/>
						<?php } ?>
					</td>
				</tr>
			</table>
		</div>
		<!--		</div>-->
	<?php
	}

	function ads_select( $level, $selected ) {
		?>
		<select name="ads_levels[<?php echo $level; ?>]" id="ads_level_<?php echo $level; ?>" class="chosen">
			<?php
			for ( $counter = 0; $counter <= 100; $counter ++ ) {
				echo '<option value="' . $counter . '"' . ( $counter == $selected ? ' selected' : '' ) . '>' . number_format_i18n( $counter ) . '</option>' . "\n";
			}
			?>
		</select>
	<?php
	}

	function ads_page() {
		global $wpdb, $psts;

		if ( ! current_user_can( 'manage_options' ) ) {
			echo "<p>" . __( 'Netter Versuch...', 'psts' ) . "</p>"; //If accessed properly, this message doesn't appear.
			return;
		}

		$ad_free_blogs_max       = $this->max_ad_free();
		$blogs                   = $wpdb->get_results( "SELECT * FROM {$wpdb->base_prefix}supporter_ads WHERE supporter_blog_ID = '" . $wpdb->blogid . "' ORDER BY supporter_ads_ID DESC", ARRAY_A );
		$ad_free_blogs_current   = count( $blogs );
		$ad_free_blogs_remaining = $ad_free_blogs_max - $ad_free_blogs_current;
		$ad_free_blogs_remaining = ( $ad_free_blogs_remaining <= 0 ) ? 0 : $ad_free_blogs_remaining;

		//handle adding new blogs
		if ( isset( $_POST['submit_process'] ) ) {
			$expire = $psts->get_expire();
			$blogs  = $_POST['blogs'];
			if ( is_array( $blogs ) ) {
				foreach ( $blogs as $blog_id => $value ) {
					if ( $ad_free_blogs_remaining > 0 && $value == '1' ) {
						$existing_check = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->base_prefix}supporter_ads WHERE supporter_blog_ID = %d AND blog_ID = %d", $wpdb->blogid, $blog_id ) );
						if ( $existing_check < 1 ) {
							$ad_free_blogs_remaining --;
							$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->base_prefix}supporter_ads (blog_ID, supporter_blog_ID) VALUES ( %s, %s )", $blog_id, $wpdb->blogid ) );
						}
					}
				}
			}
			$wpdb->query( "UPDATE {$wpdb->base_prefix}supporter_ads SET expire = '" . $expire . "' WHERE supporter_blog_ID = '" . $wpdb->blogid . "'" );
			$this->update_cache( $wpdb->blogid );
			echo '<div id="message" class="updated fade"><p>' . __( 'Webseiten hinzugefügt.', 'psts' ) . '</p></div>';
		}

		//handle removing blogs
		if ( isset( $_POST['submit_remove'] ) ) {
			foreach ( (array) $_POST['blogs'] as $blog_id => $value ) {
				if ( $value == '1' ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->base_prefix}supporter_ads WHERE blog_ID = %d AND supporter_blog_ID = %d", $blog_id, $wpdb->blogid ) );
					wp_cache_delete( "supporter_ads:{$blog_id}:expire", 'blog-details' );
				}
			}
			echo '<div id="message" class="updated fade"><p>' . __( 'Webseiten entfernt.', 'psts' ) . '</p></div>';
		}

		$blogs                   = $wpdb->get_results( "SELECT * FROM {$wpdb->base_prefix}supporter_ads WHERE supporter_blog_ID = '" . $wpdb->blogid . "' ORDER BY supporter_ads_ID DESC", ARRAY_A );
		$ad_free_blogs_current   = count( $blogs );
		$ad_free_blogs_remaining = $ad_free_blogs_max - $ad_free_blogs_current;
		$ad_free_blogs_remaining = ( $ad_free_blogs_remaining <= 0 ) ? 0 : $ad_free_blogs_remaining;
		?>
		<div class="wrap">
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('input#submit_remove').click(function () {
					var answer = confirm("<?php _e('Bist Du sicher, dass Du diese Webseiten wirklich entfernen möchtest?', 'psts'); ?>")
					if (answer) {
						return true;
					} else {
						return false;
					}
					;
				});
			});
		</script>
		<div id="icon-psts-admin" class="icon32"></div>
		<h1><?php echo $psts->get_setting( 'ads_name' ); ?></h1>

		<form method="post" action="">
		<div class="metabox-holder">

		<div class="postbox">
			<h3 class="hndle" style="cursor:auto;"><span><?php _e( 'Status', 'psts' ) ?></span></h3>

			<div class="inside">
				<?php $this->message(); ?>
				<p>
				<ul>
					<li><?php _e( 'Maximale Webseiten', 'psts' ) ?>: <strong><?php echo $ad_free_blogs_max; ?></strong></li>
					<li><?php _e( 'Derzeit werden Anzeigen deaktiviert auf', 'psts' ) ?>:
						<strong><?php echo $ad_free_blogs_current; ?></strong></li>
					<li><?php _e( 'Verbleibend', 'psts' ) ?>: <strong><?php echo $ad_free_blogs_remaining; ?></strong>
					</li>
				</ul>
				</p>
			</div>
		</div>

		<?php if ( $ad_free_blogs_remaining > 0 && is_pro_site() ) { ?>
			<div class="postbox">
				<h3 class="hndle" style="cursor:auto;"><span><?php _e( 'Webseiten finden', 'psts' ) ?></span> -
					<span class="description"><?php _e( 'Suche nach einer Website, auf der Anzeigen deaktiviert werden können.', 'psts' ) ?></span></h3>

				<div class="inside">
					<?php
					$curr_blogs = get_blogs_of_user( get_current_user_id() );
					unset( $curr_blogs[ $wpdb->blogid ] ); //remove current blog
					if ( ! isset( $_POST['submit_search'] ) && $curr_blogs ) {
						?>
						<h4><?php _e( 'Wähle eine Seite aus, bei der Du Mitglied bist:', 'psts' ); ?></h4>
						<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
							<thead>
							<tr>
								<th scope='col' width='75px'><?php _e( 'Werbungen ausschalten', 'psts' ); ?></th>
								<th scope='col'><?php _e( 'Webseite', 'psts' ); ?></th>
							</tr>
							</thead>
							<tbody id='the-list'>
							<?php
							$class = '';
							foreach ( $curr_blogs as $blog_id => $blog ) {
								//=========================================================//
								echo "<tr class='" . $class . "'>";
								$existing_check = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . $wpdb->base_prefix . "supporter_ads WHERE supporter_blog_ID = %d AND blog_ID = %d", $wpdb->blogid, $blog_id ) );
								if ( $existing_check > 0 ) {
									echo "<td valign='top'><center><input name='blogs[$blog_id]' id='blog_$blog_id' value='1' type='checkbox' disabled='disabled'></center></td>";
								} else {
									echo "<td valign='top'><center><input name='blogs[$blog_id]' id='blog_$blog_id' value='1' type='checkbox'></center></td>";
								}
								if ( $existing_check > 0 ) {
									echo "<td valign='top' style='color:#666666;'><strong>" . esc_html( $blog->blogname ) . " (<em>" . $blog->domain . "</em>): " . __( 'Anzeigen bereits deaktiviert', 'psts' ) . "</strong></td>";
								} else {
									echo "<td valign='top'><label for='blog_$blog_id'><strong>" . esc_html( $blog->blogname ) . " (<em>" . $blog->domain . "</em>)</strong></label></td>";
								}
								echo "</tr>";
								$class = ( 'alternate' == $class ) ? '' : 'alternate';
								//=========================================================//
							}
							?>
							</tbody>
						</table>
						<p class="submit">
							<input type="submit" name="submit_process"
							       value="<?php _e( 'Werbungen ausschalten', 'psts' ) ?> &raquo;"/>
						</p>
					<?php } ?>

					<h4><?php _e( 'Suche nach einer Webseite:', 'psts' ); ?></h4>

					<p><input type="text" name="search" value="" size="30"/><br/>
						<?php _e( 'Gib hier die Seiten-Domain ein. Beispiel - für "eineseite.piestingtal.online" würdest Du nach einem "eineseite" suchen.', 'psts' ) ?>
					</p>

					<p class="submit">
						<input type="submit" name="submit_search" value="<?php _e( 'Suche', 'psts' ) ?> &raquo;"/>
					</p>
				</div>
			</div>
		<?php } ?>

		<?php if ( isset( $_POST['submit_search'] ) && is_pro_site() ) { ?>

			<div class="postbox">
				<h3 class="hndle" style="cursor:auto;"><span><?php _e( 'Suchergebnisse', 'psts' ); ?></span></h3>

				<div class="inside">
					<?php
					$query = "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE ( domain LIKE '%" . $wpdb->escape( $_POST['search'] ) . "%' OR path LIKE '%" . $wpdb->escape( $_POST['search'] ) . "%' ) AND blog_id != '" . $wpdb->blogid . "' LIMIT 150";
					$blogs = $wpdb->get_results( $query, ARRAY_A );
					if ( count( $blogs ) > 0 ) {
						if ( count( $blogs ) >= 150 ) {
							?>
							<span
								class="description"><?php _e( 'Es wurden über 150 Webseiten gefunden, die den angegebenen Suchkriterien entsprechen. Wenn Du die gesuchte Website in der folgenden Auswahl nicht findest, versuche bitte, Deine Suche zu verfeinern.', 'psts' ) ?></span>
						<?php
						}
						?>
						<p>
						<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
							<thead>
							<tr>
								<th scope='col' width='75px'><?php _e( 'Werbungen ausschalten', 'psts' ); ?></th>
								<th scope='col'><?php _e( 'Webseite', 'psts' ); ?></th>
							</tr>
							</thead>
							<tbody id='the-list'>
							<?php
						$class = '';
						foreach ( $blogs as $blog ) {
							$blog_details = get_blog_details( $blog['blog_id'] );

							//=========================================================//
							echo "<tr class='" . $class . "'>";
							$existing_check = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->base_prefix . "supporter_ads WHERE supporter_blog_ID = '" . $wpdb->blogid . "' AND blog_ID = '" . $blog['blog_id'] . "'" );
							if ( $existing_check > 0 ) {
								echo "<td valign='top'><center><input name='blogs[" . $blog['blog_id'] . "]' id='blog_{$blog['blog_id']}' value='1' type='checkbox' disabled='disabled'></center></td>";
							} else {
								echo "<td valign='top'><center><input name='blogs[" . $blog['blog_id'] . "]' id='blog_{$blog['blog_id']}' value='1' type='checkbox'></center></td>";
							}
							if ( $existing_check > 0 ) {
								echo "<td valign='top' style='color:#666666;'><strong>" . $blog_details->blogname . " (<em>" . $blog_details->domain . "</em>): " . __( 'Anzeigen bereits deaktiviert', 'psts' ) . "</strong></td>";
							} else {
								echo "<td valign='top'><label for='blog_{$blog['blog_id']}'><strong>" . $blog_details->blogname . " (<em>" . $blog_details->domain . "</em>)</strong></label></td>";
							}
							echo "</tr>";
							$class = ( 'alternate' == $class ) ? '' : 'alternate';
							//=========================================================//
						}
						?>
							</tbody>
						</table></p>
						<p class="submit">
						<input type="submit" name="back" value="&laquo; <?php _e( 'Zurück', 'psts' ) ?>"/>
						<input type="submit" name="submit_process" value="<?php _e( 'Werbungen ausschalten', 'psts' ) ?> &raquo;"/>
					<?php } else { ?>
						<p><?php _e( 'Es wurden keine Webseiten gefunden, die Deinen Suchkriterien entsprechen.', 'psts' ) ?></p>
					<?php } ?>
				</div>
			</div>

		<?php } else { ?>

			<?php if ( $ad_free_blogs_current > 0 && is_pro_site() ) { ?>
				<div class="postbox">
					<h3 class="hndle" style="cursor:auto;">
						<span><?php _e( 'Derzeit deaktivierte Webseiten', 'psts' ); ?></span></h3>

					<div class="inside">
						<p>
						<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
							<thead>
							<tr>
								<th scope='col' width='45px'><?php _e( 'Remove', 'psts' ); ?></th>
								<th scope='col'><?php _e( 'Webseite', 'psts' ); ?></th>
							</tr>
							</thead>
							<tbody id='the-list'>
							<?php
							$class = '';
							foreach ( $blogs as $blog ) {
								$blog_details = get_blog_details( $blog['blog_ID'] );
								//=========================================================//
								echo "<tr class='" . $class . "'>";
								echo "<td valign='top' style='text-align: center;'><input name='blogs[" . $blog['blog_ID'] . "]' id='blog_rm_{$blog['blog_ID']}' value='1' type='checkbox'></td>";
								echo "<td valign='top'><label for='blog_rm_{$blog['blog_ID']}'><strong>" . $blog_details->blogname . " (<em>" . $blog_details->domain . "</em>)</strong></label></td>";
								echo "</tr>";
								$class = ( 'alternate' == $class ) ? '' : 'alternate';
								//=========================================================//
							}
							?>
							</tbody>
						</table>
						</p>
						<p class="submit">
							<input type="submit" id="submit_remove" name="submit_remove"
							       value="<?php _e( 'Entfernen', 'psts' ) ?> &raquo;"/>
						</p>
					</div>
				</div>
			<?php } ?>

		<?php
		}

		echo '</div></form></div>';
	}

	public static function is_included( $level_id ) {
		switch ( $level_id ) {
			default:
				return false;
		}
	}

	/**
	 * Returns the minimum required level to remove restrictions
	 */
	public static function required_level() {
		global $psts;

		return $psts->get_setting( 'ads_level' );

	}

	public static function get_level_status( $level_id ) {
		global $psts;

		$min_level = $psts->get_setting( 'ads_level', 1 );

		if( $level_id >= $min_level ) {
			return 'tick';
		} else {
			return 'cross';
		}

	}

}

/* Ads functions used by other plugins */
function psts_show_ads( $blog_id = null ) {
	global $ProSites_Module_Ads;

	if ( isset( $ProSites_Module_Ads ) && is_object( $ProSites_Module_Ads ) ) {
		return $ProSites_Module_Ads->show_ads( $blog_id );
	} else {
		return true;
	}
}

function psts_hide_ads( $blog_id = null ) {
	global $ProSites_Module_Ads;

	if ( isset( $ProSites_Module_Ads ) && is_object( $ProSites_Module_Ads ) ) {
		return $ProSites_Module_Ads->hide_ads( $blog_id );
	} else {
		return false;
	}
}

//use to detmine if ads module is enabled
function psts_ads_upgrade_active() {
	global $ProSites_Module_Ads, $psts;

	if ( isset( $ProSites_Module_Ads ) && is_object( $ProSites_Module_Ads ) ) {
		return (bool) $psts->get_setting( 'ads_enable_blogs' );
	} else {
		return false;
	}
}


/* deprecated functions */
function supporter_show_ads( $blog_id = null ) {
	return psts_show_ads( $blog_id );
}

function supporter_hide_ads( $blog_id = null ) {
	return psts_hide_ads( $blog_id );
}

function supporter_ads_check( $blog_id = null ) {
	return psts_hide_ads( $blog_id );
}

function supporter_ads() {
	return true;
}