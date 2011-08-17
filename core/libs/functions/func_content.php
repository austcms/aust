<?php
	/*
	ÃNDICE DAS FUNÃÃES
	#######
	1. function mostra_dados_li($table, $columns, $condicao_where, $order = '', $result, $chardivisor = '', $charend = '')
		DescriÃ§Ã£o = FunÃ§Ã£o que retorna um resultado de uma busca no banco de dados. O
					resultado pode retornar em forma de listagem.
			$table = Tabela no db de onde retornarÃ¡ os resultados.
			$columns = Quais as colunas da tabela do db serÃ£o usadas. Ã uma ARRAY. Ex.: "ARRAY('id', 'titulo')"
			$condicao_where = Ã a condiÃ§Ã£o para um determinado resultado. A string deve comeÃ§ar com "WHERE" e seguir
								com "campo='valor'". Ex.: "WHERE campo='valor'"
			$result_format = Ã a forma como o resultado serÃ¡ apresentado na tela. Podem ser usadas tags HTML.
								Ex.: "<li>Titulo = &%titulo</li>". &%titulo, quando inicia estritamente com &%,
								indica ao cÃ³digo para substituir o termo que segue (neste caso titulo) pelo
								resultado do campo indicado em $columns.
			$chardivisor = Ã a string que estarÃ¡ entre cada resultado. Ex.: "', '".
			$charend = Ã a string que estarÃ¡ ao final de todos os resultados. Ex.: "'.'".
	*/

	function mostra_dados_li($table, $columns, $condicao_where, $order = '', $result_format, $chardivisor = '', $charend = ''){
		for($i = 0; $i < count($columns); $i++){
			$fields .= $columns[$i];
			if($i != count($columns) - 1){
				$fields .= ',';
			}
		}
		
		$sql = "SELECT $fields
				FROM $table
				$condicao_where
				$order";
		$mysql = mysql_query($sql);
		$t = mysql_num_rows($mysql);
		$c = 0;
		while($menu = mysql_fetch_array($mysql)){
			$str = $result_format;
			for($i = 0; $i < count($columns); $i++){
				$str = str_replace("&%" . $columns[$i], $menu[$columns[$i]], $str);
			}
			echo $str;
			if($c < $t-1){
				echo $chardivisor;
			} else {
				echo $charend;
			}
			$c++;
		}
	}
	


?>