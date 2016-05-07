<?php get_header(); ?>

    <div class="container breadcrumb_panel">
        <div class="row">
            <div class="col-md-11">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a>
                    </li>
                    <li>
                        <?php $cpt = get_post_type_object( get_post_type() ); ?>
                        <a href="<?php echo get_post_type_archive_link( get_post_type() ); ?>"><?php echo $cpt->label; ?></a>
                    </li>
                    <li class="active"><?php the_title(); ?></li>
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

    <div class="container">
        <div class="row">
            <main class="col-md-8 kb-single-article">
            <?php if ( have_posts() ) : the_post();
            // т.к. данные хранятся в текстовом виде, а не в булевом, то сначала преобразуем их в булев тип
            $kb_visible = get_post_meta( get_the_ID(), 'kb_visible', true ) === "true" ? true : false;
                if ( $kb_visible || is_user_logged_in() ) : ?>
                    <div class="well">
                        <h2 class="kb-single-article-heading"><?php the_title(); ?> <?php edit_post_link(' <span class="glyphicon glyphicon-pencil"></span>'); ?></h2>
                    </div>
                    <article><?php the_content(); ?></article>
                <?php else : ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Доступ запрещён!</strong> Эта страница не доступна для общего просмотра. <a href="#">Авторизация</a>
                    </div>
                <?php endif; ?>
            </main>
            <?php endif; ?>

            <aside class="col-sm-4 kb-single-aside">
                <h4>еще в этой категории</h4>
                <ul>
                <?php
                $terms   = wp_get_post_terms( $post->ID, 'kb_categories' );
                $term_id = $terms[0]->term_id;

                $args = array(
                    'post_type' => 'kb',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'kb_categories',
                            'field'    => 'term_id',
                            'terms'    => $term_id
                        )
                    )
                );

                if ( ! is_user_logged_in() ) {
                    $args['meta_key']   = 'kb_visible';
                    $args['meta_value'] = 'true';
                }

                $loop    = new WP_Query( $args );
                if ( $loop->have_posts() ) :
                    while( $loop->have_posts() ) : $loop->the_post(); ?>
                <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
                <?php
                    endwhile;
                endif;
                wp_reset_query();
                ?>
                </ul>
            </aside>
        </div>
    </div><!-- /.container -->

<?php get_footer(); ?>