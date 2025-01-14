<?php 
/* Footer Template */
?>

    <footer>
        <div class="footer-top">
            <nav>
                <?php
                    wp_nav_menu( $arg = array (
                        'menu' => 'Footer',
                        'menu_class' => 'footer-navigation',
                        'theme_location' => 'footer'
                    ));
                ?>
            </nav>

            <section class="infolettre">
                <h2>Inscrivez-vous à l'infolettre</h2>
                <input type="text" placeholder="Entrez votre courriel">
                <button class="btn-infolettre" aria-label="Inscription à l'infolettre">Soumettre</button>
            </section>

            <?php get_template_part('/assets/templates/footer-contact'); ?>
        </div>
        
        <div class="footer-bottom">
            <div class="hr"></div>
            <div class="content">
                <p class="copyright">© <?php echo date("Y");?> OSKAR Skate & ART - Tous droits réservés</p>
                <a href="<?php echo site_url(); ?>" class="logo">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-footer.png" alt="Logo Oskar Skate & Art">
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
    
</body>
</html>