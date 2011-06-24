<?php
/**
 * Controller principal deste módulo
 *
 * @package ModController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */

class ModsSetup extends ModActionController
{

    function __construct($param){
        $param['controllerName'] = 'setup';
        $param['action'] = $param['action'];
        parent::__construct($param);
    }

    
}
?>