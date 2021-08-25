<?php
/**
 * 
 */

$config = [
    "SERVER_NAME" => empty(env("DB_HOST")) ? "localhost" : env("DB_HOST"),
    "USER_NAME" => empty(env("DB_USER")) ? "root" : env("DB_USER"),
    "PASSWORD" => empty(env("DB_PASSWORD")) ? '' : env("DB_PASSWORD"),
    "DB_NAME" => empty(env("DB_NAME")) ? 'loadshedding' : env("DB_NAME"),
    "PORT" => empty(env("PORT")) ? 3306 : (int)env("DB_NAME")
];

return $config;