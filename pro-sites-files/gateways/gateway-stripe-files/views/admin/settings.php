<?php
/**
 * Settings page for Stripe.
 *
 * @package    View
 * @subpackage Stripe
 */

// Pro Sites global.
global $psts;

?>
<div class="inside">
	<p class="description">
		<?php esc_html_e( 'Akzeptiere Visa-, MasterCard-, American Express-, Discover-, JCB- und Diners Club-Karten direkt auf Deiner Webseite. Du benötigst kein Händlerkonto oder Gateway. Stripe kümmert sich um alles, einschließlich der Speicherung von Karten, Abonnements und direkten Auszahlungen auf Dein Bankkonto. Kreditkarten gehen direkt in die sichere Umgebung von Stripe und gelangen nie auf Deine Server, sodass Du die meisten PCI-Anforderungen umgehen kannst.', 'psts' ); ?>
		<a href="https://stripe.com/" target="_blank"><?php _e( 'Mehr Info &raquo;', 'psts' ); ?></a>
	</p>

	<p><?php printf( __( 'Um Stripe verwenden zu können, musst Du %1$diese Webbook-URL%2$s (%3$s) in Deinem Konto eingeben.', 'psts' ), '<a href="https://dashboard.stripe.com/account/webhooks" target="_blank">', '</a>', '<strong>' . network_site_url( 'wp-admin/admin-ajax.php?action=psts_stripe_webhook', 'admin' ) . '</strong>' ); ?></p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Stripe Modus', 'psts' ); ?></th>
			<td>
				<select name="psts[stripe_ssl]" class="chosen">
					<option value="1" <?php selected( $psts->get_setting( 'stripe_ssl' ), 1 ); ?>><?php esc_html_e( 'SSL erzwingen (Live-Site)', 'psts' ); ?></option>
					<option value="0" <?php selected( $psts->get_setting( 'stripe_ssl' ), 0 ); ?>><?php esc_html_e( 'Kein SSL (Test)', 'psts' ); ?></option>
				</select>
				<br/>
				<span class="description"><?php esc_html_e( 'Im Live-Modus empfiehlt Stripe die Einrichtung eines SSL-Zertifikats für Deinen Hauptblog/Deine Hauptseite, auf der das Checkout-Formular angezeigt wird.', 'psts' ); ?>
					<a href="https://stripe.com/help/ssl" target="_blank"><?php esc_html_e( 'Mehr Info &raquo;', 'psts' ); ?></a>
                </span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Stripe-API-Anmeldeinformationen', 'psts' ); ?></th>
			<td>
				<p>
					<label><?php esc_html_e( 'Geheimer Schlüssel', 'psts' ); ?><br/>
						<input value="<?php echo esc_attr( $psts->get_setting( 'stripe_secret_key' ) ); ?>" size="70" name="psts[stripe_secret_key]" type="text"/>
					</label>
				</p>

				<p>
					<label><?php esc_html_e( 'Öffentlicher Schlüssel', 'psts' ); ?><br/>
						<input value="<?php echo esc_attr( $psts->get_setting( "stripe_publishable_key" ) ); ?>" size="70" name="psts[stripe_publishable_key]" type="text"/>
					</label>
				</p>
				<br/>
				<span class="description"><?php printf( __( 'Du musst Dich bei Stripe anmelden, um %1$sDeine API-Anmeldeinformationen zu erhalten%2$s. Du kannst Deinee Testanmeldeinformationen eingeben und anschließend die Live-Zugangsdaten eingeben, wenn Du bereit bist. Wenn Du von Test- zu Live-API-Anmeldeinformationen wechselst und auf einer Seite testest, die im Live-Modus verwendet wird, musst Du die zugehörige Zeile manuell aus der Tabelle *_pro_sites_stripe_customers für das angegebene Blogid löschen, um Fehler beim Auschecken oder bei der Verwaltung zu verhindern.', 'psts' ), '<a target="_blank" href="https://dashboard.stripe.com/account/apikeys">', '</a>' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="psts-help-div psts-stripe-currency"><?php echo esc_html__( 'Stripe Wärung', 'psts' ); ?></th>
			<td>
				<p>
					<strong><?php echo $psts->get_setting( 'currency', 'EUR' ); ?></strong> &ndash;
					<span class="description"><?php printf( __( '%1$sWährung ändern%2$s', 'psts' ), '<a href="' . network_admin_url( 'admin.php?page=psts-settings&tab=payment' ) . '">', '</a>' ); ?></span>
				</p>
				<p class="description"><?php esc_html_e( 'Die Währung muss mit der Währung Deines Stripe-Kontos übereinstimmen.', 'psts' ); ?></p>
				<p class="description">
					<strong><?php esc_html_e( 'Für Null-Dezimal-Währungen wie den japanischen Yen sollten die Mindestplankosten mehr als 50 Cent betragen.', 'psts' ); ?></strong>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="psts-help-div psts-stripe-thankyou"><?php echo esc_html__( 'Dankesnachricht', 'psts' ) . $psts->help_text( esc_html__( 'Wird bei erfolgreichem Bezahlvorgang angezeigt. Dies ist auch ein guter Ort, um Conversion-Tracking-Skripte wie von Google Analytics einzufügen. - HTML erlaubt', 'psts' ) ); ?></th>
			<td>
				<textarea name="psts[stripe_thankyou]" type="text" rows="4" wrap="soft" id="stripe_thankyou" style="width: 100%"><?php echo esc_textarea( stripslashes( $psts->get_setting( 'stripe_thankyou' ) ) ); ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html__( 'Aktiviere Debug', 'psts' ); ?></th>
			<td>
				<input name="psts[stripe_debug]" type="checkbox" value="1" <?php checked( $psts->get_setting( 'stripe_debug' ), 1 ); ?>>
				<span class="description"><?php esc_html_e( 'Aktiviere Debug, um Stripe-Fehler in Deinem PHP-Fehlerprotokoll zu protokollieren.', 'psts' ); ?></span>
			</td>
		</tr>
	</table>
</div>
