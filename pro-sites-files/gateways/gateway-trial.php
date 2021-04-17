<?php
class ProSites_Gateway_Trial {
	public static function get_existing_user_information( $blog_id, $domain, $get_all = true ) {
		global $psts;
		$end_date     = date_i18n( get_option( 'date_format' ), $psts->get_expire( $blog_id ) );
		$level        = $psts->get_level_setting( $psts->get_level( $blog_id ), 'name' );

		$args = array();

		$args['level'] = $level;
		$args['expires'] = $end_date;
		$args['trial'] = '<div id="psts-general-error" class="psts-warning">' . sprintf( __( 'Du hast derzeit eine Test-Webseite. Deine Funktionen verfallen in Zukunft. Bitte aktualisiere Deine Webseite, um weiterhin die Funktionen Deines %s-Levels nutzen zu können. oder wähle einen Plan, der Deinen Anforderungen besser entspricht.', 'psts' ), $level ) . '</div>';

		return $args;
	}
}