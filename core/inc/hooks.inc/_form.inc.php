<form action="adm_main.php?section=hooks&action=save" method="POST" class="simples">

	<?php if( !empty($data["id"]) ){ ?>
		<input type="hidden" name="id" value="<?php echo $data["id"] ?>" />
	<?php } ?>
	<input type="hidden" name="hook_engine" value="<?php echo $_GET["hook_engine"] ?>" />

	<?php if( !empty($data) ){ ?>
		<div class="field" style="margin-bottom: 10px">
			<label for="when_action">Engine</label>
			<?php echo $data['hook_engine']; ?>
		</div>
	<?php } ?>
	
	<div class="field">
		<label for="when_action">Quando o usuário: </label>
		<select name="when_action" id="when_action">
			<option value="delete_record" <?php if(@$data['when_action']=='delete_record') echo "selected='selected'" ?>>excluir um registro</option>
			<option value="approve_record" <?php if(@$data['when_action']=='approve_record') echo "selected='selected'" ?>>aprovar um registro</option>
			<option value="save_record" <?php if(@$data['when_action']=='save_record') echo "selected='selected'" ?>>salvar um registro</option>
		</select>
	</div>
	<div class="field">
		<label for="node_id">Relacionado a uma estrutura:</label>
		<?php $structures = Aust::getInstance()->getStructures(); ?>
		<select name="node_id">
		<?php foreach( $structures as $site){ ?>
			
			<optgroup label="<?php echo $site["Site"]["nome"] ?>">
			<?php foreach( $site["Structures"] as $structure ){ ?>
				<option value="<?php echo $structure["id"]; ?>" <?php if(@$data['node_id']==$structure["id"]) echo "selected='selected'" ?>>
					&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $structure["nome"]; ?>
				</option>
			<?php } ?>
			</optgroup>

		<?php } ?>
		</select>
	</div>
	<div class="field">
		<label for="description">Descrição</label>
		<textarea name="description" id="description" rows="5"><?php echo @$data['description'] ?></textarea>
	</div>
	<div class="field">
		<label for="perform">Realizar seguinte ação</label>
		<textarea name="perform" id="perform" rows="17"><?php echo @$data['perform'] ?></textarea>
		<span class="explanation">
			Campo <em>Perform</em>.
		</span>
	</div>
	<br>
	<div class="field">
		<input type="submit" value="Enviar" name="submit" />
	</div>
</form>