<?php
/**
 * HELPER
 *
 * Form
 *
 * Contém gerador de elementos HTML automáticos
 *
 * @package Helpers
 * @name Form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
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
        $controller = (empty($options["controller"])) ? 'conteudo' : $options["controller"];
        /**
         * Action
         *
         * O action padrão para salvar um formulário é 'save'
         */
        $action = (empty($options["action"])) ? 'save' : $options["action"];

        $conteudo.= '<form method="post" action="adm_main.php?section='.$controller.'&action='.$action.'" class="formHelper">';

        /**
         * INPUTS HIDDEN
         */
        if( !empty($options["hidden"]) ){
            foreach( $options["hidden"] as $chave=>$valor){
                if( is_string($valor) ){
                    $conteudo.= $valor;
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

        $conteudo.= '<div class="input">';


        /**
         * VALUE
         *
         * Se value não foi especificado
         */
        if( !empty($options["value"]) ){
            $value = $options["value"];
        } else {
            $value = "";
        }


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
//            $inputType = "select";
//        } else if( !empty($options["checkbox"]) ){
//            $inputType = "checkbox";
//        }
        
        /**
         * Mostra inputs de acordo com o especificado
         */
        /**
         * INPUT TEXT
         */
        if( $inputType == "text" ){
            $conteudo.= '<div class="input_field input_text">';
            $conteudo.= '<input type="text" name="'.$inputName.'" value="'.$value.'" id="input-'.$fieldName.'" />';
        }
        /**
         * INPUT HIDDEN
         */
        else if( $inputType == "hidden" ){
            $conteudo.= '<div class="input_field input_hidden">';
            $conteudo.= '<input type="hidden" name="'.$inputName.'" value="'.$value.'" id="input-'.$fieldName.'" />';
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
            $selectSelected = $select["selected"];
                /**
                 * Se um valor padrão foi passado
                 */
                if( !empty($value) )
                $selectSelected = $value;

            /**
             * Opções a serem mostradas
             */
            $selectOptions = $select["options"];
            $conteudo.= '<div class="input_field input_select">';
            $conteudo.= '<select name="'.$inputName.'" id="input-'.$fieldName.'">';
            /**
             * Loop pelo select criando <options>
             */
            foreach($selectOptions as $chave=>$valor){
                /**
                 * Verifica se o <option> atual deve ser selecionado por
                 * padrão
                 */
                if( !empty($selectSelected) AND $selectSelected == $chave ){
                    $selectThis = 'selected="true"';
                } else {
                    $selectThis = false;
                }
                $conteudo.= '<option '.$selectThis.' value="'.$chave.'">'.$valor.'</option>';
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
            $select = $options["checkbox"];
            //pr($select);

            /**
             * <option> selecionado
             */
            if( empty($select["checked"]) )
                $selectSelected = array();
            else
                $selectSelected = $select["checked"];
                /**
                 * Se um valor padrão foi passado
                 */
                if( !empty($value) )
                $selectSelected = $value;

            /**
             * Opções a serem mostradas
             */
            $selectOptions = $select["options"];
            $conteudo.= '<div class="input_field input_checkbox input_'.$fieldName.'">';
            //$conteudo.= '<input type="checkbox" name="'.$inputName.'" />';
            /**
             * Loop pelo select criando <options>
             */
            foreach($selectOptions as $chave=>$valor){
                /**
                 * Verifica se o <option> atual deve ser selecionado por
                 * padrão
                 */
                if( !empty($selectSelected) AND in_array($chave, $selectSelected) ){
                    $selectThis = 'checked="checked"';
                } else {
                    $selectThis = false;
                }
                $conteudo.= '<div class="input_checkbox_each"><input type="checkbox" name="'.$inputName.'[]" '.$selectThis.' value="'.$chave.'" />'.$valor.'</div>';
            }

            $conteudo.= '</select>';
        } // fim checkbox

        else {
            $conteudo.= '<div class="input_field input_text">';
            $conteudo.= '<input type="text" name="'.$inputName.'" value="ERRO NO TIPO DE CAMPO" id="input-'.$fieldName.'">';
        }
        $conteudo.= '</div>';


        $conteudo.= '</div>';

        return $conteudo;
    }

    public function end($submitValue = "Enviar", $options = ""){
        $conteudo = '';
        $conteudo.= '<input type="submit" name="formSubmit" value="'.$submitValue.'" class="submit" />';
        $conteudo.= '</form>';
        return $conteudo;
    }

}

?>