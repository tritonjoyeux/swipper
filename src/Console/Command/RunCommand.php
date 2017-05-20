<?php

namespace Fashiongroup\Swiper\Console\Command;

use Fashiongroup\Swiper\Search;
use Fashiongroup\Swiper\Workflow\Workflow;
use Fashiongroup\Swiper\Workflow\Writers\ConsoleWriter;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Swiper [no] swiping')
            ->addArgument('jsonPath', InputArgument::OPTIONAL, 'The json file path')
            ->addOption('agent', null, InputOption::VALUE_OPTIONAL)
            ->addOption('writers', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, null, [])
            ->addOption('terms', null, InputOption::VALUE_OPTIONAL)
            ->addOption('country', null, InputOption::VALUE_OPTIONAL)
            ->addOption('location', null, InputOption::VALUE_OPTIONAL)
            ->addOption('extras', null, InputOption::VALUE_OPTIONAL, 'json formated')
            ->addOption('freshness', null, InputOption::VALUE_OPTIONAL, 'Number of days to fetch', 2);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->pushHandler(new ConsoleHandler($output));

        if ($input->getArgument('jsonPath') !== null) {
            $this->runMultipleSearches($input, $output);
        } else {
            $this->runSingleSearch($input, $output);
        }
    }

    protected function runSingleSearch(InputInterface $input, OutputInterface $output)
    {
        $search = new Search($input->getOption('terms'));

        $search
            ->setCountry($input->getOption('country'))
            ->setLocation($input->getOption('location'))
            ->setAgent($input->getOption('agent'))
            ->setExtras(json_decode($input->getOption('extras'), true))
            ->setFreshness($input->getOption('freshness'));

        $this
            ->createWorkflow($input, $output)
            ->setSearch($search)
            ->run();
    }

    protected function runMultipleSearches(InputInterface $input, OutputInterface $output)
    {
        $jsonPath = $input->getArgument('jsonPath');
        $extrasOption = $input->getOption('extras') ? json_decode($input->getOption('extras'), true) : [];

        $searches = [];

        $searchesData = json_decode(file_get_contents($jsonPath), true);

        foreach ($searchesData['searches'] as $searchData) {

            $searchData = array_merge([
                'country' => '',
                'agent' => null,
                'location' => ''
            ], $searchData);

            $search = new Search($searchData['term']);

            $searches[] = $search
                ->setCountry($searchData['country'])
                ->setLocation($searchData['location'])
                ->setAgent($searchData['agent'])
                ->setExtras(array_merge($extrasOption, $searchData['extras']))
                ->setFreshness($input->getOption('freshness'));
        }

        $this
            ->createWorkflow($input, $output)
            ->setSearches($searches)
            ->run();
    }

    protected function getWriter($writer)
    {
        switch ($writer) {
            case 'api':
                return $this->getContainer()->get('fg_writer');
                break;
        }
    }

    protected function getWriters(InputInterface $input, OutputInterface $output)
    {
        $writers = [];

        if ($output->isVerbose()) {
            $writers[] = new ConsoleWriter($output);
        }

        foreach ($input->getOption('writers') as $writer) {
            $writer = $this->getWriter($writer);

            if ($writer) {
                $writers[] = $writer;
            }
        }

        return $writers;
    }

    /**
     * @return Workflow
     */
    protected function createWorkflow(InputInterface $input, OutputInterface $output)
    {
        $writers = $this->getWriters($input, $output);

        /** @var Workflow $workflow */
        $workflow = $this->getContainer()->get('workflow');

        foreach ($writers as $writer) {
            $workflow->addWriter($writer);
        }

        return $workflow;
    }
}
