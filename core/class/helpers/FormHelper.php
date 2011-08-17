<?php
/**
 * HELPER
 *
 * Form
 *
 * Contém gerador de elementos HTML automáticos
 *
 * @since v0.1.6, 13/07/2009
 */
class FormHelper
{

	protected $modelName;

	function __construct(){
		
	}

	public function create($modelName, $options = ''){
		$conteudo = "";

		/**
		 * Ajusta o nome do model no objeto instanciado
		 */
		if( !empty($modelName) ){
			$this->modelName = $modelName;
		}

		/**
		 * Controller
		 *
		 * Verifica se o usuário especificou um controller que deverá
		 * ser usado para salvar as informações do formulário
		 */
		$controller = (empty($options["controller"])) ? 'content' : $options["controller"];
		/**
		 * Action
		 *
		 * O action padrão para salvar um formulário é 'save'
		 */
		$action = (empty($options["action"])) ? 'save' : $options["action"];

		$conteudo.= '<form method="post" action="adm_main.php?section='.$controller.'&action='.$action.'" class="formHelper" enctype="multipart/form-data">';

		/**
		 * INPUTS HIDDEN
		 */
		if( !empty($options["hidden"]) ){
			foreach( $options["hidden"] as $chave=>$value){
				if( is_string($value) ){
					$conteudo.= $value;
				}
			}
		}

		/**
		 * MODEL PRINCIPAL
		 */
		if( !empty($modelName) ){
			$conteudo.= '<input type="hidden" name="modelName" value="'.$modelName.'" />';
		}
		/**
		 * Indica que é formulário de um FormHelper
		 */
		$conteudo.= '<input type="hidden" name="sender" value="formHelper" />';

		return $conteudo;

	}
	/**
	 *
	 * @param string $fieldName Nome do campo no banco de dados.
	 * @param array $options Opções de configurações e amostragem.
	 * @return array Código HTML para o form input pedido.
	 */
	public function input($fieldName, $options = ''){
		$conteudo ='';

		/**
		 * VALUE
		 *
		 * Se value não foi especificado
		 */
		if( $options["value"] != '' ){
			$inputValue = $options["value"];
		} else {
			$inputValue = "";
		}

		/**
		 * After
		 *
		 */
	 	$after = false;
		if( !empty($options["after"]) ){
			$after = $options["after"];
		}

		/**
		 * Class
		 *
		 */
	 	$class = "";
		if( !empty($options["class"]) ){
			$class = $options["class"];
		}

		// DIV FIELD
		$conteudo.= '<div class="input '.$class.'">';
		
		/**
		 * Gera nomes para os inputs
		 */
		if( !empty($this->modelName) ){
			$inputName = "data[".$this->modelName."][".$fieldName."]";
		} else {
			$inputName = "data[".$fieldName."]";
		}

		/**
		 * TIPOS DE CAMPOS
		 */
		/**
		 * Campo id sempre será hidden
		 */
		if( $fieldName == "id" ){
			$inputType = "hidden";
		}
		/**
		 * Por padrão, campos são do tipo texto
		 */
		else if( empty($options["type"]) ){
			$inputType = "text";
		}
		/**
		 * Se um tipo de campo foi configurado
		 */
		else if ( !empty($options["type"]) ) {
			$inputType = $options["type"];
		}
		
		/**
		 * LABEL
		 *
		 * Se Label não foi especificado
		 *
		 * Não há label se o inputType == hidden
		 */
		if( $inputType != "hidden" ){
			if( empty($options["label"]) ){
				$conteudo.= '<label for="input-'.$fieldName.'">'.$fieldName.'</label>';
			} else {
				$conteudo.= '<label for="input-'.$fieldName.'">'.$options["label"].'</label>';
			}
		}

		/**
		 * Analisa qual é o tipo de campo
		 */
		//if( !empty($options["select"]) ){
//			$inputType = "select";
//		} else if( !empty($options["checkbox"]) ){
//			$inputType = "checkbox";
//		}
		
		/**
		 * Mostra inputs de acordo com o especificado
		 */
		/**
		 * INPUT TEXT
		 */
		if( $inputType == "text" ){
			$conteudo.= '<div class="input_field input_text">';
			$conteudo.= '<input type="text" name="'.$inputName.'" value="'.$inputValue.'" id="input-'.$fieldName.'" class="text" />';
		}
		/*
		 * DATE FIELD
		 */
		else if( $inputType == "date"){
			$options['fieldName'] = $fieldName;
			$conteudo.= $this->date($inputName, $options);
		}
		/**
		 * <TEXTAREA>
		 *
		 * Quando o tipo de dados do DB for text, blog, etc, o campo será
		 * textarea.
		 */
		else if( $inputType == "textarea"){
			$conteudo.= '<div class="input_field input_textarea">';

			if( empty($extraOptions) )
				$extraOptions = array();

			$rows = '';
			$cols = '';
			//if( !array_key_exists("row", $extraOptions) )
				//$rows = 'rows="4"';
			if( !array_key_exists("cols", $extraOptions) )
				$cols = 'cols="20"';

			$conteudo.= '<textarea name="'.$inputName.'" '.$rows.' '.$cols.' id="input-'.$fieldName.'" class="">';
			$conteudo.= $inputValue;//$fieldTextValue;
			$conteudo.= '</textarea>';
		}
		/**
		 * INPUT HIDDEN
		 */
		else if( $inputType == "hidden" ){
			$conteudo.= '<div class="input_field input_hidden">';
			$conteudo.= '<input type="hidden" name="'.$inputName.'" value="'.$inputValue.'" id="input-'.$fieldName.'" />';
		}
		/**
		 * INPUT <SELECT>
		 *
		 * Pega as informações de $options["select"] e cria
		 * um <select> com vários <option></option>
		 */
		else if( $inputType == "select" ){
			$select = $options["select"];
			//pr($select);

			/**
			 * <option> selecionado
			 */
			$selectSelected = array();//$select["selected"];
				/**
				 * Se um valor padrão foi passado
				 */
				if( !empty($inputValue) || $inputValue == 0 )
					$selectSelected = $inputValue;

			/**
			 * Opções a serem mostradas
			 */
			$selectOptions = $select["options"];

			if( !is_array($selectOptions) )
				$selectOptions = array();

			$conteudo.= '<div class="input_field input_select">';
			$conteudo.= '<select name="'.$inputName.'" id="input-'.$fieldName.'">';
			/**
			 * Loop pelo select criando <options>
			 */
			foreach($selectOptions as $chave=>$value){
				/**
				 * Verifica se o <option> atual deve ser selecionado por
				 * padrão
				 */
				if( (!empty($selectSelected) || $selectSelected == 0) AND $selectSelected == $chave ){
					$selectThis = 'selected="true"';
				} else {
					$selectThis = false;
				}
				$conteudo.= '<option '.$selectThis.' value="'.$chave.'">'.$value.'</option>';
			}

			$conteudo.= '</select>';
		} // fim <select>
		/**
		 * INPUT <CHECKBOX>
		 *
		 * Pega as informações de $options["checkbox"] e cria
		 * vários <checkbox>
		 */
		else if( $inputType == "checkbox" ){
			$checkbox = $options["checkbox"];
			
			/**
			 * VALORES ATUAIS PARA EDIÇÃO
			 */
			if( !empty($inputValue) )
				$values = $inputValue;

			/**
			 * Opções a serem mostradas
			 */
			$selectOptions = $checkbox["options"];
			if( !is_array($selectOptions) )
				$selectOptions = array();
			$conteudo.= '<div class="input_field input_checkbox input_'.$fieldName.'">';
			
			/**
			 * Loop para criar checkboxes
			 */
			foreach($selectOptions as $chave=>$value){
				
				/**
				 * Verifica se o valor atual está salvo no DB
				 */
				if( !empty($values)
					AND is_array($values)
					AND in_array($chave, $values) )
				{
					$selectThis = 'checked="checked"';
				} else {
					$selectThis = false;
				}
				$conteudo.= '<input type="hidden" name="'.$inputName.'[]" value="0" />';
				$conteudo.= '<div class="input_checkbox_each"><input type="checkbox" name="'.$inputName.'[]" '.$selectThis.' value="'.$chave.'" /> '.$value.'</div>';
			}

		} // fim checkbox

		else {
			$conteudo.= '<div class="input_field input_text">';
			$conteudo.= '<input type="text" name="'.$inputName.'" value="ERRO NO TIPO DE CAMPO" id="input-'.$fieldName.'">';
		}
		
		if( $after != "" ){
			$conteudo.= '<div class="after">';
			$conteudo.= $after;
			$conteudo.= '</div>';
		}
		$conteudo.= '</div>';


		$conteudo.= '</div>';

		return $conteudo;
	}

	/**
	 * date()
	 *
	 * Gera o código HTML para um campo do tipo 'date'
	 *
	 * @param <string> $inputName
	 * @param <array> $options
	 * @return <string>
	 */
	public function date($inputName, $options){

		/*
		 * Inicializa Conteúdo a ser retornado
		 */
		$c = '';

		$inputValue = empty($options['value']) ? date("Y-m-d") : $options['value'];
		$fieldName = empty($options['fieldName']) ? '' : $options['fieldName'];

		$inputValueDay = date("d", strtotime($inputValue) );
		$inputValueMonth = date("m", strtotime($inputValue) );
		$inputValueYear = date("Y", strtotime($inputValue) );

		$c.= '<div class="input_field input_date">';
		$c.= '<input type="text" name="'.$inputName.'[day]" value="'.$inputValueDay.'" id="input-'.$fieldName.'" class="input_date_day input-'.$fieldName.'-day " maxlength="2" />';
		$c.= '-';
		$c.= '<input type="text" name="'.$inputName.'[month]" value="'.$inputValueMonth.'" id="input-'.$fieldName.'-month" class="input_date_month input-'.$fieldName.'-month" maxlength="2" />';
		$c.= '-';
		$c.= '<input type="text" name="'.$inputName.'[year]" value="'.$inputValueYear.'" id="input-'.$fieldName.'-year" class="input_date_year input-'.$fieldName.'-year" maxlength="4" />';
		ob_start();
		tt('<p>Formato: dia-mês-ano (dd-mm-aaaa).</p><p>Exemplo: 21-04-1987.</p>');
		$conteudo = ob_get_contents();
		ob_end_clean();
		$c.= $conteudo;

		return $c;
	}

	public function end($submitValue = "Enviar", $options = ""){
		$conteudo = '';
		$conteudo.= '<input type="submit" name="formSubmit" value="'.$submitValue.'" class="submit" />';
		$conteudo.= '</form>';
		return $conteudo;
	}

}

?>