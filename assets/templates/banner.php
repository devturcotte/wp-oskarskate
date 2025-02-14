<?php 
        $banniere = get_field('banniere');
        $titre = $banniere["titre"];
        $image = $banniere["image"];
        $imageUrl = $image["url"];
        $texte = $banniere["texte"];
        $pageSlug = get_page_template_slug();
        $pageTemplate = str_replace(".php", "", $pageSlug);
        $bouton = $banniere["bouton"];
?>
<section class="banner <?php echo $pageTemplate; ?>">
    <section>
        <img src="<?php echo esc_url($imageUrl); ?>" alt="Bannière">
        <h2><?php echo $titre; ?></h2>
    </section>
    <p class="banner-texte"><?php echo $texte; ?></p>
</section>