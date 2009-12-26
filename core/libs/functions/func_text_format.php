<?php
	/*
	ÃNDICE DAS FUNÃÃES
	#######
	1. function write_content($str)
		DescriÃ§Ã£o = FunÃ§Ã£o que formata a string $str, substituindo termos cadastrados pelo usuÃ¡rio
					por termos propÃ­cios para visualizaÃ§Ã£o do conteÃºdo na tela do usuÃ¡rio.
			$str = string que serÃ¡ formatada.
	*/



	function write_content($str){
		$str = str_replace("\n", "<br />", $str);
		$str = str_replace('<titulo>', '<h2 style="margin-bottom: 0px; display: inline;">', $str);
		$str = str_replace("</titulo>", "</h2>", $str);
		echo $str;
	}

/**
 * tooltip()
 *
 * Gera código HTML necessário para criação dinâmica de Tooltips
 *
 * @param string $str
 */
function tooltip($str = ''){
    if( !empty($str) ){
        ?>
        <div class="hint">
            <a href="#" title="Saiba mais"></a>

            <div class="tooltip">
                <div class="top"></div>
                <div class="text"><p><?php echo $str ?></p></div>
                <div class="bottom"></div>
            </div>
        </div>
        <?php
        return true;
    }
}// fim tooltip()

/**
 * tt()
 *
 * ALIAS PARA TOOLTIP()
 *
 * @param string $str
 * @return bool
 */
function tt($str = ''){
    return tooltip($str);
}
?>