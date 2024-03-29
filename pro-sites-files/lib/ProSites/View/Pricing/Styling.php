<?php

if ( ! class_exists( 'ProSites_View_Pricing_Styling' ) ) {
	class ProSites_View_Pricing_Styling {


		// Render Tab content
		public static function render_tab_pricing_style() {
			global $psts;

			$active_tab = ProSites_Helper_Tabs_Pricing::get_active_tab();
			ProSites_Helper_Settings::settings_header( $active_tab );

			?>
			<div class="pricing-styles">
				<input type="hidden" name="pricing_settings" value="<?php echo esc_attr( $active_tab['tab_key'] ); ?>"/>
				<h3><?php esc_html_e( 'Tabellenlayout', 'psts' ); ?></h3>
				<div class="table-layout">
					<label>
						<input type="radio" name="psts[pricing_table_layout]" value="option1" <?php checked( $psts->get_setting( 'pricing_table_layout', 'option1' ), 'option1' ); ?> />
						<div class="layout-img option1"></div>
					</label>
					<label>
						<input type="radio" name="psts[pricing_table_layout]" value="option2" <?php checked( $psts->get_setting( 'pricing_table_layout', 'option1' ), 'option2' ); ?> />
						<div class="layout-img option2"></div>
					</label>
					<label>
						<input type="radio" name="psts[pricing_table_layout]" value="option3" <?php checked( $psts->get_setting( 'pricing_table_layout', 'option1' ), 'option3' ); ?> />
						<div class="layout-img option3"></div>
					</label>
					<label>
						<input type="radio" name="psts[pricing_table_layout]" value="option4" <?php checked( $psts->get_setting( 'pricing_table_layout', 'option1' ), 'option4' ); ?> />
						<div class="layout-img option4"></div>
					</label>
					<label>
						<input type="radio" name="psts[pricing_table_layout]" value="option5" <?php checked( $psts->get_setting( 'pricing_table_layout', 'option1' ), 'option5' ); ?> />
						<div class="layout-img option5"></div>
					</label>
				</div>
				<p class="description clear"><?php esc_html_e( 'Wähle das Basislayout für die Preistabellen. Dies wird die anfängliche CSS für Deine Preistabellen.', 'psts' ); ?>
					<br/>
					<?php esc_html_e( 'Für eine detailliertere Anpassung musst Du das CSS in Deinem Design ändern. Verwende ein benutzerdefiniertes CSS-Plugin oder das benutzerdefinierte CSS-Feld auf dieser Seite.', 'psts' ); ?></p>

				<?php
				$normal_heading   = __( 'Normaler Level', 'psts' );
				$selected_heading = __( 'Gewählter Level', 'psts' );
				$featured_heading = __( 'Empfohlener Level', 'psts' );
				$styles           = $psts->get_setting( 'checkout_style' );
				?>
				<h3><?php esc_html_e( 'Spalten anpassen', 'psts' ); ?></h3>
				<p class="description clear"><?php esc_html_e( 'Verwende die folgenden Einstellungen, um Deine Checkout-Tabelle anzupassen. Jede Spalte hier repräsentiert den Status Deiner Levels in der Checkout-Tabelle: normales, ausgewähltes Level oder empfohlenes Level. Hinweis: Dadurch werden die Standardeinstellungen für jedes der oben genannten Layouts überschrieben.', 'psts' ); ?>
				<table class="form-table checkout_style">
					<tr>
						<th></th>
						<th scope="row"><?php echo esc_html( $normal_heading ); ?></th>
						<th scope="row"><?php echo esc_html( $selected_heading ); ?></th>
						<th scope="row"><?php echo esc_html( $featured_heading ); ?></th>
					</tr>
					<tr>
						<td><?php _e( 'Spalten Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_column_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_column_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_column_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_column_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_column_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_column_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Spalten Rahmenfarbe', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_border_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_border_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_border_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_border_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_border_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_border_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Spalten rahmenbreite', 'psts' ) ?></td>
						<td>
							<select name="psts[checkout_style][pricing_style_border_width]" class="chosen">
								<?php
								$border_width         = self::get_style( 'pricing_style_border_width', $styles );
								$border_width_options = '';

								for ( $counter = 0; $counter <= 50; $counter ++ ) {
									$border_width_options .= '<option value="' . $counter . '"' . ( $counter == $border_width ? ' selected' : '' ) . '>' . ( ( $counter ) ? $counter : 'default' ) . '</option>' . "\n";
								}
								echo $border_width_options;
								?>
							</select>
						</td>
						<td>
							<select name="psts[checkout_style][pricing_style_border_width_selected]" class="chosen">
								<?php
								$border_width         = self::get_style( 'pricing_style_border_width_selected', $styles );
								$border_width_options = '';

								for ( $counter = 0; $counter <= 50; $counter ++ ) {
									$border_width_options .= '<option value="' . $counter . '"' . ( $counter == $border_width ? ' selected' : '' ) . '>' . ( ( $counter ) ? $counter : 'default' ) . '</option>' . "\n";
								}
								echo $border_width_options;
								?>
							</select>
						</td>
						<td>
							<select name="psts[checkout_style][pricing_style_border_width_featured]" class="chosen">
								<?php
								$border_width         = self::get_style( 'pricing_style_border_width_featured', $styles );
								$border_width_options = '';

								for ( $counter = 0; $counter <= 50; $counter ++ ) {
									$border_width_options .= '<option value="' . $counter . '"' . ( $counter == $border_width ? ' selected' : '' ) . '>' . ( ( $counter ) ? $counter : 'default' ) . '</option>' . "\n";
								}
								echo $border_width_options;
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Titel Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Titel Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_title_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_title_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Preis Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Preis Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Preisübersicht Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Preisübersicht Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_price_summary_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_price_summary_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Untertiteltext', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_subtitle_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_color', '', $styles ); ?>"/></td>
						<td><!-- <input type="text" name="psts[checkout_style][pricing_style_subtitle_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_color_selected', '', $styles ); ?>"/> --></td>
						<td><!-- <input type="text" name="psts[checkout_style][pricing_style_subtitle_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_color_featured', '', $styles ); ?>"/> --></td>
					</tr>
					<tr>
						<td><?php _e( 'Untertitel Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_subtitle_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_subtitle_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_subtitle_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_subtitle_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Features Zeilentext', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Features Zeilenhintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_text_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_text_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Features Zeilentext (alternativ/ungerade)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_text_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_text_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_text_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_text_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_text_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Features Zeilenhintergrund (alternativ/ungerade)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_features_alt_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_features_alt_bg_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_container]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_container', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_container_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_container_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_container_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_container_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_text_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_text_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_text_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_text_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_text_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_bg_featured', '', $styles ); ?>"/></td>

					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Text (Hover)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_text_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_text_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_text_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_text_color_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_text_color_featured', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Hintergrund (Hover)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_button_hover_bg_featured]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_button_hover_bg_featured', '', $styles ); ?>"/></td>
					</tr>
				</table>

				<?php
					$normal_heading   = __( 'Normal', 'psts' );
					$selected_heading = __( 'Gewählt', 'psts' );
					$hover_heading = __( 'Hover', 'psts' );
				?>
				<h3><?php esc_html_e( 'Periodenauswahl', 'psts' ); ?></h3>
				<p class="description clear"><?php esc_html_e( 'Ändere den Stil der Periodenauswahl, wenn sie über der Tabelle angezeigt wird.', 'psts' ); ?>
				<table class="form-table checkout_style">
					<tr>
						<th></th>
						<th scope="row"><?php echo esc_html( $normal_heading ); ?></th>
						<th scope="row"><?php echo esc_html( $selected_heading ); ?></th>
						<th scope="row"><?php echo esc_html( $hover_heading ); ?></th>
					</tr>
					<tr>
						<td><?php _e( 'Rahmenfarbe', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_period_border_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_period_border_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_period_border_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_period_border_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_period_border_color_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_period_border_color_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Monatlich Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_color_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_color_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Monatlich Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_monthly_bg_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_monthly_bg_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Vierteljährlich Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_color_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_color_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Vierteljährlich Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_quarterly_bg_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_quarterly_bg_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Jährlich Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_color', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_color_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_color_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_color_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_color_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Jährlich Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_bg', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_bg_selected]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_bg_selected', '', $styles ); ?>"/></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_annually_bg_hover]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_annually_bg_hover', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Ausrichtung', 'psts' ) ?></td>
						<td>
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_period_align]" value="left" <?php checked( self::get_style( 'pricing_style_period_align', 'none', $styles ), 'left' ); ?> />
								<?php esc_html_e( 'Links ', 'psts' ) ?>
							</label>&nbsp;
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_period_align]" value="none" <?php checked( self::get_style( 'pricing_style_period_align', 'none', $styles ), 'none' ); ?> />
								<?php esc_html_e( 'Zentriert ', 'psts' ) ?>
							</label>&nbsp;
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_period_align]" value="right" <?php checked( self::get_style( 'pricing_style_period_align', 'none', $styles ), 'right' ); ?> />
								<?php esc_html_e( 'Rechts ', 'psts' ) ?>
							</label>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Freistehende Couponbox', 'psts' ); ?></h3>
				<p class="description clear"><?php esc_html_e( 'Ändere die Anzeige der Gutscheinbox (falls diese nicht an die Tabelle angehängt ist)..', 'psts' ); ?>
				<table class="form-table checkout_style">
					<tr>
						<td><?php _e( 'Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_column_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_column_bg', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Rahmenfarbe', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_border_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_border_color', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Rahmenbreite', 'psts' ) ?></td>
						<td>
							<select name="psts[checkout_style][pricing_style_coupon_border_width]" class="chosen">
								<?php
								$border_width         = self::get_style( 'pricing_style_coupon_border_width', $styles );
								$border_width_options = '';

								for ( $counter = 0; $counter <= 50; $counter ++ ) {
									$border_width_options .= '<option value="' . $counter . '"' . ( $counter == $border_width ? ' selected' : '' ) . '>' . ( ( $counter ) ? $counter : 'default' ) . '</option>' . "\n";
								}
								echo $border_width_options;
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Text', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_button_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_button_text_color', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Hintergrund', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_button_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_button_bg', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Text (Hover)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_button_hover_text_color]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_button_hover_text_color', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Schaltfläche Hintergrund (Hover)', 'psts' ) ?></td>
						<td><input type="text" name="psts[checkout_style][pricing_style_coupon_button_hover_bg]" class="color-picker" value="<?php echo self::get_style( 'pricing_style_coupon_button_hover_bg', '', $styles ); ?>"/></td>
					</tr>
					<tr>
						<td><?php _e( 'Ausrichtung', 'psts' ) ?></td>
						<td>
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_coupon_align]" value="left" <?php checked( self::get_style( 'pricing_style_coupon_align', 'none', $styles ), 'left' ); ?> />
								<?php esc_html_e( 'Links ', 'psts' ) ?>
							</label>&nbsp;
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_coupon_align]" value="none" <?php checked( self::get_style( 'pricing_style_coupon_align', 'none', $styles ), 'none' ); ?> />
								<?php esc_html_e( 'Zentriert ', 'psts' ) ?>
							</label>&nbsp;
							<label>
								<input type="radio" name="psts[checkout_style][pricing_style_coupon_align]" value="right" <?php checked( self::get_style( 'pricing_style_coupon_align', 'none', $styles ), 'right' ); ?> />
								<?php esc_html_e( 'Rechts ', 'psts' ) ?>
							</label>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Benutzerdefiniertes CSS', 'psts' ); ?></h3>
				<p class="description clear"><?php esc_html_e( 'In diesem Feld kannst Du Dein CSS eingeben, Dein Theme anpassen oder ein benutzerdefiniertes CSS-Plugin verwenden. Du kannst vermeiden, das CSS der restlichen Seite zu ändern, indem Du allen von Dir angegebenen Regeln das Präfix "#prosites-checkout-table" voranstellst. Hinweis: Das in diesem Feld angegebene CSS wird vor dem Speichern gefiltert, um Deine Seite zu schützen. Nicht-CSS-Text wird entfernt.', 'psts' ); ?></p>
				<textarea class="custom-css" name="psts[checkout_style][pricing_table_custom_css]"><?php echo stripslashes( self::get_style( 'pricing_table_custom_css', '', $styles ) ); ?></textarea>

			</div>
		<?php

		}

		public static function get_styles_from_options() {
			global $psts;

			$options = $checkout_style = $psts->get_setting( 'checkout_style', array() );

			$style = '';

			$style .= self::get_column_background( $options );
			$style .= self::get_column_borders( $options );
			$style .= self::get_column_title( $options );
			$style .= self::get_column_price( $options );
			$style .= self::get_column_price_summary( $options );
			$style .= self::get_column_subtitle( $options );
			$style .= self::get_column_features( $options );
			$style .= self::get_column_features_alt( $options );
			$style .= self::get_column_button_container( $options );
			$style .= self::get_column_button( $options );
			$style .= self::get_column_button_hover( $options );
			$style .= self::get_period_styles( $options );
			$style .= self::get_coupon_styles( $options );



			$value = stripslashes( self::get_style( 'pricing_table_custom_css', '', $options ) );
			$style .= ! empty( $value ) ? $value : '';

			return $style;
		}

		private static function add_class_style( $css, $value, $option = '' ) {

			$replace = '';

			$type = 'column';
			switch( $option ) {
				case 'selected':
					$replace = '.pricing-column.chosen-plan';
					break;
				case 'featured':
					$replace = '.pricing-column.featured';
					break;
				case 'period_selected':
					$type = 'period';
					$replace = '.period-selector-container label > input:checked + .period-option';
					break;
				case 'period_hover':
					$type = 'period';
					$replace = '.period-selector-container label > input + .period-option:hover';
					break;
				case 'period1':
					$type = 'period1';
					$replace = '.period-selector-container label > input + .period-option.period1';
					break;
				case 'period1_hover':
					$type = 'period1';
					$replace = '.period-selector-container label > input + .period-option.period1:hover';
					break;
				case 'period1_selected':
					$type = 'period1';
					$replace = '.period-selector-container label > input:checked + .period-option.period1';
					break;
				case 'period3':
					$type = 'period3';
					$replace = '.period-selector-container label > input + .period-option.period3';
					break;
				case 'period3_hover':
					$type = 'period3';
					$replace = '.period-selector-container label > input + .period-option.period3:hover';
					break;
				case 'period3_selected':
					$type = 'period3';
					$replace = '.period-selector-container label > input:checked + .period-option.period3';
					break;
				case 'period12':
					$type = 'period12';
					$replace = '.period-selector-container label > input + .period-option.period12';
					break;
				case 'period12_hover':
					$type = 'period12';
					$replace = '.period-selector-container label > input + .period-option.period12:hover';
					break;
				case 'period12_selected':
					$type = 'period1';
					$replace = '.period-selector-container label > input:checked + .period-option.period12';
					break;
			}

			if( ! empty( $replace ) ) {
				switch( $type ) {
					case 'column';
						$css = str_replace( '.pricing-column', $replace, $css );
						break;
					case 'period':
						$css = str_replace( '.period-selector-container label > input + .period-option', $replace, $css );
						break;
					case 'period1':
						$css = str_replace( '.period-selector-container label > input + .period-option', $replace, $css );
						break;
					case 'period3':
						$css = str_replace( '.period-selector-container label > input + .period-option', $replace, $css );
						break;
					case 'period12':
						$css = str_replace( '.period-selector-container label > input + .period-option', $replace, $css );
						break;
				}
			}

			$css = str_replace( 'XXX', $value, $css );

			return $css;
		}

		public static function get_style( $key, $default = '', $options = false ) {
			global $psts;
			if ( empty( $options ) ) {
				$options = $checkout_style = $psts->get_setting( 'checkout_style', array() );
			}

			if ( isset( $options[ $key ] ) ) {
				return $options[ $key ];
			} else {
				return $default;
			}
		}

		private static function get_column_background( $options ) {
			$style = '';

			$the_style = "
					.pricing-column .title,
					.pricing-column .summary,
					.pricing-column .sub-title,
					.pricing-column .summary,
					.pricing-column .button-box,
					#prosites-checkout-table .coupon-wrapper .coupon-box,
					.pricing-column li ul.feature-section,
					.pricing-column .feature,
					.pricing-column .feature.alternate {
						background: XXX;
					}";


			$extra_style = "
					.pricing-column .button-box.no-button,
					.pricing-column:first-child .title,
					.pricing-column:first-child .summary {
						background: none;
					}
					.pricing-column:first-child .summary .period-selector {
						background: XXX;
					}
				";

			$style .= self::convert_css_from_setting( 'pricing_style_column_bg', $options, $the_style . $extra_style );
			$style .= self::convert_css_from_setting( 'pricing_style_column_bg_featured', $options, $the_style, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_column_bg_selected', $options, $the_style, 'selected' );

			return $style;
		}

		private static function get_column_borders( $options ) {
			global $psts;

			$layout = $psts->get_setting( 'pricing_table_layout', 'option1' );


			$style = '';

			$the_css = "
				.pricing-column .period-selector select,
				.pricing-column .sub-title,
				.pricing-column .title,
				.pricing-column .summary,
				.pricing-column .summary.no-periods,
				.pricing-column .summary .period-selector,
				.pricing-column .sub-title,
				.pricing-column .sub-title.no-title,
				.pricing-column .feature-section,
				.pricing-column:first-child .feature-section,
				.pricing-column .button-box,
				#prosites-checkout-table .coupon-wrapper .coupon-box,
				.period-selector-container label > input + .period-option,
				.pricing-column.featured .sub-title.no-title {
				    border-color: XXX;
				}
			";

			if( 'option1' == $layout ) {
				$the_css .= "
					.pricing-column .title:after {
						border-bottom-color: XXX;
					}
				";
			}

			$style .= self::convert_css_from_setting( 'pricing_style_border_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_border_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_border_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.pricing-column .period-selector select,
				.pricing-column .sub-title,
				.pricing-column .title,
				.pricing-column .summary,
				.pricing-column .summary.no-periods,
				.pricing-column .summary .period-selector,
				.pricing-column .sub-title,
				.pricing-column .sub-title.no-title,
				.pricing-column .feature-section,
				.pricing-column:first-child .feature-section,
				.pricing-column .button-box,
				#prosites-checkout-table .coupon-wrapper .coupon-box {
				    border-width: XXXpx;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_border_width', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_border_width_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_border_width_selected', $options, $the_css, 'selected' );

			return $style;
		}

		private static function get_column_title( $options ) {

			$style = '';

			$the_css = "
				.period-selector-container label > input + .period-option,
				.pricing-column .title {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_title_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_title_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_title_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.period-selector-container label > input + .period-option,
				.pricing-column .title {
				    background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_title_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_title_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_title_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function convert_css_from_setting( $setting, $options, $template_css, $state = '' ) {

			$style = '';

			$value = self::get_style( $setting, '', $options );
			if( ! empty( $value ) ) {
				$style .= self::add_class_style( $template_css, $value, $state );
			}

			return $style;

		}

		private static function get_column_price( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .price {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_price_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_price_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_price_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.pricing-column .price {
				    background: XXX !important;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_price_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_price_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_price_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_price_summary( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .level-summary {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.pricing-column .level-summary {
				    background: XXX !important;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_price_summary_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_subtitle( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .sub-title {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_subtitle_color', $options, $the_css );
			//$style .= self::convert_css_from_setting( 'pricing_style_subtitle_color_selected', $options, $the_css, 'featured' );
			//$style .= self::convert_css_from_setting( 'pricing_style_subtitle_color_featured', $options, $the_css, 'selected' );

			$the_css = "
				.pricing-column .sub-title {
				    background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_subtitle_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_subtitle_bg_selected', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_subtitle_bg_featured', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_features( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .feature.alternate,
				.pricing-column .feature {
					color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_features_text_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_features_text_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_features_text_color_selected', $options, $the_css, 'selected' );

			$the_css = "
			    .pricing-column .feature.alternate,
				.pricing-column .feature {
					background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_features_text_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_features_text_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_features_text_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_features_alt( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .feature.alternate {
					color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_text_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_text_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_text_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.pricing-column .feature.alternate {
					background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_features_alt_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_button_container( $options ) {

			$style = '';

			$the_css = "
				.pricing-column .button-box {
					background: XXX;
				}
				.pricing-column .button-box.no-button {
					background: none;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_button_container', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_button_container_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_button_container_selected', $options, $the_css, 'selected' );


			return $style;

		}

		private static function get_column_button( $options ) {

			$style = '';

			$the_css = "
				.period-selector-container label > input:checked + .period-option,
				#prosites-checkout-table .coupon-wrapper .coupon-box button,
				.pricing-column .button-box button {
					color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_button_text_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_button_text_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_button_text_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.period-selector-container label > input:checked + .period-option,
				#prosites-checkout-table .coupon-wrapper .coupon-box button,
				.pricing-column .button-box button {
					background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_button_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_button_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_button_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_column_button_hover( $options ) {

			$style = '';

			$the_css = "
				.period-selector-container label > input + .period-option:hover,
				#prosites-checkout-table .coupon-wrapper .coupon-box button:hover,
				.pricing-column .button-box button:hover {
					color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_text_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_text_color_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_text_color_selected', $options, $the_css, 'selected' );

			$the_css = "
				.period-selector-container label > input + .period-option:hover,
				#prosites-checkout-table .coupon-wrapper .coupon-box button:hover,
				.pricing-column .button-box button:hover {
					background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_bg', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_bg_featured', $options, $the_css, 'featured' );
			$style .= self::convert_css_from_setting( 'pricing_style_button_hover_bg_selected', $options, $the_css, 'selected' );

			return $style;

		}

		private static function get_coupon_styles( $options ) {
			global $psts;

			$style = '';

			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box {
				    background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_column_bg', $options, $the_css );

			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box {
				    border-color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_border_color', $options, $the_css );

			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box {
				    border-width: XXXpx;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_border_width', $options, $the_css );


			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box button {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_button_text_color', $options, $the_css );

			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box button {
				    background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_button_bg', $options, $the_css );


			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box button:hover {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_button_hover_text_color', $options, $the_css );

			$the_css = "
				#prosites-checkout-table .coupon-wrapper .coupon-box button:hover {
				    background: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_button_hover_bg', $options, $the_css );

			$the_css = "
				#prosites-checkout-table .coupon-wrapper {
					float: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_coupon_align', $options, $the_css );

			return $style;
		}

		private static function get_period_styles( $options ) {
			$style = '';

			$the_css = "
				.period-selector-container label > input + .period-option {
				    border-color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_period_border_color', $options, $the_css );
			$style .= self::convert_css_from_setting( 'pricing_style_period_border_color_selected', $options, $the_css, 'period_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_period_border_color_hover', $options, $the_css, 'period_hover' );

			$the_css = "
				.period-selector-container label > input + .period-option {
				    color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_monthly_color', $options, $the_css, 'period1' );
			$style .= self::convert_css_from_setting( 'pricing_style_monthly_color_selected', $options, $the_css, 'period1_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_monthly_color_hover', $options, $the_css, 'period1_hover' );

			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_color', $options, $the_css, 'period3' );
			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_color_selected', $options, $the_css, 'period3_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_color_hover', $options, $the_css, 'period3_hover' );

			$style .= self::convert_css_from_setting( 'pricing_style_annually_color', $options, $the_css, 'period12' );
			$style .= self::convert_css_from_setting( 'pricing_style_annually_color_selected', $options, $the_css, 'period12_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_annually_color_hover', $options, $the_css, 'period12_hover' );

			$the_css = "
				.period-selector-container label > input + .period-option {
				    background-color: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_monthly_bg', $options, $the_css, 'period1' );
			$style .= self::convert_css_from_setting( 'pricing_style_monthly_bg_selected', $options, $the_css, 'period1_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_monthly_bg_hover', $options, $the_css, 'period1_hover' );

			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_bg', $options, $the_css, 'period3' );
			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_bg_selected', $options, $the_css, 'period3_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_quarterly_bg_hover', $options, $the_css, 'period3_hover' );

			$style .= self::convert_css_from_setting( 'pricing_style_annually_bg', $options, $the_css, 'period12' );
			$style .= self::convert_css_from_setting( 'pricing_style_annually_bg_selected', $options, $the_css, 'period12_selected' );
			$style .= self::convert_css_from_setting( 'pricing_style_annually_bg_hover', $options, $the_css, 'period12_hover' );

			$the_css = "
				.period-selector-container {
					float: XXX;
				}
			";

			$style .= self::convert_css_from_setting( 'pricing_style_period_align', $options, $the_css );

			return $style;
		}

	}
}