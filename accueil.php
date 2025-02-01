<?php 
/* 
Template Name: Accueil
*/
?>

<?php get_header(); ?>

<main class="main_accueil">
    <?php get_template_part('/assets/templates/banner-homepage'); ?>

    <img src="<?php echo get_template_directory_uri() ?>/assets/images/rails.png" alt="Skate Rails" class="rails">
</main>

<?php get_footer(); ?>