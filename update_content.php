<?php
require_once("kirby_publisher.php");
$CONTENT_PATH="/var/www/mina.ink/public_html/content/01-home";
$TOOLKIT_PATH="/var/www/mina.ink/public_html/kirby/toolkit";
$publisher = new kirby_publisher($CONTENT_PATH,$TOOLKIT_PATH,4);
$publisher->update_published_status();