<?php

namespace Queue\Application;

use Perfumer\Package\Framework\Bundle\ConsoleBundle;
use Perfumer\Package\Framework\Bundle\HttpBundle;
use Queue\Bundle\SharedBundle;

class Application extends \Perfumer\Framework\Application\Application
{
    protected function before()
    {
        date_default_timezone_set('UTC');

        define('ROOT_DIR', __DIR__ . '/../../');
        define('TMP_DIR', ROOT_DIR . 'tmp/');
        define('VENDOR_DIR', ROOT_DIR . 'vendor/');
        define('WEB_DIR', ROOT_DIR . 'web/');
    }

    protected function after()
    {
    }

    protected function configure()
    {
        $this->addBundle(new HttpBundle(),   self::HTTP);
        $this->addBundle(new ConsoleBundle(),self::CLI);
        $this->addBundle(new SharedBundle());
        $this->addBundle(new \Queue\Bundle\HttpBundle(), self::HTTP);
        $this->addBundle(new \Queue\Bundle\CliBundle(), self::CLI);
    }
}
