<section class="campagne">
    <h2><?php the_field('section_campagne_don') ?></h2>
    <div class="slider-container"> 
        <ul class="slider_slides">
            <?php 
                $donations = new WP_Query([
                'post_type' => 'campagne-dons',
                'posts_per_page' => -1,
                ]);

                if ($donations->have_posts()) :
                    while ($donations->have_posts()) : $donations->the_post();
                        $image = get_field('image');
                        $imageUrl = $image['url'];
                ?>

                <li class="slider_slide">
                    <figure>
                        <img src="<?php echo esc_url($imageUrl); ?>" alt="<?php the_title(); ?>">
                    </figure>
                    <div>
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_field('description'); ?></p>
                    </div>
                </li>

            <?php
                            wp_reset_postdata();
                    endwhile;
                endif;
            ?>
        </ul>
        <div>
            <button class="control--previous">
                <i class="fa-solid fa-angle-left"></i>
            </button>  
            <button class="control--next">
                <i class="fa-solid fa-angle-right"></i>
            </button>
        </div>
    </div>
</section>