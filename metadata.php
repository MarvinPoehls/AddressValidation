<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'           => 'adressvalidation',
    'title'  => [
        'de' => 'Adressvalidierung',
        'en' => 'Adress validation',
    ],
    'description'  => [
        'de' => 'Modul welches prüft ob die im Checkout eingegebene PLZ und Stadt zu einander passen.',
        'en' => 'Module which checks whether the zip code and city entered in the checkout match.',
    ],
    'thumbnail'    => '',
    'version'      => '1.0',
    'author'       => 'Marvin Poehls',
    'url'          => 'https://www.fatchip.de/',
    'email'        => 'marvin.poehls@fatchip.de',
    'extend'       => [
        \OxidEsales\Eshop\Application\Controller\Admin\ModuleConfiguration::class => \MarvinPoehls\AdressValidation\Controller\ModuleConfiguration::class,
    ],
    'blocks' => [
        [
            'template' => 'module_config.tpl',
            'block' => 'admin_module_config_var',
            'file' => 'adress_validator_module_config_var.tpl'
        ],
    ],
    'settings' => [],
    'events' => [
        'onActivate' => 'MarvinPoehls\AdressValidation\Core\Events\Setup::onActivate',
    ]
];
