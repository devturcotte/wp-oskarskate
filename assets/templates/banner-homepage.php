<section class="banner-homepage">
    <?php
        $homepageBanner = new WP_Query([
            'post_type' => 'bannieres',
            'posts_per_page' => 1,
        ]);

        if ($homepageBanner->have_posts()) :
        while ($homepageBanner->have_posts()) { 
            $homepageBanner->the_post();
            $image = get_field('image');
            $imageUrl = $image['url'];
    ?>
        <img src="<?php echo esc_url($imageUrl) ?>" alt="Bannière d'accueil">
        <section>
            <h2><?php the_field('titre'); ?></h2>
            <p><?php the_field('texte'); ?></p>
            <button><?php the_field('bouton_dinteraction'); ?></button>
        </section>
        
    <?php
        }
        endif;
    ?>
</section>
