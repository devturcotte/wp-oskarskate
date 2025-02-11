<?php
/* Footer Template */
?>

<footer>
    <div class="footer-top">
        <nav>
            <a href="<?php echo site_url(); ?>" class="logo-mobile">
                <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-footer.png"
                    alt="Logo Oskar Skate & Art">
            </a>
            <?php
            wp_nav_menu($arg = array(
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
        <div class="content">
            <p class="copyright">© <?php echo date("Y"); ?> OSKAR Skate & ART - Tous droits réservés</p>
            <a href="<?php echo site_url(); ?>" class="logo-desktop">
                <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-footer.png"
                    alt="Logo Oskar Skate & Art">
            </a>
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
        </div>
    </div>
</footer>
<?php get_template_part('assets/templates/modal-activites'); ?>
<?php wp_footer(); ?>
<script src="https://unpkg.com/xlsx-js-style/dist/xlsx.full.min.js"></script>

</body>

</html>