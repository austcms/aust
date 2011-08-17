<?php
/*
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{
	/**
	 * listar()
	 *
	 * Listagem de Contéudos
	 */
	public function listing(){

		/**
		 * <h2> HEADER
		 */
		$this->set('h1', Aust::getInstance()->getStructureNameById($_GET['aust_node']) );

		if((!empty($filter)) AND ($filter <> 'off')){
			$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
		}

		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';

		/*
		 * MÊS ATUAL
		 */
		if( empty($_GET['m']) )
			$month_int = date("n");
		else if( is_numeric($_GET['m']) )
			$month_int = $_GET['m'];
		else
			$month_int = date("n");
		/*
		 * ANO ATUAL
		 */
		if( empty($_GET['y']) )
			$year_int = date("Y");
		else if( is_numeric($_GET['y']) )
			$year_int = $_GET['y'];
		else
			$year_int = date("Y");

		$this->set("month_int", $month_int);
		$this->set("year_int", $year_int);

		/*
		 * PAGINAÇÃO
		 */
		/*
		 * Página atual
		 */
		$pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
		/*
		 * Resultados por página
		 */
		$num_por_pagina = '20';
		$this->set('numPorPagina', $num_por_pagina);

		/*
		 * SQL para listagem
		 */
		$params = array(
			'austNode' => $categorias,
			'pagina' => $pagina,
			'resultadosPorPagina' => $num_por_pagina,
			'where' => "AND ( MONTH(start_datetime)=".$month_int." AND YEAR(start_datetime)=".$year_int." )",
		);
		$sql = $this->module->loadSql($params);
		$this->set('sql', $sql );

		/*
		 * RESULTADO PROCESSADO
		 *
		 * Query com resultado
		 */
		$query = $this->module->connection->query($sql);
		$results = array();
		foreach( $query as $value ){

			/*
			 * Ajusta variáveis
			 */
			$day = date("j", strtotime($value['start_datetime']) ) * 1;
			$endDay = date("j", strtotime($value['end_datetime']) ) * 1;

			if( empty($value['title']) )
				$value['title'] = "Sem título";

			/*
			 * Salva vários dias se necessário
			 */
			if( $day != $endDay ){
				$i = $endDay - $day;
				$currentDay = $day;
				while( $currentDay <= $endDay ){

					$results[$currentDay][] = $value;
					$currentDay++;
					$i--;
				}
			} else {
				$results[$day][] = $value;
			}
		}
		ksort($results);
		$this->set('results', $results );

	}

	public function create(){
		$this->render('form');
	}

	public function edit(){
		$this->set('tagh2', "Editar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']) );
		$this->set('tagp', 'Edite o conteúdo abaixo.');

		$w = (!empty($_GET['w'])) ? $_GET['w'] : '';
		$this->set('w', $w);


		$sql = "
				SELECT
					*
				FROM
					".$this->module->getContentTable()."
				WHERE
					id='$w'
				";
		$query = $this->module->connection->query($sql);
		$this->set('dados', $query[0] );
		
		$this->render('form');
	}

	public function save(){

		$_POST['frmupdated_on'] = date("Y-m-d H:i:s");
		$_POST['frmcategoria_id'] = $_POST['aust_node'];

		$user = User::getInstance();
		if( empty($_POST['frmactor_admin_id']) )
			unset($_POST['frmactor_admin_id']);
		else {
			$_POST['frmactor_admin_name'] = $user->getNameById($_POST['frmactor_admin_id']);
		}

		$_POST['frmadmin_id'] = $user->getId();

		if( empty($_POST['start_date']) ){
			return false;
		}
		$_POST['start_date'] = str_replace("/", "-", $_POST['start_date']);

		if( !empty($_POST['end_date']) )
			$_POST['end_date'] = str_replace("/", "-", $_POST['end_date']);

		/*
		 * Tem horário específico
		 */
		if( $_POST['durationAllDay'] == '0' ){
			$start_time = substr( $_POST['start_time'], 0, 2).':'.substr( $_POST['start_time'], 2, 4).':00';
			$end_time = substr( $_POST['end_time'], 0, 2).':'.substr( $_POST['end_time'], 2, 4).':00';
		} else {
			$start_time = '00:00:00';
			$end_time = '00:00:00';
		}


		$_POST['frmoccurs_all_day'] = $_POST['durationAllDay'];
		$_POST['frmstart_datetime'] = date("Y-m-d", strtotime($_POST['start_date']) ).' '.$start_time;

		if( empty($_POST['end_date']) ){
			$_POST['frmend_datetime'] = date("Y-m-d", strtotime($_POST['start_date']) ).' '.$end_time;
		} else {
			$_POST['frmend_datetime'] = date("Y-m-d", strtotime($_POST['end_date']) ).' '.$end_time;
		}
		
		//pr($_POST);

		$this->set('resultado', $this->module->save($_POST));
	}
	
}
?>