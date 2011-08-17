<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<link rel="stylesheet" href="<?php echo INSTALLATION_DIR; ?>style.css" type="text/css" />

<script type="text/javascript" src="<?php echo BASECODE_JS; ?>100_jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:visible:enabled:first').focus();
	});
</script>

</head>

<body>

<div id="outer" class="<?php if( !empty($errorStatus) ) echo 'error_message'; ?>">
  <div id="conteudo">
		

