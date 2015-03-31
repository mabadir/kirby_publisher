<?php
require_once("kirby_publisher.php");
$CONTENT_PATH="/var/www/public_html/content/01-home";
$TOOLKIT_PATH="/var/www/public_html/kirby/toolkit";
$publisher = new kirby_publisher($CONTENT_PATH,$TOOLKIT_PATH,4);
$publisher->update_published_status();