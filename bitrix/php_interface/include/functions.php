<?php

function showLoader($style) {
	echo '<img style="' . $style . '"
		src="' . SITE_TEMPLATE_PATH . '/img/preloader.gif"
		alt="preloader">';
}