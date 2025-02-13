<?php 
/* Footer Template */
?>

    <footer>
        <div class="footer-top">
            <nav>
                <a href="<?php echo site_url(); ?>" class="logo-mobile">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-footer.png" alt="Logo Oskar Skate & Art">
                </a>
                <?php
                    wp_nav_menu( $arg = array (
                        'menu' => 'Footer',
                        'menu_class' => 'footer-navigation',
                        'theme_location' => 'footer'
                    ));
                ?>
            </nav>

            <?php get_template_part('/assets/templates/footer-contact'); ?>
        </div>
        
        <div class="footer-bottom">
            <div class="hr"></div>
            <p class="copyright">© <?php echo date("Y");?> OSKAR Skate & ART - Tous droits réservés</p>
        </div>
    </footer>
    <?php get_template_part('assets/templates/modal-activites'); ?>
    <?php wp_footer(); ?>
</body>
</html>