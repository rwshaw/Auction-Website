<?php

function console_log($var_to_log) {
    echo '<script>';
    echo 'console.log('.json_encode( $var_to_log ) .')';
    echo '</script>';
}

?>