<?php

return array(

    'fetch' => PDO::FETCH_CLASS,

    'default' => 'mysql',


    'connections' => array(



        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'dnsmonitor',
            'username'  => 'root',
            'password'  => 'Ties That Bind',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),



    ),



);
