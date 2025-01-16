<section class="banner nousconnaitre">
    <?php
        $bannerActivites = new WP_Query([
            'post_type' => 'bannieres',
            'posts_per_page' => 1,
            'offset' => 4,
        ]);

        if ($bannerActivites->have_posts()) :
        while ($bannerActivites->have_posts()) { 
            $bannerActivites->the_post();
            $image = get_field('image');
            $imageUrl = $image['url'];
    ?>
        <img src="<?php echo esc_url($imageUrl) ?>" alt="Bannière page nous connaitre">
        <h2><?php the_field('titre'); ?></h2>
        <p><?php the_field('texte'); ?></p>
    <?php
        }
        endif;
    ?>
</section>