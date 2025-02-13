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

    <ul class="socials">
        <li>
            <a href="https://www.facebook.com/magogskateplaza" target="_blank">
                <i class="fa-brands fa-facebook"></i>
            </a>
        </li>
        <li>
            <a href="https://youtube.com" target="_blank">
                <i class="fa-brands fa-youtube"></i>
            </a>
        </li>
    </ul>
    <?php
        endwhile;
        endif;
    ?>
</section>


