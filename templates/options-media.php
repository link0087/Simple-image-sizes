<?php
// Get the sizes
		global $_wp_additional_image_sizes,$_wp_post_type_features;
?>
		<input type="hidden" class="addSize" value='<?php echo wp_create_nonce( 'add_size' ); ?>' />
		<input type="hidden" class="regen" value='<?php echo wp_create_nonce( 'regen' ); ?>' />
		<input type="hidden" class="getList" value='<?php echo wp_create_nonce( 'getList' ); ?>' />
		<div id="sis-regen">
			<div class="wrapper" style="">
				<h4> <?php _e( 'Select which thumbnails you want to rebuild:', 'sis'); ?> </h4>
				<table cellspacing="0" id="sis_sizes" class="widefat page fixed sis">
					<thead>
						<tr>
							<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input checked="checked" type="checkbox"></th>
							<th class="manage-column" scope="col"><?php _e( 'Size name', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Width', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Height', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Crop ?', 'sis'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Display the sizes in the array
						foreach ( get_intermediate_image_sizes() as $s ):
							// Don't make or numeric sizes that appear
							if( is_integer( $s ) ) {
								continue;
							}
							
							// Set width
							$width = isset( $_wp_additional_image_sizes[$s]['width'] ) ? intval( $_wp_additional_image_sizes[$s]['width'] ) : get_option( "{$s}_size_w" ) ;
							
							// Set height
							$height = isset( $_wp_additional_image_sizes[$s]['height'] ) ? intval( $_wp_additional_image_sizes[$s]['height'] ) : get_option( "{$s}_size_h" ) ;
							
							//Set crop
							$crop = isset( $_wp_additional_image_sizes[$s]['crop'] ) ? intval( $_wp_additional_image_sizes[$s]['crop'] ) : get_option( "{$s}_crop" ) ;
							
							?>
							<tr id="sis-<?php echo esc_attr( $s ) ?>">
								<th  class="check-column">
									<input type="checkbox" class="thumbnails" id="<?php echo esc_attr( $s ) ?>" name="thumbnails[]" checked="checked" value="<?php echo esc_attr( $s ); ?>" />
								</th>
								<th>
									<label for="<?php esc_attr_e( $s ); ?>"><?php echo esc_html( $s ); ?></label>
								</th>
								<th>
									<label for="<?php esc_attr_e( $s ); ?>"><?php echo esc_html( $width); ?> px</label>
								</th>
								<th>
									<label for="<?php esc_attr_e( $s ); ?>"><?php echo esc_html( $height ); ?> px</label>
								</th>
								<th>
									<label for="<?php esc_attr_e( $s ); ?>"><?php echo ( $crop == 1 )? __( 'Yes', 'sis' ):__( 'No', 'sis' ); ?>	</label>
								</th>
							</tr>
						<?php endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input checked="checked" type="checkbox"></th>
							<th class="manage-column" scope="col"><?php _e( 'Size name', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Width', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Height', 'sis'); ?></th>
							<th class="manage-column" scope="col"><?php _e( 'Crop ?', 'sis'); ?></th>
						</tr>
					</tfoot>
				</table>
				
				<h4><?php _e( 'Select which post type source thumbnails you want to rebuild:', 'sis'); ?></h4>
				<table cellspacing="0" class="widefat page fixed sis">
						<thead>
							<tr>
								<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input checked="checked" type="checkbox"></th>
								<th class="manage-column" scope="col"><?php _e( 'Post type', 'sis'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						// Diplay the post types table
						foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $ptype ):
							// Avoid the post_types without post thumbnails feature
							if( !array_key_exists( 'thumbnail' , $_wp_post_type_features[$ptype->name] ) || $_wp_post_type_features[$ptype->name] == false ) {
								continue;
							}
							?>
							<tr>
								<th class="check-column">
									<label for="<?php esc_attr_e( $ptype->name ); ?>">
										<input type="checkbox" class="post_types" name="post_types[]" checked="checked" id="<?php echo esc_attr( $ptype->name ); ?>" value="<?php echo esc_attr( $ptype->name ); ?>" />
									</label>
								</th>
								<th>
									<label for="<?php esc_attr_e( $ptype->name ); ?>"><em><?php echo esc_html( $ptype->labels->name ); ?></em></label>
								</th>
							</tr>
						<?php endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<th scope="col" id="cb" class="manage-column column-cb check-column"><input checked="checked" type="checkbox"></th>
							<th class="manage-column" scope="col"><?php _e( 'Post type', 'sis'); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div >
			<div id="regenerate_message"></div>
			<div class="progress"></div>
			<div id="sis_progress-percent" class="hidden" >0%</div>
			<div class="ui-widget" id="time">
				<div class="ui-state-highlight ui-corner-all"> 
					<p>
						<span class="ui-icon ui-icon-info"></span> 
						<span><strong><?php _e( 'End time calculated :', 'sis' ); ?></strong> <span class='time_message'><?php _e( 'Calculating...', 'sis' ) ?></span> </span>
					</p>
					<ul class="messages"></ul>
				</div>
			</div>
			<div id="error_messages">
				<p>
					<ol class="messages">
					</ol>
				</p>
			</div>
			<div id="thumb"><h4><?php _e( 'Last image:', 'sis'); ?></h4><img id="thumb-img" /></div>
			<input type="button" class="button" name="ajax_thumbnail_rebuild" id="ajax_thumbnail_rebuild" value="<?php _e( 'Regenerate Thumbnails', 'sis' ) ?>" />
		</div>