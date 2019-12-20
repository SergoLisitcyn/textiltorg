<div class="logo_bant"></div>
<?if(!IS_HOME):?>
    <a href="/">
        <img alt="" src="<?=SITE_TEMPLATE_PATH?>/mod_files/ce_images/template_images/logo.png" data-ami-mbgrp="Главная">
    </a>
<? else: ?>
    <img onclick="location.reload()" alt="" src="<?=SITE_TEMPLATE_PATH?>/mod_files/ce_images/template_images/logo.png" data-ami-mbgrp="Главная">
<? endif ?>