<?php get_header(); ?>

	<div class="container breadcrumb_panel">
		<div class="row">
			<div class="col-md-11">
				<ol class="breadcrumb">
					<li class="active"><?php bloginfo( 'name' ); ?></li>
				</ol>
			</div>
			<div class="col-md-1">
				<?php if ( ! is_user_logged_in() ) : ?>
					<button type="button" class="btn btn-default"
					        data-toggle="modal"
					        data-target="#loginModal">
						Log In
					</button>
				<?php endif; ?>
				<?php if ( is_user_logged_in() ) : ?>
					<a class="btn btn-default" href="<?php echo wp_logout_url(); ?>">Logout</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php if ( is_active_sidebar( 'vauv_fp_widgets' ) ): ?>
	<?php dynamic_sidebar( 'vauv_fp_widgets' ); ?>
<?php endif ?>


	<!--
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-warning">
				<div class="panel-heading">Разное</div>
				<?php
	/*				$args = array(
						'theme_location' => 'frontpage-links',
						'container'      => false,
						'items_wrap'     => '<ul class="list-group">%3$s</ul>',
						'walker'         => new frontpage_links_Walker
					);
					wp_nav_menu( $args ); */ ?>
			</div>
		</div>
	</div>
	-->


<?php get_footer(); ?>