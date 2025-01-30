<?php 
/* Header Template */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php wp_head(); ?>
</head>

<body>
    <header>
        <nav class="desktop-nav">
            <div class="logo">
                <a href="<?php echo site_url(); ?>">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-header.svg" alt="Logo: Oskar Skate & Art">
                </a>
            </div>
            <?php
                wp_nav_menu( $arg = array (
                    'menu' => 'Header',
                    'menu_class' => 'desktop-navigation',
                    'theme_location' => 'primary'
                ));
            ?>
            <div class="mask"></div>
        </nav>
        <nav>
            <div class="mobile-header">
                <a href="<?php echo site_url(); ?>">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-header-mobile.svg" alt="Logo: Oskar Skate & Art">
                </a>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/menu-burger.svg" class="btn-menu" data-dialog="#menu-mobile" />
            </div>
            <aside id="menu-mobile" class="dialog">
                <div class="mobile-navigation">
                    <?php
                        wp_nav_menu( array(
                            'menu' => 'Header',
                            'container' => false,
                            'theme_location' => 'primary',
                        ));
                    ?>
                    <div class="btn-close">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/btn-close.svg" />
                    </div>
                </div>
            </aside>
        </nav>
    </header>