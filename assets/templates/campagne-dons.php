<section class="campagne">
<h2><?php the_field('don_section_title') ?></h2>
<ul>
    <?php 
        $donations = new WP_Query([
            'post_type' => 'campagne-dons',
            'posts_per_page' => 3,
        ]);

        if ($donations->have_posts()) :
            while ($donations->have_posts()) : $donations->the_post();
                $image = get_field('image');
                $imageUrl = $image['url'];
    ?>

        <li>
            <img src="<?php echo esc_url($imageUrl); ?>" alt="<?php the_title(); ?>">
            <div>
            <h3><?php the_title(); ?></h3>
            <p><?php the_field('description', $donations->ID); ?></p>
            </div>
            <div>
                <span>fond nécessaire :</span> <span><?php the_field('objectif', $donations->ID); ?>$</span>
            </div>
        </li>

    <?php
            endwhile;
        endif;
    ?>
</ul>
</section>