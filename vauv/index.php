<?php get_header(); ?>

    <div class="container">

        <div class="row breadcrumb_panel">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></li>
                    <li class="active">
                        <?php the_title(); ?>
                    </li>
                </ol>
            </div>
        </div>

        <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <div class="row">
                <div class="col-md-12">
                    <article>
                        <h2><?php the_title(); ?></h2>

                        <p><?php the_content(); ?></p>
                    </article>
                </div>
            </div>
        <?php endwhile; endif; ?>

    </div><!-- /.container -->

<?php get_footer(); ?>