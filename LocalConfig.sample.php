<?php
class LocalConfig
{
    public function __construct()
    {
        $_ENV["APPLICATION_TYPE"] = 'web';
        $_ENV["ENVIRONMENT"] = 'development';
        //0 in prod - 1-3 for dev;
        $_ENV["F3_DEBUG"] = '3';
        $_ENV["BASE_URL"] = 'http://my.domain.com';
        $_ENV["BASE_PATH"] = '/path/to/public_html/';
        $_ENV["DEVELOPER_TEST_EMAIL"] = 'email@emial.com';
        //Database 
        $_ENV["DB_HOSTNAME"] = 'localhost';
        $_ENV["DB_DATABASE"] = 'datbase name';
        $_ENV["DB_USERNAME"] = 'username';
        $_ENV["DB_PASSWORD"] = 'password';
    }
}