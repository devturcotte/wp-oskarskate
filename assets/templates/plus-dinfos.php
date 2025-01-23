<?php
    $infos = get_field('infos');
    $image1 = $infos["image_1"];
    $image1Url = $image1["url"];

    $image2 = $infos["image_2"];
    $image2Url = $image2["url"];
?>
<div class="plus-dinfos">
        <div>
            <img src="<?php echo esc_url($image1Url); ?>" alt="img">
            <p><?php echo $infos["texte"]; ?></p>
            <div class="ctas">
                <button class="btn-left"><?php echo $infos["bouton_gauche"]; ?></button>
                <button class="btn-right"><?php echo $infos["bouton_droit"]; ?></button>
            </div>
        </div>
        <img src="<?php echo esc_url($image2Url); ?>" alt="img-2">
</div>