<section class="footer-contact">
    <h2>Contactez-nous</h2>
    <?php 
        $footerContact = new WP_Query([
            'post_type' => 'infos-contact',
            'posts_per_page' => -1,
        ]);

        if ($footerContact->have_posts()) :
        while ($footerContact->have_posts()) : $footerContact->the_post();
    ?>
        <ul>
            <li class="courriel"><?php the_field('courriel'); ?></li>
            <li class="telephone"><?php the_field('telephone'); ?></li>
        </ul>
    <?php
        endwhile;
        endif;
    ?>
</section>


