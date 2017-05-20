<?php

namespace Fashiongroup\Swiper\Workflow\Writers;

use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;
use Fashiongroup\Swiper\Swiper;
use Fashiongroup\Swiper\Workflow\Writer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleWriter implements Writer
{
    private $increment = 0;

    const FLUSH_INTERVAL = 10;

    /**
     * @var Output
     */
    private $output;

    /**
     * @var Table
     */
    private $table;

    /**
     * ConsoleWriter constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->table = $this->createTable();
    }

    public function write(JobPosting $jobPosting, Search $search)
    {
        $this->table->addRow([
            $jobPosting->getHiringOrganization() ? $jobPosting->getHiringOrganization()->getName() : null,
            $jobPosting->getTitle(),
            (string) $jobPosting->getJobLocation(),
            $jobPosting->getPublishedAt() ? $jobPosting->getPublishedAt()->format(DATE_RFC822) : null
        ]);

        $this->increment++;

        if ($this->increment % self::FLUSH_INTERVAL === 0) {
            $this->flush();
        }
    }

    public function flush()
    {
        $this->table->render();
        $this->table = $this->createTable();
    }

    private function createTable()
    {
        $table = new Table($this->output);

        return $table->setHeaders([
            'HiringOrganization',
            'Title',
            'JobLocation',
            'PublishedAt'
        ]);
    }
}
