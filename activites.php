<?php
/* 
Template Name: Activites
*/
?>

<?php get_header(); ?>

<main class="main_activites">
<?php get_template_part('/assets/templates/banner'); ?>


    <?php
    // 1) Récupérer le HTML généré par le shortcode
    $timeline_output = do_shortcode('[cool-timeline layout="vertical" skin="default" show-posts="10" icons="YES" date-format="j F Y" order="ASC"]');
    // 2) Appliquer le même filtre que the_content
    $timeline_output = apply_filters('the_content', $timeline_output);
    // 3) Afficher le HTML modifié
    echo $timeline_output;
    ?>
</main>

<?php get_footer(); ?>