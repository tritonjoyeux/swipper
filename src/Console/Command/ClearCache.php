<?php

namespace Fashiongroup\Swiper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('clear:cache')
            ->setAliases(['cc'])
            ->setDescription('Clear cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->getContainer()->getParameter('cache_dir') . '/container.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $output->writeln('Cache cleared');
    }
}
