<tr class="titulo">
	<?php foreach( $query['results'][0] as $field=>$value ){ ?>
	<td class="">
		<?php echo $field ?>
	</td>
	<?php } ?>

	<td width="80">
		Ações
	</td>
	
</tr>
<?php foreach($query['results'] as $item){ ?>
    <tr class="conteudo">
		<?php foreach( $item as $field=>$value ){ ?>
		<td>
			<?php echo $value; ?>
		</td>
		<?php } ?>
		<td width="80">

		</td>
		
    </tr>
<?php } ?>