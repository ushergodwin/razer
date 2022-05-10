<?php
/**
 * Database Configurations
 */

$config = [
    "DB_HOST"   => empty(env("DB_HOST")) ? "localhost" : env("DB_HOST"),
    "DB_USER"     => empty(env("DB_USER")) ? "root" : env("DB_USER"),
    "DB_PASSWORD"      => empty(env("DB_PASSWORD")) ? '' : env("DB_PASSWORD"),
    "DB_NAME"       => empty(env("DB_NAME")) ? '' : env("DB_NAME"),
    "PORT"          => empty(env("DB_PORT")) ? 3306 : (int)env("DB_PORT")
];

return $config;