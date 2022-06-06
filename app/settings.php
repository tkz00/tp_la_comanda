<?php

    use DI\Container;

    return function(Container $container) {
        $container->set('settings', function() {
            return [
                'name' => 'La Comanda',
                'displayErrorDetails' => true,
                'logErrorDetails' => true,
                'logErrors' => true
            ];
        });
    };

?>