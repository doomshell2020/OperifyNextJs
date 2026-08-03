<!doctype html>
<html lang=en>
<head>
<meta charset=utf-8>
<meta http-equiv=X-UA-Compatible content="IE=edge">
<meta name=viewport content="width=device-width, initial-scale=1">
<title>School ERP</title>

	<?= $this->Html->css('animate.min.css') ?>
	<?= $this->Html->css('font-awesome.min.css') ?>
	<?= $this->Html->css('bootstrap.min.css') ?>
	<?= $this->Html->css('menu.css') ?>
	<?= $this->Html->css('style.css') ?>
	<?= $this->Html->css('validationEngine.jquery.css') ?>
	<?= $this->Html->css('template.css') ?>
	
	<link rel="shortcut icon" href="<?php echo $this->request->webroot . 'img/favicon.ico'; ?>" type="image/x-icon">
	<link rel=icon href="<?php echo $this->request->webroot . 'img/favicon.ico'; ?>" type="image/x-icon">
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <?= $this->fetch('meta') ?>
	    <?= $this->fetch('css') ?>
	    <?= $this->fetch('script') ?>



</head>
<body id=home>
<div class=onloadimg><img class=loader src=<?php echo $this->request->webroot . 'img/under-construction'; ?> alt=creative-studio width=128 height=128></div>

