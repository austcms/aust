<?php
function __autoload($className) {
    if( is_file('core/class/'.$className.'.php') )
        require_once 'core/class/'.$className.'.php';
    else
        require_once 'core/class/'.$className.'.class.php';

}
?>
