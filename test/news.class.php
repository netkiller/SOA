<?php
include_once('../library/news.class.php');
$news = new News();
print_r( $news->getAllByNews('1'));
