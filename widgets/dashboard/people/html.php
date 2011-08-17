<?php
$users = Connection::getInstance()->query("SELECT nome FROM admins WHERE is_blocked!='1' AND is_deleted!='1'");
?>

<ul>
	<?php
	foreach( $users as $user ){
		?>
		<li><?php echo $user['nome']?></li>
		<?php
	}
	?>
</ul>