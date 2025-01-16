<section class="partenaires">
    <?php
        $partenaires = new WP_Query([
            'post_type' => 'partenaires',
            'posts_per_page' => 1,
        ]);

        if ($partenaires->have_posts()) :
        while ($partenaires->have_posts()) { 
            $partenaires->the_post();
            $images = [get_field('partenaire-1'), get_field('partenaire-2'), get_field('partenaire-3'), get_field('partenaire-4')];
            $imageUrls = [$images[0]['url'], $images[1]['url'], $images[2]['url'], $images[3]['url']];
    ?>
    <h2><?php the_field('titre'); ?></h2>
    <ul>
        <li>
            <img src="<?php echo esc_url($imageUrls[0]); ?>" alt="Partenaire">
        </li>
        <li>
            <img src="<?php echo esc_url($imageUrls[1]); ?>" alt="Partenaire">
        </li>
        <li>
            <img src="<?php echo esc_url($imageUrls[2]); ?>" alt="Partenaire">
        </li>
        <li>
            <img src="<?php echo esc_url($imageUrls[3]); ?>" alt="Partenaire">
        </li>
    </ul>
    <?php
        };
        endif;
    ?>
</section>