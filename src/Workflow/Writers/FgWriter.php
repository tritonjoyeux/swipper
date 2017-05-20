<?php

namespace Fashiongroup\Swiper\Workflow\Writers;

use Fashiongroup\Swiper\LoggerAwareInterface;
use Fashiongroup\Swiper\LoggerAwareTrait;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;
use Fashiongroup\Swiper\Workflow\Writer;
use GuzzleHttp\Client;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Helper\Table;

class FgWriter implements Writer, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Client
     */
    private $client;

    /**
     * ConsoleWriter constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->logger = new NullLogger();
    }

    public function write(JobPosting $jobPosting, Search $search)
    {
        $queryString = http_build_query([
            'domaine' => $search->getExtra('domaine'),
            'code_pays' => $search->getExtra('code_pays'),
            'lang' => $search->getExtra('lang'),
            'sector' => $search->getExtra(('sector'))
        ]);

        $url = '/v1/jobs';

        if ($queryString) {
            $url .= '?' . $queryString;
        }

        $this->logger->debug('Write to api');
        try {
            $this->client->request('POST', $url, ['json' => $jobPosting->toArray()]);
        } catch (\Exception $e) {
            throw new WriterException($e->getMessage(), null, $e);
        }
    }

    public function flush()
    {
        return;
    }
}
