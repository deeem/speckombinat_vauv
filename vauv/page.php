<?php get_header(); ?>

	<div class="container breadcrumb_panel">
		<div class="row">
			<div class="col-md-11">
				<ol class="breadcrumb">
					<li><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></li>
					<li class="active"><?php echo the_title(); ?></li>
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

<?php if ( have_posts() ): the_post(); ?>
	<?php the_content(); ?>
<?php endif; ?>

<?php get_footer(); ?>