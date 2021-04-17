<?php

if ( ! class_exists( 'ProSites_View_Settings' ) ) {
	class ProSites_View_Settings {

		public static function render_page() {

			if ( ! is_super_admin() ) {
				echo "<p>" . __( 'Netter Versuch...', 'psts' ) . "</p>"; //If accessed properly, this message doesn't appear.
				return false;
			}

			// Might move this to a controller, not sure if needed yet.
			ProSites_Model_Settings::process_form();

			?>
			<form method="post" action="">
				<?php

				$page_header_options = array(
					'title'       => __( 'Bloghosting Einstellungen', 'psts' ),
					'desc'        => '',
					'page_header' => true,
				);

				$options = array(
					'header_save_button'  => true,
					'section_save_button' => true,
					'nonce_name'          => 'psts_settings',
					'button_name'         => 'settings',
				);

				ProSites_Helper_Tabs_Settings::render( get_class(), $page_header_options, $options );

				?>

			</form>
			<?php

		}

		/**
		 * General Settings
		 *
		 * @return string
		 */
		public static function render_tab_general() {
			global $psts, $current_site;

			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );

//			$levels = (array) get_site_option( 'psts_levels' );

			//allow overriding and changing the root site to put the checkout page on
			$checkout_site = defined( 'PSTS_CHECKOUT_SITE' ) ? constant( 'PSTS_CHECKOUT_SITE' ) : $current_site->blog_id;

			//insert new page if not existing
			switch_to_blog( $checkout_site );
			$page_id       = $psts->get_setting( 'checkout_page' );
			$post_status   = get_post_status( $page_id );
			$checkout_link = false !== $post_status && 'trash' != $post_status ? get_edit_post_link( $page_id ) : false;
			restore_current_blog();

			?>

			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-rebrand-pro"><?php echo __( 'Rebrand Bloghosting', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Benenne "Bloghosting" für Benutzer in "Pro" oder "Plus" um..', 'psts' ) ); ?></th>
						<td>
							<input type="text" name="psts[rebrand]"
							       value="<?php echo esc_attr( $psts->get_setting( 'rebrand' ) ); ?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Beschriftungen der Admin-Menüschaltflächen', 'psts' ) ?></th>
						<td>
							<label>
								<span class="psts-label psts-label-notpro"><?php _e( 'Nicht Pro', 'psts' ); ?></span>
								<input type="text" name="psts[lbl_signup]"
								       value="<?php echo esc_attr( $psts->get_setting( 'lbl_signup' ) ); ?>"/>
							</label><br/>
							<label>
								<span
									class="psts-label psts-label-currentpro"><?php _e( 'Aktueller Pro', 'psts' ); ?></span>
								<input type="text" name="psts[lbl_curr]"
								       value="<?php echo esc_attr( $psts->get_setting( 'lbl_curr' ) ); ?>"/>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Admin-Menü ausblenden', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[hide_adminmenu]"
							              value="1"<?php checked( $psts->get_setting( 'hide_adminmenu' ) ); ?> />
								<?php _e( 'Entferne den Menüpunkt Bloghosting-Upgrade', 'psts' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Adminbar Schaltfläche ausblenden', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[hide_adminbar]"
							              value="1"<?php checked( $psts->get_setting( 'hide_adminbar' ) ); ?> />
								<?php _e( 'Entferne die Menüschaltfläche für das Upgrade von Blogosting aus der Admin-Leiste', 'psts' ); ?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Pro-Status für Superadmin ausblenden', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[hide_adminbar_super]"
							              value="1"<?php checked( $psts->get_setting( 'hide_adminbar_super' ) ); ?> />
								<?php _e( 'Entferne das Statusmenü für Superadmin aus der Admin-Leiste', 'psts' ); ?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div force-redirect-expiraton"><?php echo __( 'Umleitung bei Ablauf erzwingen', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Leite den Seiten-Administrator nach Ablauf des Abonnents zur Checkout-Seite um', 'psts' ), 'force-redirect' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[psts_force_redirect]"
							              value="1"<?php checked( $psts->get_setting( 'psts_force_redirect', 1 ) ); ?> /></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-free-level psts-help-div"><?php echo __( 'Gratislevel', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Bloghosting verfügt standardmäßig über eine integrierte kostenlose Ebene. Konfiguriere wie diese Ebene auf dem Checkout-Formular angezeigt wird:', 'psts' ) ); ?></th>
						<td>
							<label>
								<span class="psts-label psts-label-name"><?php _e( 'Name', 'psts' ); ?></span>
								<input type="text" name="psts[free_name]"
								       value="<?php echo esc_attr( $psts->get_setting( 'free_name' ) ); ?>"/>
							</label><br/>
							<label>
								<span class="psts-label psts-label-message"><?php _e( 'Nachricht', 'psts' ); ?></span>
								<input type="text" size="50" name="psts[free_msg]"
								       value="<?php echo esc_attr( $psts->get_setting( 'free_msg' ) ); ?>"/>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="pay-for-signup"><?php echo __( 'Anmeldung an der Kasse<br /><small>Deaktiviert die WordPress-Anmeldung</small>', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Ermöglicht dem Benutzer, sich über die Checkout-Seite für eine Webseite anzumelden. Testversionen werden automatisch aktiviert, Webseiten werden aktiviert, nachdem die Zahlung verarbeitet wurde (oder manuell)..', 'psts' ) ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[show_signup]"
							              value="1"<?php checked( $psts->get_setting( 'show_signup' ) ); ?> />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="free-signup"><?php echo __( 'Kostenlose Anmeldung zulassen<br /><small>* Anmeldung an der Kasse</small>', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Ermögliche dem Benutzer, sich für einen kostenlosen Standard-Blog anzumelden.', 'psts' ) ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[free_signup]"
							              value="1"<?php checked( $psts->get_setting( 'free_signup' ) ); ?> />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="multiple-signup"><?php echo __( 'Erlaube mehrere Blogs<br /><small>* Anmeldung an der Kasse</small>', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Ermögliche einzelnen Benutzern, mehrere Blogs zu registrieren.', 'psts' ) ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[multiple_signup]"
							              value="1"<?php checked( $psts->get_setting( 'multiple_signup' ) ); ?> />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-show-signup-message"><?php echo __( 'Anmeldemeldung anzeigen', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Zeige eine Anmeldemeldung über der Checkout-Tabelle an', 'psts' ) ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="psts[show_signup_message]"
								       value="1"<?php checked( $psts->get_setting( 'show_signup_message' ) ); ?> />
							</label>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-signup-message"><?php echo __( 'Anmeldemeldung', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Optional - HTML zulässig - Diese Meldung wird auf der Anmeldeseite angezeigt, wenn das Kontrollkästchen oben aktiviert ist.', 'psts' ) ); ?></th>
						<td>
							<textarea name="psts[signup_message]" rows="3" wrap="soft" id="signup_message"
							          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'signup_message' ) ); ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-checkout-page"><?php echo __( 'Checkout-Seite', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Wenn die Checkout-Seite nicht gefunden wird, wird beim Speichern der Einstellungen eine neue Checkout-Seite generiert. Der Slug und der Titel basieren auf der oben genannten Rebranding-Option.', 'psts' ) ); ?></th>
						<td>
							<?php if ( empty( $checkout_link ) ) { ?>
								<?php _e( 'Beim Auffinden der Checkout-Seite ist ein Problem aufgetreten. Diese wird erstellt, wenn Du die Einstellungen auf dieser Seite speicherst.', 'psts' ); ?>
							<?php } else { ?>
								<a href="<?php echo $checkout_link; ?>"
								   title="<?php _e( 'Checkout-Seite bearbeiten &raquo;', 'psts' ); ?>"><?php _e( 'Checkout-Seite bearbeiten &raquo;', 'psts' ); ?></a>
							<?php } ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Checkout-Berechtigungen', 'psts' ) ?></th>
						<td><?php

							$roles          = get_editable_roles();
							$checkout_roles = $psts->get_setting( 'checkout_roles', 'not_set' );

							foreach ( $roles as $role_key => $role ) {
								$checked = '';
								//Default keep all applicable roles checked
								if ( ( is_array( $checkout_roles ) && in_array( $role_key, $checkout_roles ) ) || $checkout_roles == 'not_set' ) {
									$checked = 'checked="checked"';
								}
								if ( ! empty ( $role['capabilities']['manage_options'] ) || ! empty( $role['capabilities']['edit_pages'] ) ) {
									?>
									<label>
										<input type="checkbox" name="psts[checkout_roles][]"
										       value="<?php echo $role_key; ?>" <?php echo $checked; ?>/><?php echo $role['name']; ?>
									</label> <?php
								}
							}

							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-feature-message"><?php echo __( 'Bloghosting Feature-Nachricht', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Erforderlich - Kein HTML - Diese Meldung wird angezeigt, wenn jemands auf eine Funktion auf einer Site zugegreifen möchte, die keinen Zugriff darauf hat. "LEVEL" wird durch den für die Funktion erforderlichen Ebenennamen ersetzt.', 'psts' ) ); ?></th>
						<td>
							<input name="psts[feature_message]" type="text" id="feature_message"
							       value="<?php echo esc_attr( $psts->get_setting( 'feature_message' ) ); ?>"
							       style="width: 95%"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-free-trial psts-help-div"><?php echo __( 'Kostenlose Testphase', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Freie Tage für alle neuen Seiten', 'psts' ) ); ?></th>
						<td><select name="psts[trial_days]" class="chosen">
								<?php
								$trial_days         = $psts->get_setting( 'trial_days' );
								$trial_days_options = '';

								for ( $counter = 0; $counter <= 365; $counter ++ ) {
									$trial_days_options .= '<option value="' . $counter . '"' . ( $counter == $trial_days ? ' selected' : '' ) . '>' . ( ( $counter ) ? $counter : __( 'Deaktiviert', 'psts' ) ) . '</option>' . "\n";
								}

								//allow plugins to modify the trial days options (some people want to display as years, more than one year, etc)
								echo apply_filters( 'psts_trial_days_options', $trial_days_options );
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-trial-message psts-help-div"><?php echo __( 'Kostenlose Testphase Nachricht', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Erforderlich - Diese Meldung wird im Dashboard angezeigt und gibt an, wie viele Tage noch in der kostenlosen Testversion verbleiben. "DAYS" wirde durch die Anzahl der verbleibenden Tage in der Testversion ersetzt. "LEVEL" wird durch den erforderlichen Levelnamen ersetzt.', 'psts' ) ); ?></th>
						<td>
							<input type="text" name="psts[trial_message]" id="trial_message"
							       value="<?php esc_attr_e( $psts->get_setting( 'trial_message' ) ); ?>"
							       style="width: 95%"/>
						</td>
					</tr>
					<!--<tr valign="top">
						<th scope="row"
						    class="psts-cancellation psts-help-div"><?php // echo __( 'Cancellation Message', 'psts' ) . ProSites_Helper_UI::help_text( __( 'This message is displayed on the checkout screen notifying FREE TRIAL and NEW customers of your cancellation policy. "DAYS" will be replaced with the number of "Cancellation Days" set above.', 'psts' ) ); ?></th>
						<td>
							<textarea style="width:95%" wrap="soft" rows="3"
							          name="psts[cancel_message]"><?php // echo $psts->get_setting( 'cancel_message', __( 'Your DAYS day trial begins once you click "Subscribe" below. We perform a $1 pre-authorization to ensure your credit card is valid, but we won\'t actually charge your card until the end of your trial. If you don\'t cancel by day DAYS, your card will be charged for the subscription amount shown above. You can cancel your subscription at any time.', 'psts' ) ); ?></textarea><br/>
						</td>
					</tr>-->
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-setup-fee"><?php echo __( 'Einrichtungsgebühr', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Für kostenlose Websites wird keine Einrichtungsgebühr erhoben, bis ein Upgrade auf ein kostenpflichtiges Level erfolgt. Wenn für Upgrades von einer beliebigen Ebene eine Einrichtungsgebühr erhoben werden soll, aktiviere "Einrichtungsgebühr auf Upgrades anwenden".', 'psts' ) ); ?></th>
						<td>
							<label><?php echo $psts->format_currency(); ?></label><input type="text" name="psts[setup_fee]" size="4" value="<?php echo ( $setup_fee = ProSites_Helper_Settings::setup_fee() ) ? number_format( (float) $setup_fee, 2, '.', '' ) : ''; ?>"/>
							<span class="description"><?php printf( __( '%sHinweis:%s Gutscheine werden nicht auf die Einrichtungsgebühr angewendet.', 'psts' ), '<strong>', '</strong>' ); ?></span>
							<br/><br/>
							<label for="psts-apply-setup-fee-upgrade">
								<input type="checkbox" name="psts[apply_setup_fee_upgrade]"
								       id="psts-apply-setup-fee-upgrade"
								       value="1" <?php checked( $psts->get_setting( 'apply_setup_fee_upgrade', 0 ), 1 ); ?> />
								<label
									for="psts-apply-setup-fee-upgrade"><?php _e( 'Wende eine Einrichtungsgebühr auf Upgrades an', 'psts' ); ?></label>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-recurring"><?php echo __( 'Wiederkehrende Abonnements', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Durch das Deaktivieren wiederkehrender Abonnements müssen Benutzer nach Ablauf ihrer Laufzeit manuell erneut abonnieren.', 'psts' ) ); ?></th>
						<td>
							<label for="psts-recurring-subscriptions-on" style="margin-right:10px">
								<input type="radio" name="psts[recurring_subscriptions]"
								       id="psts-recurring-subscriptions-on"
								       value="1" <?php checked( $psts->get_setting( 'recurring_subscriptions', 1 ), 1 ); ?> /> <?php _e( 'Aktivieren', 'psts' ); ?>
							</label>
							<label for="psts-subscriptions-off">
								<input type="radio" name="psts[recurring_subscriptions]"
								       id="psts-recurring-subscriptions-off"
								       value="0" <?php checked( $psts->get_setting( 'recurring_subscriptions', 1 ), 0 ); ?> /> <?php _e( 'Deaktivieren', 'psts' ); ?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Google Analytics Ecommerce Tracking', 'psts' ) ?></th>
						<td>
							<select name="psts[ga_ecommerce]" class="chosen">
								<option
									value="none"<?php selected( $psts->get_setting( 'ga_ecommerce' ), 'none' ) ?>><?php _e( 'Kein Tracking', 'psts' ) ?></option>
								<option
									value="new"<?php selected( $psts->get_setting( 'ga_ecommerce' ), 'new' ) ?>><?php _e( 'Asynchroner Tracking Code', 'psts' ) ?></option>
								<option
									value="old"<?php selected( $psts->get_setting( 'ga_ecommerce' ), 'old' ) ?>><?php _e( 'Alter Tracking Code', 'psts' ) ?></option>
							</select>
							<br/><span
								class="description"><?php _e( 'Wenn Du Google Analytics bereits für Deine Webseite verwendst, kannst Du detaillierte E-Commerce-Informationen verfolgen, indem Du diese Einstellung aktivierst. Wähle aus, ob Du den neuen asynchronen (analyse.js) oder alten Tracking-Code verwendest. Bevor Google Analytics E-Commerce-Aktivitäten für Deine Webseite melden kann, musst Du das E-Commerce-Tracking auf der Seite mit den Profileinstellungen für Deine Webseite aktivieren. <a href="http://analytics.blogspot.com/2009/05/how-to-use-ecommerce-tracking-in-google.html" target="_blank">Mehr Information &raquo;</a>', 'psts' ) ?></span>
						</td>
					</tr>
					<?php if ( is_ssl() ) : ?>
						<tr valign="top">
							<th scope="row"
							    class="psts-help-div psts-ssl"><?php echo __( 'SSL für neue Webseiten', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Aktiviere diese Option, wenn für alle neuen Unterseiten Platzhalter-SSL verfügbar ist. Dies gilt nur, wenn SSL aktiviert ist und Multisite das Subdomain-Setup verwendet.', 'psts' ) ); ?></th>
							<td>
								<label for="psts-subsites-ssl-on">
									<input type="radio" name="psts[subsites_ssl]" id="psts-subsites-ssl-on" value="1" <?php checked( $psts->get_setting( 'subsites_ssl', 1 ), 1 ); ?> /> <?php _e( 'Aktivieren', 'psts' ); ?>
								</label>
								<label for="psts-subsites-ssl-off">
									<input type="radio" name="psts[subsites_ssl]" id="psts-subsites_ssl-off" value="0" <?php checked( $psts->get_setting( 'subsites_ssl', 1 ), 0 ); ?> /> <?php _e( 'Deaktivieren', 'psts' ); ?>
								</label>
							</td>
						</tr>
					<?php endif; ?>
					<?php do_action( 'psts_general_settings' ); ?>
				</table>
			</div>


			<?php
		}

		/**
		 * E-mail Settings
		 *
		 * @return string
		 */
		public static function render_tab_email() {
			global $psts;

			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$defaults = ProSites::get_default_settings_array();
			?>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"
						    class="psts-help-div psts-pro-signup"><?php echo __( 'Bloghosting-Anmeldung', 'psts' ) . ProSites_Helper_UI::help_text( __( 'An den Benutzer gesendete Bestätigungs-E-Mail für die Bloghosting-Anmeldung', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[success_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'success_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[success_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'success_msg' ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-pro-site-cancelled"><?php echo __( 'Bloghosting Kündigung', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Die an den Benutzer gesendete E-Mail zur Kündigung der Mitgliedschaft "ENDDATE" wird durch das Datum ersetzt, an dem der Zugriff auf die Webseite endet.', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[canceled_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'canceled_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[canceled_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'canceled_msg' ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-payment-reciept"><?php echo __( 'Zahlungsbeleg', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Zahlungsbestätigung Quittung. Du musst den Code "PAYMENTINFO" angeben, der durch die Zahlungsdetails ersetzt wird.', 'psts' ) ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[send_receipts]"
							              value="1"<?php checked( $psts->get_setting( 'send_receipts', 1 ) ); ?> /><?php _e( 'Sende PDF-Belege', 'psts' ); ?>
							</label><br/>
							<br/>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[receipt_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'receipt_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[receipt_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'receipt_msg' ) ); ?></textarea></label><br/>
							<br/>
							<label><?php _e( 'Header-Bild-URL (für PDF-Anhang):', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_img" name="psts[receipt_image]"
								       value="<?php echo esc_attr( $psts->get_setting( 'receipt_image' ) ); ?>"
								       maxlength="150" style="width: 65%"/></label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-expiration-mail"><?php echo __( 'Ablauf-E-Mail', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Webseitenabo-Ablauf-E-Mail an Benutzer gesendet. "CHECKOUTURL" wird durch die URL ersetzt, um das Webseitenabo zu aktualisieren.', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[expired_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'expired_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[expired_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'expired_msg' ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-payment-problem"><?php echo __( 'Zahlungsproblem', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Der E-Mail-Text, der an Deine Kunden gesendet wird, wenn eine geplante Zahlung fehlschlägt.', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[failed_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'failed_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[failed_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'failed_msg' ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-pro-manual-extension"><?php echo __( 'Bloghosting Manuelle Erweiterung', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Bloghosting-E-Mail, die an den Benutzer gesendet wird, wenn die Webseite manuell erweitert wird.', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[extension_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'extension_subject' ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[extension_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'extension_msg' ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"
						    class="psts-help-div psts-pro-permanent-revoked"><?php echo __( 'Bloghosting Permanent Status widerrufen', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Bloghosting-E-Mail an Benutzer gesendet, wenn der permanente Status widerrufen wurde.', 'psts' ) ); ?></th>
						<td>
							<label><?php _e( 'Betreff:', 'psts' ); ?><br/>
								<input type="text" class="pp_emails_sub" name="psts[revoked_subject]"
								       value="<?php echo esc_attr( $psts->get_setting( 'revoked_subject', $defaults['revoked_subject'] ) ); ?>"
								       maxlength="150" style="width: 95%"/></label><br/>
							<label><?php _e( 'Nachricht:', 'psts' ); ?><br/>
								<textarea class="pp_emails_txt" name="psts[revoked_msg]"
								          style="width: 95%"><?php echo esc_textarea( $psts->get_setting( 'revoked_msg', $defaults['revoked_msg'] ) ); ?></textarea>
							</label>
						</td>
					</tr>
					<?php do_action( 'psts_email_settings' ); ?>
				</table>
			</div>

			<?php
		}

		/**
		 * 'Payment Settings'
		 *
		 * @return string
		 */
		public static function render_tab_payment() {
			global $psts;

			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			?>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Währung', 'psts' ); ?><?php echo $psts->help_text( esc_html__( 'Dies ist die Währung, in der Kunden belastet werden. Deine Gateway-Währung ist eine Ersatzoption.', 'psts' ), 'site-currency' ); ?></th>
						<td>
							<select id="psts-currency-select" name="psts[currency]" class="chosen"
							        data-placeholder="<?php echo esc_attr__( 'Aktiviere Gateways', 'psts' ); ?>">
								<?php
								$super    = array(
									'&#8304;',
									'&#185;',
									'&#178;',
									'&#179;',
									'&#8308;',
									'&#8309;',
									'&#8310;',
									'&#8311;',
									'&#8312;',
									'&#8313;',
								);
								$gateways = ProSites_Helper_Gateway::get_gateways();

								$count         = 0;
								$supported_key = '';
								foreach ( $gateways as $key => $gateway ) {
									$count ++;
									$gateways[ $key ]['idx'] = $count;
									if ( $count > 1 ) {
										$supported_key .= '<sup> | </sup>';
									}
									$supported_key .= '<sup>' . $count . ' - ' . $gateway['name'] . '</sup>';

								}
								//supports_currency
								//foreach ( $psts->currencies as $key => $value ) {
								$all_currencies = ProSites_Model_Data::$currencies;
								ksort( $all_currencies );
								foreach ( $all_currencies as $key => $currency ) {

									$supported_by = '';
									foreach ( $gateways as $slug => $gateway ) {
										if ( ProSites_Helper_Gateway::supports_currency( $key, $slug ) ) {
											if ( strlen( $supported_by ) > 0 ) {
												$supported_by .= '&#x207B;';
											}
											$supported_by .= $super[ $gateway['idx'] ];
										}
									} ?>
									<option value="<?php echo $key; ?>"<?php selected( $psts->get_setting( 'currency' ), $key ); ?>><?php echo esc_attr( strtoupper( $key ) ) . '' . $supported_by . ' - ' . esc_attr( $currency['name'] ) . ' - ' . $psts->format_currency( $key ); ?></option><?php
								} ?>
							</select>
							<div>
								<?php echo $supported_key; ?><br/>
								<?php echo sprintf( '<sup>%s</sup>', esc_html__( 'Hinweis: Wenn eine Währung von Deinem Gateway nicht unterstützt wird, wird sie möglicherweise auf die Währung Deines Händlerkontos zurückgesetzt. (z.B. Stripe)', 'psts' ) ); ?>
								<?php echo sprintf( '<sup><br />%s</sup>', esc_html__( 'Hinweis: Das Aktualisieren Deiner Webseite-Währung kann einige Zeit dauern. Bitte habe etwas Geduld.', 'psts' ) ); ?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Position des Währungssymbols', 'psts' ) ?></th>
						<td>
							<label><input value="1" name="psts[curr_symbol_position]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_symbol_position', 1 ), 1 ); ?>>
								<?php echo $psts->format_currency(); ?>100</label><br/>
							<label><input value="2" name="psts[curr_symbol_position]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_symbol_position' ), 2 ); ?>>
								<?php echo $psts->format_currency(); ?> 100</label><br/>
							<label><input value="3" name="psts[curr_symbol_position]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_symbol_position' ), 3 ); ?>>
								100<?php echo $psts->format_currency(); ?></label><br/>
							<label><input value="4" name="psts[curr_symbol_position]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_symbol_position' ), 4 ); ?>>
								100 <?php echo $psts->format_currency(); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Dezimalzahl in Preisen anzeigen', 'psts' ) ?></th>
						<td>
							<label><input value="1" name="psts[curr_decimal]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_decimal', 1 ), 1 ); ?>>
								<?php _e( 'Ja', 'psts' ) ?></label>
							<label><input value="0" name="psts[curr_decimal]"
							              type="radio"<?php checked( $psts->get_setting( 'curr_decimal' ), 0 ); ?>>
								<?php _e( 'Nein', 'psts' ) ?></label>
						</td>
					</tr>
				</table>
			</div>
			<?php
		}

		/**
		 * 'Payment Settings'
		 *
		 * @return string
		 */
		public static function render_tab_taxes() {
			global $psts;

			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			?>
			<div class="inside">
				<!--<table class="form-table">-->
				<!--</table>-->
				<!--<hr />-->
				<h3 class="psts-settings-title"><br/>EU VAT - Taxamo Integration</h3>
				<div class="psts-settings-desc psts-description">Setup integration with Taxamo.com to handle your EU VAT
					requirements.
				</div>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"
						    class="pay-for-signup"><?php echo __( 'Aktiviere Taxamo', 'psts' ); ?></th>
						<td>
							<label><input type="checkbox" name="psts[taxamo_status]"
							              value="1"<?php checked( $psts->get_setting( 'taxamo_status' ) ); ?> />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-rebrand-pro"><?php echo __( 'Öffentlicher Taxamo-Schlüssel', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Du musst Deinen API-Token im Taxamo-Dashboard einrichten. Sobald Du Taxamo auf "LIVE" umstellst, musst Du diesen Schlüssel aktualisieren.', 'psts' ) ); ?></th>
						<td>
							<input type="text" name="psts[taxamo_token]"
							       value="<?php echo esc_attr( $psts->get_setting( 'taxamo_token' ) ); ?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"
						    class="psts-help-div psts-rebrand-pro"><?php echo __( 'Privater Taxamo Schlüssel', 'psts' ) . ProSites_Helper_UI::help_text( __( 'Du musst Deinen API-Token im Taxamo-Dashboard einrichten. Sobald Du Taxamo auf "LIVE" umstellst, musst Du diesen Schlüssel aktualisieren.', 'psts' ) ); ?></th>
						<td>
							<input type="text" name="psts[taxamo_private_token]"
							       value="<?php echo esc_attr( $psts->get_setting( 'taxamo_private_token' ) ); ?>"/>
						</td>
					</tr>
				</table>
				<p class="description"><?php echo sprintf( __( 'Erstelle ein Konto bei Taxamo.com. Du kannst Deine API-Schlüssel dann auf der Seite <a href="%s">API-Zugriff</a> abrufen.', 'psts' ), esc_url( 'https://dashboard.taxamo.com/merchant/app.html#/account/api' ) ); ?></p>
				<p class="description"><?php echo sprintf( __( 'Füge Deine Seiten-Domain auch zum Abschnitt "Web-API-Verweise" auf der Seite <a href="%s">JavaScript-API</a> hinzu, damit die Taxamo-Integration funktioniert.', 'psts' ), esc_url( 'https://dashboard.taxamo.com/merchant/app.html#/account/api/javascript' ) ); ?></p>
			</div>
			<?php
		}

		/**
		 * 'Advertising'
		 *
		 * @return string
		 */
		public static function render_tab_ads() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_Ads();
			echo $module->settings();
		}

		/**
		 * 'Automated Email Responses'
		 *
		 * @return string
		 */
//		public static function render_tab_messages_automated() {
//			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
//		}

		/**
		 * 'Pro Sites Widget'
		 *
		 * @return string
		 */
		public static function render_tab_prowidget() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_ProWidget();
			echo $module->settings();
		}


		/**
		 * 'BuddyPress Features'
		 *
		 * @return string
		 */
		public static function render_tab_buddypress() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_BP();
			echo $module->settings();
		}


		/**
		 * 'Bulk Upgrades'
		 *
		 * @return string
		 */
		public static function render_tab_bulkupgrades() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_BulkUpgrades();
			echo $module->settings();
		}


		/**
		 * 'Pay to Blog'
		 *
		 * @return string
		 */
		public static function render_tab_paytoblog() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_PayToBlog();
			echo $module->settings();
		}


		/**
		 * 'Post/Page Throttling'
		 *
		 * @return string
		 */
		public static function render_tab_throttling() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_PostThrottling();
			echo $module->renderModuleSettings();
		}


		/**
		 * 'Post/Page Quotas'
		 *
		 * @return string
		 */
		public static function render_tab_quotas() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_PostingQuota();
			echo $module->settings();
		}


		/**
		 * 'Rename Plugin/Theme Features'
		 *
		 * @return string
		 */
		public static function render_tab_renaming() {
			global $psts;
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );

			$modules = $psts->get_setting( 'modules_enabled' );
			$modules = ! empty( $modules ) ? $modules : array();

			if ( in_array( 'ProSites_Module_PremiumThemes', $modules ) ) {
				$module = new ProSites_Module_PremiumThemes();
				echo $module->settings();
			}
			if ( in_array( 'ProSites_Module_Plugins', $modules ) ) {
				$module = new ProSites_Module_Plugins();
				echo $module->settings();
			}
		}


		/**
		 * 'Premium Support'
		 *
		 * @return string
		 */
		public static function render_tab_support() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_Support();
			echo $module->settings();
		}


		/**
		 * 'Upload Quotas'
		 *
		 * @return string
		 */
		public static function render_tab_upload_quota() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_Quota();
			echo $module->settings();
		}

		/**
		 * 'Upgrade Admin Links'
		 *
		 * @return string
		 */
		public static function render_tab_upgrade_admin_links() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_UpgradeAdminLinks();
			echo $module->settings();
		}

		/**
		 * 'Content/HTML Filter'
		 *
		 * @return string
		 */
		public static function render_tab_filters() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_UnfilterHtml();
			echo $module->settings();
		}


		/**
		 * 'Publishing Limits'
		 *
		 * @return string
		 */
		public static function render_tab_writing() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_Writing();
			echo $module->settings();
		}


		/**
		 * 'Restrict XML-RPC'
		 *
		 * @return string
		 */
		public static function render_tab_xmlrpc() {
			ProSites_Helper_Settings::settings_header( ProSites_Helper_Tabs_Settings::get_active_tab() );
			$module = new ProSites_Module_XMLRPC();
			echo $module->settings();
		}


	}
}