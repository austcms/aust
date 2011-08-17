<?php
/**
 * Arquivo que contém interface para configurar estrutura que usa este módulo depois de já estar instalado.
 *
 * Ex: Inserir novos campos
 *
 * Inicialmente há todo o código PHP para executar as funções requisitadas e o FORM html está no final do documento. O action
 * dos FORMs enviam as informações para a própria página
 *
 * @category Cadastro
 * @since v0.1.6 16/08/2009
 */

/**
 * INICIALIZAÇÃO
 */
$tabela_da_estrutura = $module->LeTabelaDaEstrutura($_GET['aust_node']);


/**
 * FUNÇÃO
 *
 * Campo necessário? Desativar campo? Usar em listagem?
 *
 * Se $_GET['function'] existir
 */
if(!empty($_GET['function'])){
	/**
	 * DESATIVAR CAMPO
	 */
	if($_GET['function'] == "desativar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					desativado='1'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo desativado com sucesso";
		else
			$status[] = "Erro ao desativar campo.";
	}
	/**
	 * ATIVAR CAMPO
	 */
	if($_GET['function'] == "ativar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					desativado='0'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo ativado com sucesso";
		else
			$status[] = "Erro ao ativar campo.";
	}

	/**
	 * NECESSARIO
	 */
	if($_GET['function'] == "necessario"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					necessario='1'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Preenchimento do campo ajustado para necessário com sucesso.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * CAMPO NÃO OBRIGATÓRIO
	 */
	if($_GET['function'] == "desnecessario"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					necessario='0'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Não é necessário preenchimento obrigatório do campo ajustado com sucesso.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * LISTAR
	 *
	 * Campo deve aparecer em listagens
	 */
	if($_GET['function'] == "listar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					listagem='1'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo aparecerá na listagem de cadastro.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * NÃO LISTAR
	 *
	 * Campo não deve aparecer em listagens
	 */
	if($_GET['function'] == "naolistar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					listagem='0'
				WHERE
					chave='".$_GET['w']."' AND
					categorias_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "O campo selecionado não aparecerá mais em listagens.";
		else
			$status[] = "Erro ao executar ação.";
	}

}

/**
 * Desativar campos
 */
if(!empty($_GET['function'])){
	if($_GET['function'] == 'desativar' AND !empty($_GET['w'])){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					tipo='campodesativado'


";
	}
}
?>

<?php /* use this as template */ ?>
<div class="widget_group hidden">
	<div class="widget">
		<div class="titulo">
		</div>
		<div class="content">

		</div>
		<div class="footer"></div>
	</div>

	<div class="widget">
		<div class="titulo">
			<h3></h3>
		</div>
		<div class="content">
			<p>
				
			</p>

		</div>
		<div class="footer"></div>
	</div>
</div>

<?php /* use this as template */ ?>
<div class="widget_group hidden">

	<div class="widget">
		<div class="titulo">
			<h3></h3>
		</div>
		<div class="content">
			<p></p>
		</div>
		<div class="footer"></div>
	</div>

	<div class="widget">
		<div class="titulo">
			<h3></h3>
		</div>
		<div class="content">
			<p></p>
		</div>
		<div class="footer"></div>
	</div>
</div>
