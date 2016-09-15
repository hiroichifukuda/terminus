<?php

namespace Pantheon\Terminus;

use Consolidation\AnnotatedCommand\CommandFileDiscovery;
use League\Container\Container;
use Robo\Runner as RoboRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VCR\VCR;

class Runner
{
    /**
     * @var \Robo\Runner
     */
    private $runner;
    /**
     * @var string[]
     */
    private $commands = [];
    /**
     * @var Config
     */
    private $config;

    /**
     * Object constructor
     *
     * @param Container $container Container The dependency injection container
     */
    public function __construct(Container $container = null)
    {
        $commands_directory = __DIR__ . '/Commands';
        $top_namespace = 'Pantheon\Terminus\Commands';
        $this->config = $container->get('config');
        $this->commands = $this->getCommands(['path' => $commands_directory, 'namespace' => $top_namespace,]);
        $this->commands[] = 'Pantheon\\Terminus\\Authorizer';
        $this->runner = new RoboRunner();
        $this->runner->setContainer($container);
    }

    /**
     * Runs the instantiated Terminus application
     *
     * @param InputInterface  $input  An input object to run the application with
     * @param OutputInterface $output An output object to run the application with
     * @return integer $status_code The exiting status code of the application
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        if (!empty($cassette = $this->config->get('vcr_cassette')) && !empty($mode = $this->config->get('vcr_mode'))) {
            $this->startVCR(array_merge(compact('cassette'), compact('mode')));
        }
        $status_code = $this->runner->run($input, $output, null, $this->commands);
        if (!empty($cassette) && !empty($mode)) {
            $this->stopVCR();
        }
        return $status_code;
    }

    /**
     * Discovers command classes using CommandFileDiscovery
     *
     * @param string[] $options Elements as follow
     *        string path      The full path to the directory to search for commands
     *        string namespace The full namespace associated with given the command directory
     * @return TerminusCommand[] An array of TerminusCommand instances
     */
    private function getCommands(array $options = ['path' => null, 'namespace' => null,])
    {
        $discovery = new CommandFileDiscovery();
        $discovery->setSearchPattern('*Command.php')->setSearchLocations([]);
        return $discovery->discover($options['path'], $options['namespace']);
    }

    /**
     * Starts and configures PHP-VCR
     *
     * @param string[] $options Elements as follow:
     *        string cassette The name of the fixture in tests/fixtures to record or run in this feature test run
     *        string mode     Mode in which to run PHP-VCR (options are none, once, strict, and new_episodes)
     * @return void
     */
    private function startVCR(array $options = ['cassette' => 'tmp', 'mode' => 'none',])
    {
        VCR::configure()->enableRequestMatchers(['method', 'url', 'body',]);
        VCR::configure()->setMode($options['mode']);
        VCR::turnOn();
        VCR::insertCassette($options['cassette']);
    }

    /**
     * Stops PHP-VCR's recording and playback
     *
     * @return void
     */
    private function stopVCR()
    {
        VCR::eject();
        VCR::turnOff();
    }
}
