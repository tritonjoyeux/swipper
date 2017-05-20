<?php

namespace Fashiongroup\Swiper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearData extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('clear:data')
            ->setAliases(['cd'])
            ->setDescription('Clear data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->getContainer()->getParameter('data_dir') . '/swiper.json';

        if (file_exists($file)) {
            unlink($file);
        }

        $output->writeln('Data cleared');
    }
}
