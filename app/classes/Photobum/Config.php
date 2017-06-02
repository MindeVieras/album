<?php

namespace Photobum;

use DB\SQL;

class Config
{
    public static function bootstrap($router = 'web')
    {

        date_default_timezone_set('UTC');
        ini_set('session.gc_maxlifetime', 7200);
        if (!array_key_exists('EB_ROOT', $_ENV) && !array_key_exists('UPSTART_JOB', $_ENV) && !array_key_exists('EC2_HOME', $_ENV)) {

            require(sprintf('%s/LocalConfig.php', dirname(dirname(dirname(__DIR__)))));
            new \LocalConfig();

        }
        $f3 = \Base::instance();
        $f3->set('AUTOLOAD', 'app/classes/');

        $f3->set('DEBUG', Config::get('F3_DEBUG'));
        $dbconStr = sprintf(
            'mysql:host=%s;port=3306;dbname=%s',
            Config::get('DB_HOSTNAME'),
            Config::get('DB_DATABASE')
        );

        $dbconStr = str_replace('"', '', $dbconStr);
        $dboptions = [];
        if (!array_key_exists('EB_ROOT', $_ENV)) {
            $dboptions = array(
                \PDO::ATTR_PERSISTENT => true,  // we want to use persistent connections
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            );
        }
        $f3->set(
            'DB',
            new SQL(
                $dbconStr,
                Config::get('DB_USERNAME'),
                Config::get('DB_PASSWORD'),
                $dboptions
            )
        );

        if (Config::get('APPLICATION_TYPE') == 'web') {
            new Session();
        }

        if (self::get('ENVIRONMENT') == 'development') {
            \Kint::enabled(true);
        } else {
            \Kint::enabled(false);
        }

        if ($f3->get('SERVER.HTTP_HOST')) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = self::get('BASE_URL');
            $hostarray = explode('/', $host);
            $host = array_pop($hostarray);
        }
        
        DEFINE('HOST_TYPE', explode('.', $host)[0]);

        if (substr(self::get('BASE_PATH'), -1) != '/') {
            die('BASE_PATH must end with a trailing slash');
        }

        if (PHP_SAPI === 'cli') {
            DEFINE('EOL', PHP_EOL);
        } else {
            DEFINE('EOL', '<br/>');
        }

    }

    /**
     * @param $var
     *
     * @return int
     * @throws \Exception
     */

    public static function get($var)
    {
        if (array_key_exists($var, $_ENV)) {
            return $_ENV[$var];
        } elseif (getenv($var)) {
            return getenv($var);
        } else {
            throw new \Exception('Unknown variable: '.$var);
        }
    }

    // This function CREATES a AWS credential provider.
    public static function get_aws_creds()
    {
        // This function IS the credential provider.
        return function () {
            // Use credentials from environment variables, if available
            $key = self::get('AWS_ACCESS_KEY_ID');
            $secret = self::get('AWS_SECRET_KEY');
            if ($key && $secret) {
                return Promise\promise_for(
                    new Credentials($key, $secret)
                );
            }

            $msg = 'Could not find environment variable '
                . 'credentials in AWS_ACCESS_KEY_ID/AWS_SECRET_KEY';
            return new RejectedPromise(new CredentialsException($msg));
        };
    }

}
