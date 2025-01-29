<?php 
/* 
Template Name: Nous Connaitre
*/
?>

<?php get_header(); ?>

<main class="main_nous-connaitre">
    <?php 
        get_template_part('/assets/templates/banner'); 
        get_template_part('/assets/templates/cards_nous-connaitre');
        get_template_part('/assets/templates/equipe');
        get_template_part('/assets/templates/faq');
        get_template_part('/assets/templates/discuter');
    ?>
</main>

<?php get_footer(); ?>