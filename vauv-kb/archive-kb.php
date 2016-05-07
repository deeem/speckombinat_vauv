<?php get_header(); ?>

    <div class="container breadcrumb_panel">
        <div class="row">
            <div class="col-md-11">
                <ol class="breadcrumb">
                    <li><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></li>
                    <li class="active">
                        <?php $cpt = get_post_type_object( get_post_type() ); ?>
                        <?php echo $cpt->label; ?>
                    </li>
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
        <?php
        /*
         * Отображаем категории с ссылками на посты
         * с учётом залогинен ли пользователь (is_user_logged_in())
         * и отметки общедоступности поста (meta_key: visible).
         * Если категория не содержит общедоступных постов, то и сама категория не выводится (т.н. "скрытая категория")
         */

        // получаем список terms для таксономии kb_categories
        $post_type = 'kb';
        $taxonomy  = 'kb_categories';
        $terms     = get_terms( $taxonomy );
        $term_counter = 0;

        foreach ( $terms as $term ) :
            // для каждого из terms получаем список постов
            $args = array(
                'post_type' => $post_type,
                'tax_query' => array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $term->slug
                    )
                )
            );
            // если пользователь не залогинился, то получаем только общедоступные записи
            if ( ! is_user_logged_in() ) {
                $args['meta_key']   = 'kb_visible';
                $args['meta_value'] = 'true';
            }
            $query = new WP_Query( $args );
            // если по указанным фильтрам для данной категории нет постов, то и выводить заголовок категории не будем
            if ( $query->post_count ) :
                //fix зацепившихся блоков
                if ( $term_counter % 3 == 0 ) : ?>
                <div class="clearfix"></div>
                <?php endif; ?>

                <div class="col-sm-4">
                    <div class="kb-list">

                        <p class="kb-list-heading">
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <?php echo $term->name; ?>
                            <?php if ( $term->description ) : ?>
                                <span class="glyphicon glyphicon-info-sign kb-list-description pull-right"
                                      title="<?php echo $term->description; ?>"></span>
                            <?php endif; ?>
                        </p>

                        <ul>
                            <?php while ( $query->have_posts() ) : $query->the_post();
                                $kb_visible = get_post_meta( get_the_ID(), 'kb_visible', true ) === "true" ? true : false;
                                if ( $kb_visible || is_user_logged_in() ): ?>
                                    <li class="kb-list-item">
                                        <span class="glyphicon glyphicon-file"></span>
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        <?php if ( $kb_visible ): ?>
                                            <span class="glyphicon glyphicon-eye-open" title="виден для всех"></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>

                    </div><!-- .kb-list -->
                </div><!-- .col -->

                <?php $term_counter++; ?>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        <?php endforeach; ?>

        </div>
    </div>
<?php get_footer(); ?>