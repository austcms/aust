<?php
function __autoload($className) {
    require_once 'core/class/'.$className.'.class.php';
}
?>
