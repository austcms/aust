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

    function __construct() {
		$this->conexao = Connection::getInstance();
    }

    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Themes;
        }

        return $instance[0];

    }

    /**
     * getThemes()
     *
     * Retorna os temas atuais
     *
     * @return <array>
     */
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

    public function setTheme($params){

        $userId = $params['userId'];
        $themeName = $params['themeName'];

        $sql = "DELETE
                FROM config
                WHERE
                    tipo='Themes' AND
                    local='".$userId."' AND
                    propriedade='current_theme'
                ";
        Connection::getInstance()->exec($sql);

        $sql = "INSERT INTO
                    config
                    (tipo, local, nome, propriedade, valor, autor)
                VALUES
                    ('Themes', '".$userId."', 'Tema Atual', 'current_theme', '".$themeName."', '".$userId."')
                ";
        Connection::getInstance()->exec($sql);

        return true;

    }

    public function currentTheme($userId){
        $sql = "SELECT valor
                FROM config
                WHERE
                    tipo='Themes' AND
                    local='".$userId."' AND
                    propriedade='current_theme'
                LIMIT 1
                ";

        $tmp = Connection::getInstance()->query($sql);

        if(empty($tmp[0]['valor']))
            return Registry::read('defaultTheme');

        return $tmp[0]['valor'];
    }
}

?>