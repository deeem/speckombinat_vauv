<?php get_header(); ?>

<div class="container">

	<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
	<div class="row breadcrumb_panel">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></li>
				<li class="active">
					<?php the_title(); ?>					
				</li>
			</ol>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<p>				
			<span class="glyphicon glyphicon-file"></span>&nbsp;<a href="<?php echo wp_get_attachment_url($post->ID) ?>"><?php the_title(); ?></a>
			</p>			
		</div>
	</div>
	<?php endwhile; endif; ?>

</div><!-- /.container -->

<?php get_footer(); ?>