<?php

return [
    'typ' => 'JWT',
    'alg' => 'HS256',
    'expire_time' => env('JWT_EXPIRATION'),
    'key' => 'abC123!',
];
