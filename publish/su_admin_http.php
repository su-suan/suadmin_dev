<?php
return [
    'header'    => [
        'Access-Control-Allow-Origin'       => '*',
        'Access-Control-Allow-Headers'      => 'Accept-Language,Authorization,Authentication,Keep-Alive,User-Agent,Cache-Control,Content-Type',
        'Access-Control-Allow-Methods'      => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Max-Age'            =>  '1728000',
        'Access-Control-Allow-Credentials'  => 'true',
        'Access-Control-Expose-Headers'     => 'Server'
    ],
    // token名称
    'token' => 'Authentication',
    'Server' => 'SuAdmin',
];