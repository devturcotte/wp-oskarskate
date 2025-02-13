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
        <nav>
            <div class="logo">
                <a href="<?php echo site_url(); ?>">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/logo-header.svg" alt="Logo: Oskar Skate & Art">
                </a>
            </div>
            <div class="content">
                <?php
                    wp_nav_menu( $arg = array (
                        'menu' => 'Header',
                        'menu_class' => 'nav-items nav-hidden',
                        'theme_location' => 'primary'
                    ));
                ?>
                <div class="mask"></div>
            </div>
            <button class="open-btn">
                <i class="fa-solid fa-bars"></i>
            </button> 
            <button class="close-btn hidden">
                <i class="fa-solid fa-x"></i>
            </button> 
        </nav>
</header>