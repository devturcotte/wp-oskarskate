<?php 
/* 
Template Name: Nous Connaitre
*/
?>

<?php get_header(); ?>

<main class="main_nous-connaitre">
    <!-- IMAGE ENTÊTE & TITRE -->
    <?php get_template_part('/assets/templates/banner-nous-connaitre'); ?>

    <!-- SECTION CARTES  -->
    <?php get_template_part('/assets/templates/cards_nous-connaitre'); ?>

    <!-- SECTION ÉQUIPE OSKAR -->
    <?php get_template_part('/assets/templates/equipe'); ?>

    <!-- SECTION FAQ -->
    <?php get_template_part('/assets/templates/faq'); ?>

    <!-- SECTION AUTRES QUESTIONS -->
    <?php get_template_part('/assets/templates/en-discuter'); ?>
</main>

<?php get_footer(); ?>