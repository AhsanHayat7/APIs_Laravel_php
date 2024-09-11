
<?php

use Symfony\Component\CssSelector\Node\FunctionNode;

if(!function_exists('p')){
    function p($data){

        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
