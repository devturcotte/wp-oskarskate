<section class="footer-contact">
    <?php 
        $footerContact = new WP_Query([
            'post_type' => 'infos-contact',
            'post_title' => 'Pied-de-page',
            'posts_per_page' => 1,
        ]);

        if ($footerContact->have_posts()) :
        while ($footerContact->have_posts()) : $footerContact->the_post();
    ?>
    <h2><?php the_field('titre'); ?></h2>
    <ul>
        <li class="courriel"><?php the_field('courriel'); ?></li>
        <li class="telephone"><?php the_field('telephone'); ?></li>
    </ul>
    <?php
        endwhile;
        endif;
    ?>
</section>


