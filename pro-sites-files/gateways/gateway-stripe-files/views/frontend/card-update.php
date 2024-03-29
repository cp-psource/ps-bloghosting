<?php
/**
 * Settings page for Stripe.
 *
 * @package    View
 * @subpackage Stripe
 */

// Globals.
global $psts, $current_site, $current_user;
?>

<form action="<?php echo $url; ?>" method="post" id="psts-stripe-update">
	<input type="hidden" name="update_stripe_card" value="1">
	<button type="button" id="psts-stripe-card-update" title="<?php esc_attr_e( 'Aktualisiere Deine Kartendaten auf Stripe', 'psts' ); ?>"><?php esc_attr_e( 'Kartendetails aktualisieren', 'psts' ); ?></button>
</form>