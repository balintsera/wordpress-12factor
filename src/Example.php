<?php

namespace Evista;

class Example
{
    public function __construct() {
        echo 'Example constructor';
        if (function_exists('add_action')) {
            add_action('init', function () {
                // var_dump('init hook example');
            });
        }
    }
}