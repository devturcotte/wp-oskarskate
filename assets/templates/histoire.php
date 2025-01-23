<?php
    $histoire = get_field('histoire');
    $image1 = $histoire["image_1"];
    $image1Url = $image1["url"];

    $image2 = $histoire["image_2"];
    $image2Url = $image2["url"];
?>
<section class="histoire">
        <h2><?php echo $histoire["titre"]; ?></h2>
        <div class="images">
            <img class="img-1" src="<?php echo esc_url($image1Url); ?>" alt="histoire image 1">
            <img class="img-2" src="<?php echo esc_url($image2Url); ?>" alt="histoire image 2">
        </div>
        <div class="texte-container">
            <p><?php echo $histoire["texte"]; ?></p>
            <button><?php echo $histoire["bouton"]; ?></button>
        </div> 
</section>