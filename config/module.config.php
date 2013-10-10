<?php
/**
 * By: Codejit Solutions
 * Copyright by Codejit Solutions
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'JitPoll\Controller\Index' => 'JitPoll\Controller\IndexController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'jitpoll' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/unbpoll[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'JitPoll\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'jit-poll' => __DIR__ . '/../view',            
        )
    ),
    'module_layouts' => array(
        'JitPoll' => 'layout/unbadmin',
    )
);