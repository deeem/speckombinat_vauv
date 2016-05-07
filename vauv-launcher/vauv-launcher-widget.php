<?php

/*
Plugin Name: VAUV Launcher Widget
Plugin URI: https://github.com/deeem/vauv-launcher
Description: Виджет панели ярлыков для запуска различных web-приложений на сайте отдела ВАУВ
Version: 1.0
Author: deeem
License: GPLv2
*/

class VAUV_Launcher_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'VAUV_Launcher_Widget', // Base ID
			'VAUV Launcher Widget',
			array(
				'description' => 'Виджет панели ярлыков для запуска различных web-приложений'
			)
		);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		?>
		<div class="container">
			<div class="row">
				<?php $current_post = 0; ?>
				<?php if ( is_user_logged_in() ) : ?>
					<?php
					$args  = array(
						'post_type'      => 'app',
						'posts_per_page' => 50,
						'meta_key'       => 'app_visible',
						'meta_value'     => 'false'
					);
					$query = new WP_Query( $args );
					while ( $query->have_posts() ) :
						$query->the_post();
						$app_description = get_post_meta( get_the_ID(), 'app_description', true );
						$app_visible     = get_post_meta( get_the_ID(), 'app_visible', true );
						$app_link        = get_post_meta( get_the_ID(), 'app_link', true );
						if ( $app_visible || is_user_logged_in() ) : ?>
							<?php if ( $current_post % 3 == 0 ) : ?>
								<div class="clearfix visible-xs"></div>
							<?php endif; ?>
							<?php if ( $current_post % 4 == 0 ) : ?>
								<div class="clearfix visible-sm"></div>
							<?php endif; ?>
							<?php if ( $current_post % 6 == 0 ) : ?>
								<div class="clearfix visible-md visible-lg"></div>
							<?php endif; ?>
							<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
								<div class="panel panel-info launcher_item_logged">
									<div class="panel-body">
										<?php
										$thumb_id  = get_post_thumbnail_id();
										$thumb_url = wp_get_attachment_thumb_url( $thumb_id );
										?>
										<a href="<?php echo $app_link; ?>" title="<?php echo $app_description; ?>">
											<img src="<?php echo $thumb_url; ?>" class="center-block" width="128"
											     height="128"/>
										</a>
									</div>
									<div class="panel-footer">
										<?php the_title(); ?>
									</div>
								</div>
							</div>
							<?php $current_post ++; ?>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>

				<?php
				$args  = array(
					'post_type'      => 'app',
					'posts_per_page' => 50,
					'meta_key'       => 'app_visible',
					'meta_value'     => 'true'
				);
				$query = new WP_Query( $args );
				while ( $query->have_posts() ) : $query->the_post();
					$app_description = get_post_meta( get_the_ID(), 'app_description', true );
					$app_visible     = get_post_meta( get_the_ID(), 'app_visible', true );
					$app_link        = get_post_meta( get_the_ID(), 'app_link', true );
					if ( $app_visible || is_user_logged_in() ) : ?>
						<?php if ( $current_post % 3 == 0 ) : ?>
							<div class="clearfix visible-xs"></div>
						<?php endif; ?>
						<?php if ( $current_post % 4 == 0 ) : ?>
							<div class="clearfix visible-sm"></div>
						<?php endif; ?>
						<?php if ( $current_post % 6 == 0 ) : ?>
							<div class="clearfix visible-md visible-lg"></div>
						<?php endif; ?>
						<div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
							<div class="panel panel-warning launcher_item_guest">
								<div class="panel-body">
									<?php
									$thumb_id  = get_post_thumbnail_id();
									$thumb_url = wp_get_attachment_thumb_url( $thumb_id );
									?>
									<a href="<?php echo $app_link; ?>" title="<?php echo $app_description; ?>">
										<img src="<?php echo $thumb_url; ?>" class="center-block" width="128"
										     height="128"/>
									</a>
								</div>
								<div class="panel-footer">
									<?php the_title(); ?>
								</div>
							</div>
						</div>
						<?php $current_post ++; ?>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		</div>
		<?php
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'VAUV_Launcher_Widget' );
} );
