<?php 
        $investir = get_field('module_investir');
        $titre = $investir["titre"];
        $texte = $investir["text"];
        $bouton = $investir["label_button"];
        $urlbtn = $investir["url_button"];
?>
<section class="module-investir">
    <h2><?php echo $titre ?></h2>
    <p><?php echo $texte ?></p>
    <a class="btn-investir" target="_blank" href=<?php echo $urlbtn?>><?php echo $bouton ?></a>
</section>
