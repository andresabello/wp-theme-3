<?php get_header(); ?>
<div class="row">
  <div class="box">
    <div class="col-lg-8">
      <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
      <div class="ac-blog">
        <h1><a href="<?php the_permalink(); ?>">
          <?php the_title(); ?>
          </a></h1>
        <?php if ( has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="pull-left">
        <?php the_post_thumbnail('full', array( 'class'   => "img-responsive feature-image")); ?>
        </a>
        <?php endif; ?>
        <div class="byline">
          <p>by
            <?php the_author_posts_link(); ?>
            on <span class="date">
            <?php the_time('l F d, Y'); ?>
            </span><br/>
            Posted in:
            <?php the_category(', '); ?>
            |
            <?php                        the_tags('Tagged with: ',' • ','<br />'); ?>
          </p>
        </div>
        <?php                     $content = get_the_content();                    echo substr($content, 0, 350) . '<span class="ac-more"><a href="' . get_the_permalink() . '">... Read More → </a></span>';                    ?>
      </div>
      <hr>
      <?php endwhile; else: ?>
      <p>
        <?php _e('No posts were found. Sorry!'); ?>
      </p>
      <?php endif; ?>
       <!-- Columns for Insurance Companies -->
                <div class="insurance-image">
                    <?php if ( !function_exists('dynamic_sidebar')
                        || !dynamic_sidebar("Upper Footer") ) : ?>  
                    <?php endif; ?>                       
                </div>
    </div>
    <?php get_sidebar(); ?>
  </div>
</div>
<?php get_footer(); ?>