<?php
    $benevolat = get_field('benevolat');
    $image = $benevolat["image"];
    $imageUrl = $image["url"];

    $titre = $benevolat["titre"];
    $texte = $benevolat["texte"];
    $bouton = $benevolat["bouton"];
?>
<div class="benevolat">
        <img src="<?php echo esc_url($imageUrl) ?>" alt="Benevolat">
        <section>
            <h2><?php echo $titre; ?></h2>
            <p><?php echo $texte; ?></p>
            <button><?php echo $bouton; ?></button>
        </section>
</div>