<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Test\Controller\Test' => 'Test\Controller\TestController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'test' => __DIR__ . '/../view',
        ),
    ),
);