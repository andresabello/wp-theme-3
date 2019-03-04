<?php get_header(); ?>
<div class="row">

    <div class="col-md-8 ac-content">

        <?php if(have_posts()) : while(have_posts()) : the_post(); ?>

            <?php the_content(); ?>

        <?php endwhile; else: ?>

        <p><?php _e('No pages were found. Sorry!'); ?></p>

        <?php endif; ?>

        <!-- Columns for Insurance Companies -->

        <?php if ( !function_exists('dynamic_sidebar')

                || !dynamic_sidebar("Upper Footer") ) : ?>  

        <?php endif; ?>   

    </div>

    <?php get_sidebar(); ?>                 

</div>

<?php get_footer(); ?>