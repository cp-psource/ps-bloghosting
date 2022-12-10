<?php

if ( ! class_exists( 'ProSites_View_Coupons' ) ) {
	class ProSites_View_Coupons {

		public static function get_page_name() {
			return __( 'Bloghosting Gutscheine', 'psts' );
		}

		public static function get_menu_name() {
			return __( 'Gutscheine', 'psts' );
		}

		public static function get_description() {
			return __( 'Hier kannst Du Gutscheincodes für Dein Netzwerk erstellen, löschen oder aktualisieren.', 'psts' );
		}

		public static function get_page_slug() {
			return 'psts-coupons';
		}

		public static function render_page() {
			if ( ! is_super_admin() ) {
				echo "<p>" . __( 'Netter Versuch...', 'psts' ) . "</p>"; //If accessed properly, this message doesn't appear.
				return false;
			}

//			ProSites_Model_Coupons::process_form();

			self::process_coupon_forms();
			self::admin_coupons();
			self::admin_render_import();
		}

		/**
		 * Still using legacy coupons code below
		 */
		public static function admin_coupons() {
			global $psts, $wp;

			if ( ! is_super_admin() ) {
				echo "<p>" . __( 'Netter Versuch...', 'psts' ) . "</p>"; //If accessed properly, this message doesn't appear.
				return;
			}

			?>

			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					jQuery.datepicker.setDefaults(jQuery.datepicker.regional['<?php echo $psts->language; ?>']);
					jQuery('.pickdate').datepicker({
						dateFormat: 'yy-mm-dd',
						changeMonth: true,
						changeYear: true,
						minDate: 0,
						firstDay: <?php echo ( get_option( 'start_of_week' ) == '0' ) ? 7 : get_option( 'start_of_week' ); ?>
					});
				});
			</script>
			<div class="wrap">
				<div class="icon32"><img src="<?php echo $psts->plugin_url . 'images/coupon.png'; ?>"/></div>
				<h1><?php _e( 'Bloghosting Gutscheine', 'psts' ); ?></h1>

				<p><?php _e( 'Hier kannst Du Gutscheincodes für Dein Netzwerk erstellen, löschen oder aktualisieren.', 'psts' ) ?></p>
				<?php

				$coupons = get_site_option( 'psts_coupons' );
				$error   = false;

				//delete checked coupons
				if ( isset( $_POST['allcoupon_delete'] ) ) {
					//check nonce
					check_admin_referer( 'psts_coupons' );

					if ( is_array( $_POST['coupons_checks'] ) ) {
						//loop through and delete
						foreach ( $_POST['coupons_checks'] as $del_code ) {
							unset( $coupons[ $del_code ] );
						}

						update_site_option( 'psts_coupons', $coupons );
						//display message confirmation
						echo '<div class="updated fade"><p>' . __( 'Gutschein(e) erfolgreich gelöscht.', 'psts' ) . '</p></div>';
					}
				}

				//save or add coupon
				if ( isset( $_POST['submit_settings'] ) ) {
					//check nonce
					check_admin_referer( 'psts_coupons' );

					$error = false;

					$new_coupon_code = preg_replace( '/[^A-Z0-9_-]/', '', strtoupper( $_POST['coupon_code'] ) );
					if ( ! $new_coupon_code ) {
						$error[] = __( 'Bitte gib einen gültigen Gutscheincode ein', 'psts' );
					}

					$coupons[ $new_coupon_code ]['lifetime'] = $_POST['lifetime'];
					if ( $coupons[ $new_coupon_code ]['lifetime'] != 'first' && $coupons[ $new_coupon_code ]['lifetime'] != 'indefinite' ) {
						$error[] = __( 'Bitte wähle eine gültige Gutschein-Lebensdauer', 'psts' );
					}

					$coupons[ $new_coupon_code ]['discount'] = round( $_POST['discount'], 2 );
					if ( $coupons[ $new_coupon_code ]['discount'] <= 0 ) {
						$error[] = __( 'Bitte gib einen gültigen Rabattbetrag ein', 'psts' );
					}
					if ( 'pct' == $_POST['discount_type'] && $coupons[ $new_coupon_code ]['discount'] >= 100 ) {
						$error[] = __( 'Gutscheine mit 100% Rabatt sind nicht zulässig. Verwende stattdessen die Funktion "Kostenlose Testversion" oder "Manuelle Erweiterung"', 'psts' );
					}

					$coupons[ $new_coupon_code ]['discount_type'] = $_POST['discount_type'];
					if ( $coupons[ $new_coupon_code ]['discount_type'] != 'amt' && $coupons[ $new_coupon_code ]['discount_type'] != 'pct' ) {
						$error[] = __( 'Bitte wähle einen gültigen Rabatttyp', 'psts' );
					}
					//Coupon Valid for Period
					$coupons[ $new_coupon_code ]['valid_for_period'] = isset( $_POST['valid_for_period'] ) ? $_POST['valid_for_period'] : array();

					$coupons[ $new_coupon_code ]['start']            = strtotime( $_POST['start'] );
					if ( $coupons[ $new_coupon_code ]['start'] === false ) {
						$error[] = __( 'Bitte gib ein gültiges Startdatum ein', 'psts' );
					}

					$coupons[ $new_coupon_code ]['end'] = strtotime( $_POST['end'] );
					if ( $coupons[ $new_coupon_code ]['end'] && $coupons[ $new_coupon_code ]['end'] < $coupons[ $new_coupon_code ]['start'] ) {
						$error[] = __( 'Bitte gib ein gültiges Enddatum ein, das nicht vor dem Startdatum liegt', 'psts' );
					}

					$coupons[ $new_coupon_code ]['level'] = intval( $_POST['level'] );

					$coupons[ $new_coupon_code ]['uses'] = ( is_numeric( $_POST['uses'] ) ) ? (int) $_POST['uses'] : '';

					if ( ! $error ) {
						update_site_option( 'psts_coupons', $coupons );
						$new_coupon_code = '';
						echo '<div class="updated fade"><p>' . __( 'Gutschein erfolgreich gespeichert.', 'psts' ) . '</p></div>';
					} else {
						echo '<div class="error"><p>' . implode( '<br />', $error ) . '</p></div>';
					}
				}

				//if editing a coupon
				$new_coupon_code = isset ( $_GET['code'] ) ? sanitize_text_field ( $_GET['code'] ) : '';

				$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
				$num   = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 20;

				$coupon_list = get_site_option( 'psts_coupons' );
				$levels      = (array) get_site_option( 'psts_levels' );
				$total       = ( is_array( $coupon_list ) ) ? count( $coupon_list ) : 0;

				if ( ! empty( $total ) ) {
					$coupon_list = array_slice( $coupon_list, intval( ( $apage - 1 ) * $num ), intval( $num ), true );
				}

				$request = remove_query_arg( 'apage' );
				$nav_args = array(
					'base' => @add_query_arg('apage','%#%'),
					'total'   => ceil( $total / $num ),
					'current' => $apage,
					'add_args' => array( 'page' => 'psts-coupons'),
				);

				$coupon_navigation = paginate_links( $nav_args );
				$page_link         = ( $apage > 1 ) ? '&amp;apage=' . $apage : '';
				?>

				<form id="form-coupon-list" action="<?php echo network_admin_url( 'admin.php?page=psts-coupons' ); ?>" method="post">
					<?php wp_nonce_field( 'psts_coupons' ) ?>
					<div class="tablenav">
						<?php if ( $coupon_navigation ) {
					echo "<div class='tablenav-pages'>$coupon_navigation</div>";
				} ?>

						<div class="alignleft">
							<input type="submit" value="<?php _e( 'Löschen', 'psts' ) ?>" name="allcoupon_delete" class="button-secondary delete"/>
							<br class="clear"/>
						</div>
					</div>

					<br class="clear"/>

					<?php
				// define the columns to display, the syntax is 'internal name' => 'display name'
				$posts_columns = array(
					'code'      => __( 'Gutscheincode', 'psts' ),
					'lifetime'  => __( 'Laufzeit', 'psts' ),
					'discount'  => __( 'Rabatt', 'psts' ),
					'start'     => __( 'Startdatum', 'psts' ),
					'end'       => __( 'Ablaufdatum', 'psts' ),
					'level'     => __( 'Level', 'psts' ),
					'period'    => __( 'Zeitraum', 'psts' ),
					'used'      => __( 'Benutzt', 'psts' ),
					'remaining' => __( 'Verbleibende Verwendungen', 'psts' ),
					'edit'      => __( 'Bearbeiten', 'psts' )
				);
					?>

					<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
						<thead>
						<tr>
							<th scope="col" class="check-column"><input type="checkbox"/></th>
							<?php foreach ( $posts_columns as $column_id => $column_display_name ) {
								$col_url = $column_display_name;
								?>
								<th scope="col"><?php echo $col_url ?></th>
							<?php } ?>
						</tr>
						</thead>
						<tbody id="the-list">
						<?php
						$bgcolor = isset( $class ) ? $class : '';
						if ( is_array( $coupon_list ) && count( $coupon_list ) ) {
							foreach ( $coupon_list as $coupon_code => $coupon ) {
								$class = ( isset( $class ) && 'alternate' == $class ) ? '' : 'alternate';

								//assign classes based on coupon availability
								//$class = ($psts->check_coupon($coupon_code)) ? $class . ' coupon-active' : $class . ' coupon-inactive';

								echo '<tr class="' . $class . ' blog-row"><th scope="row" class="check-column"><input type="checkbox" name="coupons_checks[]"" value="' . $coupon_code . '" /></th>';

								foreach ( $posts_columns as $column_name => $column_display_name ) {
									switch ( $column_name ) {
										case 'code':
											?>
											<th scope="row">
												<?php echo $coupon_code; ?>
											</th>
											<?php
											break;
										case 'lifetime':
											$lifetime_label = array(
												'first'      => __( 'Erste Zahlung', 'psts' ),
												'indefinite' => __( 'Unbestimmt', 'psts' ),
											);
											?>
											<th scope="row">
												<?php echo ! empty( $coupon['lifetime'] ) ? $lifetime_label[ $coupon['lifetime'] ] : ''; ?>
											</th>
											<?php
											break;
										case 'discount':
											?>
											<th scope="row">
												<?php
												if ( $coupon['discount_type'] == 'pct' ) {
													echo $coupon['discount'] . '%';
												} else if ( $coupon['discount_type'] == 'amt' ) {
													echo $psts->format_currency( '', $coupon['discount'] );
												}
												?>
											</th>
											<?php
											break;

										case 'start':
											?>
											<th scope="row">
												<?php echo date_i18n( get_option( 'date_format' ), $coupon['start'] ); ?>
											</th>
											<?php
											break;

										case 'end':
											?>
											<th scope="row">
												<?php echo ( $coupon['end'] ) ? date_i18n( get_option( 'date_format' ), $coupon['end'] ) : __( 'Kein Ablaufdatum', 'psts' ); ?>
											</th>
											<?php
											break;

										case 'level':
											?>
											<th scope="row">
												<?php echo isset( $levels[ $coupon['level'] ] ) ? $coupon['level'] . ': ' . $levels[ $coupon['level'] ]['name'] : __( 'Jeder Ebene', 'psts' ); ?>
											</th>
											<?php
											break;

										case 'period':
											?>
											<th scope="row">
												<?php
												//echo isset( $levels[ $coupon['period'] ] ) ? $coupon['period'] . ': ' . $levels[ $coupon['period'] ]['name'] : __( 'Any Level', 'psts' );
												$zero = true;
												if ( isset( $coupon['valid_for_period'] ) ) {

													foreach ( $coupon['valid_for_period'] as $i => $period ) {
														if ( ! empty( $period ) ) {
															$zero = false;
															echo $period . __( 'm' );
															if ( $i !== count( $coupon['valid_for_period'] ) - 1 ) {
																echo ',';
															}
														}
													}
												}
												if ( $zero ) {
													echo '-';
												}
												?>
											</th>
											<?php
											break;

										case 'used':
											?>
											<th scope="row">
												<?php echo isset( $coupon['used'] ) ? number_format_i18n( $coupon['used'] ) : 0; ?>
											</th>
											<?php
											break;

										case 'remaining':
											?>
											<th scope="row">
												<?php
												if ( isset( $coupon['uses'] ) && ! empty( $coupon['uses'] ) ) {
													echo number_format_i18n( intval( $coupon['uses'] ) - intval( @$coupon['used'] ) );
												} else {
													_e( 'Unbegrenzt', 'psts' );
												}
												?>
											</th>
											<?php
											break;

										case 'edit':
											?>
											<th scope="row">
												<a href="admin.php?page=psts-coupons<?php echo $page_link; ?>&amp;code=<?php echo $coupon_code; ?>#add_coupon"><?php _e( 'Bearbeiten', 'psts' ) ?>&raquo;</a>
											</th>
											<?php
											break;

									}
								}
								?>
								</tr>
								<?php
							}
						} else {
							?>
							<tr style='background-color: <?php echo $bgcolor; ?>'>
								<td colspan="9"><?php _e( 'Noch keine Gutscheine.', 'psts' ) ?></td>
							</tr>
							<?php
						} // end if coupons
						?>
						</tbody>
						<tfoot>
						<tr>
							<th scope="col" class="check-column"><input type="checkbox"/></th>
							<?php foreach ( $posts_columns as $column_id => $column_display_name ) {
								$col_url = $column_display_name;
								?>
								<th scope="col"><?php echo $col_url ?></th>
							<?php } ?>
						</tr>
						</tfoot>
					</table>

					<div class="tablenav">
						<?php if ( $coupon_navigation ) {
							echo "<div class='tablenav-pages'>$coupon_navigation</div>";
						} ?>
					</div>

					<div id="poststuff" class="metabox-holder">

						<div class="postbox">
							<h3 class="hndle" style="cursor:auto;"><span>
							<?php
							if ( isset( $_GET['code'] ) || $error ) {
								_e( 'Gutschein bearbeiten', 'psts' );
							} else {
								_e( 'Gutschein hinzufügen', 'psts' );
							}
							$periods = $psts->get_setting( 'enabled_periods', 0 );
							?></span></h3>

							<div class="inside">
								<?php
								$coupon_life      = 'first';
								$discount         = '';
								$discount_type    = '';
								$start            = date( 'Y-m-d' );
								$end              = '';
								$uses             = '';
								$valid_for_period = array();
								//setup defaults
								if ( isset( $new_coupon_code ) && isset( $coupons[ $new_coupon_code ] ) ) {
									$coupon_life      = $coupons[ $new_coupon_code ]['lifetime'];
									$discount         = ( $coupons[ $new_coupon_code ]['discount'] && $coupons[ $new_coupon_code ]['discount_type'] == 'amt' ) ? round( $coupons[ $new_coupon_code ]['discount'], 2 ) : $coupons[ $new_coupon_code ]['discount'];
									$discount_type    = $coupons[ $new_coupon_code ]['discount_type'];
									$start            = ( $coupons[ $new_coupon_code ]['start'] ) ? date( 'Y-m-d', $coupons[ $new_coupon_code ]['start'] ) : date( 'Y-m-d' );
									$end              = ( $coupons[ $new_coupon_code ]['end'] ) ? date( 'Y-m-d', $coupons[ $new_coupon_code ]['end'] ) : '';
									$uses             = $coupons[ $new_coupon_code ]['uses'];
									$valid_for_period = isset( $coupons[ $new_coupon_code ]['valid_for_period'] ) ? $coupons[ $new_coupon_code ]['valid_for_period'] : array();
								}
								?>
								<table id="add_coupon">
									<thead>
									<tr>
										<th class="coupon-code">
											<?php echo __( 'Gutscheincode', 'psts' ) . $psts->help_text( __( 'Nur Buchstaben und Zahlen', 'psts' ) ); ?>
										</th>
										<th class="coupon-life">
											<?php echo __( 'Laufzeit', 'psts' ) . $psts->help_text( __( 'Nur für die erste Zahlung oder die Laufzeit des Kontos.', 'psts' ) ); ?>
										</th>
										<th><?php echo __( 'Rabatt', 'psts' ) . $psts->help_text( sprintf( __( 'Wenn der Rabatt zu einer kostenlosen Kaufabwicklung führt, wird der Betrag auf %s angepasst, um Gateway-Fehler zu vermeiden. Verwende stattdessen die Funktion "Kostenlose Testversion" oder "Manuelle Erweiterung".', 'psts' ), $psts->format_currency( '', 0.01 ) ) ); ?></th>
										<th><?php _e( 'Anfangsdatum', 'psts' ) ?></th>
										<th class="expire-date">
											<?php echo __( 'Ablaufdatum', 'psts' ) . $psts->help_text( __( 'Kein Ende, wenn leer gelassen', 'psts' ) ); ?>
										</th>
										<th>
											<?php _e( 'Level', 'psts' ) ?>
										</th>
										<th class="coupon-period">
											<?php echo __( 'Zeitraum', 'psts' ) . $psts->help_text( __( 'Ermöglicht es die Verfügbarkeit von Gutscheinen auf den ausgewählten Abonnementzeitraum zu begrenzen.', 'psts' ) ); ?>
										</th>
										<th class="allowed-users">
											<?php echo __( 'Zulässige Verwendungen', 'psts' ) . $psts->help_text( __( 'Unbegrenzt, wenn leer', 'psts' ) ); ?>
										</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td>
											<input value="<?php echo $new_coupon_code ?>" name="coupon_code" type="text"
											       style="text-transform: uppercase;"/>
										</td>
										<td>
											<select name="lifetime" class="chosen">
												<option
													value="first"<?php selected( $coupon_life, 'first' ) ?>><?php esc_html_e( 'Erste Zahlung' ); ?></option>
												<option
													value="indefinite"<?php selected( $coupon_life, 'indefinite' ) ?>><?php esc_html_e( 'Indefinite' ); ?></option>
											</select>
										</td>
										<td>
											<input value="<?php echo $discount; ?>" size="3" name="discount"
											       type="text"/>
											<select name="discount_type" class="chosen narrow">
												<option
													value="amt"<?php selected( $discount_type, 'amt' ) ?>><?php echo $psts->format_currency(); ?></option>
												<option value="pct"<?php selected( $discount_type, 'pct' ) ?>>%</option>
											</select>
										</td>
										<td>
											<input value="<?php echo $start; ?>" class="pickdate" size="11" name="start"
											       type="text"/>
										</td>
										<td>
											<input value="<?php echo $end; ?>" class="pickdate" size="11" name="end"
											       type="text"/>
										</td>
										<td>
											<select name="level" class="chosen">
												<option value="0"><?php _e( 'Jeder Ebene', 'psts' ) ?></option>
												<?php
												foreach ( $levels as $key => $value ) {
													?>
													<option value="<?php echo $key; ?>"<?php selected( @$coupons[ $new_coupon_code ]['level'], $key ) ?>><?php echo $key . ': ' . $value['name']; ?></option><?php
												}
												?>
											</select>
										</td>
										<?php
										if ( ! empty( $periods ) ) {
											?>
											<td>
												<select name="valid_for_period[]" multiple class="psts-period chosen"
												        data-placeholder="Zeitraum auswählen">
													<option
														value="0" <?php echo in_array( 0, $valid_for_period ) ? 'selected' : ''; ?>><?php _e( 'Jeder Zeitraum', 'psts' ) ?></option>
													<?php
													foreach ( $periods as $period ) {
														$text = $period == 1 ? __( 'Monat', 'psts' ) : __( 'Monate', 'psts' );
														?>
														<option value="<?php echo $period; ?>"<?php echo in_array( $period, $valid_for_period ) ? 'selected' : ''; ?>><?php echo $period . ' ' . $text; ?></option><?php
													}
													?>
												</select>
											</td>
											<td>
											<input value="<?php echo $uses; ?>" size="4" name="uses" type="text"/>
											</td><?php
										} ?>
									</tr>
									</tbody>
								</table>

								<p class="submit">
									<input type="submit" name="submit_settings" class="button-primary"
									       value="<?php _e( 'Gutschein speichern', 'psts' ) ?>"/>
								</p>
							</div>
						</div>

					</div>
				</form>

			</div>
			<?php
		}

		private static function admin_render_import() {

			$csv_fields = array(
				'coupon_code' => __( 'Nur Buchstaben und Zahlen. Keine Freizeichen.', 'psts' ),
				'lifetime' => __( 'Wie lange ist der Rabatt eines Gutscheins aktiv? "first" - nur für die erste Zahlung. "indefinite" - für das Leben der Website.', 'psts' ),
				'discount' => __( 'Numerischer Wert des Rabatts, der ohne Symbole angewendet werden soll.', 'psts' ),
				'type' => __( 'Gib \'amt\' als Betrag und \'pct\' als Prozentsatz an.', 'psts' ),
				'start_date' => __( 'Startdatum des Gutscheins im Format YYYY-MM-DD oder leer.', 'psts' ),
				'expiry_date' => __( 'Ablaufdatum des Gutscheins im Format YYYY-MM-DD oder leer.', 'psts' ),
				'level' => __( 'Numerische Nummer der Ebene, für die der Gutschein gilt (gemäß der Einstellung \'Levels\'). 0 für alle Level.', 'psts' ),
				'uses' => __( 'Häufigkeit, mit der dieser Gutschein verwendet werden kann. Gib 0 für keine Einschränkungen an.', 'psts' ),
				'period' => __( 'Zahlungszeitraum, für den der Gutschein gilt. 0 für alle Zeiträume. 1 für 1 Monat, 3 für 3 Monate, 12 für 12 Monate. Verwende das | Symbol für mehrere Optionen. z.B. 3|12', 'psts' ),
			);

			?>
			<div id="poststuff" class="metabox-holder">
			<div class="postbox">
			<h3 class="hndle" style="cursor:auto;"><span><?php esc_html_e( 'Gutscheine importieren', 'psts' ); ?></span></h3>
				<div class="inside">
					<p class="description">
						<?php esc_html_e( 'Wähle eine CSV-Datei mit Deinen Gutscheinen mit den folgenden Überschriften in der angegebenen Reihenfolge aus:', 'psts' ); ?>
						<ul>
						<?php
							foreach( $csv_fields as $field => $description ) {
								?>
									<li><?php echo '<strong>' . esc_html( $field ) . '</strong> - ' . esc_html( $description ); ?></li>
								<?php
							}
						?>
						</ul>
					</p>
					<form id="form-coupon-import" action="<?php echo network_admin_url( 'admin.php?page=psts-coupons' ); ?>" method="post" enctype="multipart/form-data">
						<?php wp_nonce_field( 'psts_coupons_import' ) ?>
						<p>
<!--						<label for="uploadfiles[]">-->
							<?php esc_html_e( 'Wähle die CSV-Datei aus, die Du importieren möchtest: ', 'psts' ); ?><br /><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" />
<!--						</label>-->
						</p>
						<input class="button" type="submit" name="coupon-import-csv" value="<?php esc_attr_e( 'Gutscheine importieren', 'psts' );?>" />
					</form>
				</div>
			</div>
			</div>
			<?php
		}

		private static function process_coupon_forms() {

			if ( isset( $_POST['coupon-import-csv'] ) ) {
				check_admin_referer( 'psts_coupons_import' );

				$uploadfiles = $_FILES['uploadfiles'];

				if ( is_array( $uploadfiles ) ) {
					foreach ( $uploadfiles['name'] as $key => $value ) {
						if ( $uploadfiles['error'][$key] == 0 && 'text/csv' == $uploadfiles['type'][$key] ) {

							$filetmp = $uploadfiles['tmp_name'][$key];

							$filename = $uploadfiles['name'][$key];

							//extract the extension, but keep just in case there are multiple dots, resconstruct
							$filename = explode( '.', $filename );
							$extension = array_pop( $filename );
							$filename = implode( '.', $filename );

							$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
							$filename = $filetitle . '.' . $extension;

							$upload_dir = wp_upload_dir();

							$i = 0;
							while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
								$filename = $filetitle . '_' . $i . '.' . $extension;
								$i++;
							}

							$destination = $upload_dir['path'] . '/' . $filename;


							if ( !is_writeable( $upload_dir['path'] ) ) {
								// Not writable
								return;
							}

							if ( !@move_uploaded_file( $filetmp, $destination) ){
								// Saving failed
								continue;
							}

							$added = ProSites_Helper_Coupons::process_coupon_import( $destination );
							//display message confirmation
							echo '<div class="updated fade"><p>' . sprintf( __( '%d Gutschein(e) importiert.', 'psts' ), $added ) . '</p></div>';

						} else {
							// ERROR MSG
						}
					}
				}
			}


		}

	}

}
