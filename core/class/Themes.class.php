<?php
/**
 * Arquivo responsável pelos Temas
 *
 * @package 
 * @name Themes
 * @author Andréia de Oliveira <andreia2008@gmail.com>
 * @version 0.1
 * @since v0.1.8, 11/02/2010
 */
class Themes {
    /**
     *
     * @var bool Se a base de dados existe. Serve para verificação simples.
     */
	public $conexao;

    function __construct($param) {
        $this->conexao = $param['conexao'];
    }

    public function getThemes(){
        $result = ARRAY();

        $i = 0;
        foreach (glob(THEMES_DIR."*", GLOB_ONLYDIR) as $path) {

            if( is_file($path.'/'.THEMES_SCREENSHOT_FILE.'.png') ){
                $extension = 'png';
            } elseif( is_file($path.'/'.THEMES_SCREENSHOT_FILE.'.jpg') ){
                $extension = 'jpg';
            }

            if( !empty($extension) ){
                $result[$i]['path'] = $path;
                $result[$i]['screenshotFile'] = $path.'/'.THEMES_SCREENSHOT_FILE.'.'.$extension;
                $result[$i]['themeName'] = basename($path);
                include($path.'/info.php');
                $result[$i]['name'] = $name;
                $i = $i + 1;
            }
            unset($extension);

        }
        return $result;
    } // end getThemes()

}

?>