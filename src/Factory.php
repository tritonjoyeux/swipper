<?php

namespace Fashiongroup\Swiper;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;
use Fashiongroup\Swiper\Agents\MinkSessionAdapter;
use Fashiongroup\Swiper\Rss\RssParser;
use Goutte\Client;
use Monolog\Handler\PHPConsoleHandler;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Vinelab\Rss\Parsers\XML;
use Vinelab\Rss\Rss;
use Webmozart\KeyValueStore\JsonFileStore;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class Factory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param $name
     * @param $logsDir
     * @return Logger
     */
    public static function createLogger($name, $logsDir)
    {
        $log = new Logger($name);
        $log->pushHandler(new RotatingFileHandler($logsDir . '/' . $name  . '.log', 0, Logger::INFO));

        return $log;
    }

    /**
     * @param $name
     * @param $storesDir
     * @return JsonFileStore
     */
    public static function createStore($name, $storesDir)
    {
        return new JsonFileStore($storesDir . '/' . $name . '.json');
    }

    /**
     * @return Swiper
     */
    public static function create()
    {
        $factory = new static();
        $container = $factory->getContainer();

        return $container->get('swiper')->setContainer($container);
    }

    /**
     * @return \Fashiongroup\Swiper\Agents\Session
     */
    public static function createSession()
    {
        return new MinkSessionAdapter(new Session(new GoutteDriver(new Client())));
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = $this->createContainer();
        }

        return $this->container;
    }

    /**
     * @return ContainerBuilder
     */
    public function createContainer()
    {
        $container = new ContainerBuilder();

        $dumpedContainerFile = $this->getDumpedContainerFilePath();

        if (file_exists($dumpedContainerFile)) {
            require_once $dumpedContainerFile;
            return new \ProjectServiceContainer();
        }

        $baseDir = __DIR__ . '/..';

        $container->setParameter('base_dir', $baseDir);
        $container->setParameter('logs_dir', $baseDir . '/logs');
        $container->setParameter('cache_dir', $baseDir . '/cache');
        $container->setParameter('data_dir', $baseDir . '/data');

        $loader = new YamlFileLoader($container, new FileLocator([$baseDir . '/src/Di/config', $baseDir . '/config']));
        $loader->load('services.yml');
        $loader->load('parameters.yml');

        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents($dumpedContainerFile, $dumper->dump());

        return $container;
    }

    public function getDumpedContainerFilePath()
    {
        return __DIR__ . '/../cache/container.php';
    }

    /**
     * @return \Fashiongroup\Swiper\Rss\RssParser
     */
    public static function createRss($session)
    {
        return new RssParser($session);
    }
}
