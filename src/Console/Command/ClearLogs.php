<?php

namespace Fashiongroup\Swiper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearLogs extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('clear:logs')
            ->setAliases(['clo'])
            ->setDescription('Clear logs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->getContainer()->getParameter('logs_dir') . '/swiper.log';

        if (file_exists($file)) {
            unlink($file);
        }

        $output->writeln('Logs cleared');
    }
}
