<?php
        $discuter = get_field("discuter");
        $sticker = $discuter["sticker"];
        $stickerUrl = $sticker["url"]
?>
<section class="discuter">
        <h2><?php echo $discuter["texte"]; ?></h2>
        <button><?php echo $discuter["bouton"]; ?></button>
        <img src="<?php echo esc_url($stickerUrl) ?>" alt="Sticker" class="sticker">
</section>