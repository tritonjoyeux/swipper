<?php

namespace Fashiongroup\Swiper\Filters;

use Fashiongroup\Swiper\LoggerAwareInterface;
use Fashiongroup\Swiper\LoggerAwareTrait;
use Fashiongroup\Swiper\Model\JobPosting;
use Psr\Log\NullLogger;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class AlreadySwipedFilter implements FilterInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const IGNORE_SWIPE_OLDER_THAN_IN_DAYS = 30;

    /**
     * @var KeyValueStore
     */
    private $store;

    /**
     * AlreadySwipedFilter constructor.
     * @param KeyValueStore $store
     */
    public function __construct(KeyValueStore $store)
    {
        $this->logger = new NullLogger();
        $this->store = $store;
    }

    public function accept(JobPosting $jobPosting)
    {
        $isAlreadySwiped = $this->isJobPostingAlreadySwiped($this->store, $jobPosting);

        $date = new \DateTime();
        $date->setTimestamp($this->getJobPostingSwipeDate($this->store, $jobPosting));

        if ($isAlreadySwiped) {
            $this->logger->info(sprintf('jobPosting “%s” [%s] from “%s” already swiped at %s [%s]',
                $jobPosting->getTitle(),
                $jobPosting->getHiringOrganization()->getName(),
                $jobPosting->getSource(),
                $date->format(DATE_RFC822),
                $jobPosting->getHash()
            ));
        } else {
            $this->markJobPostingAsSwiped($this->store, $jobPosting);
        }

        return !$isAlreadySwiped;
    }

    /**
     * @param KeyValueStore $store
     * @param JobPosting $jobPosting
     * @return bool
     */
    public function isJobPostingAlreadySwiped(KeyValueStore $store, JobPosting $jobPosting)
    {
        $date = $this->getJobPostingSwipeDate($store, $jobPosting);

        if (!$date) {
            return false;
        }

        $dateTime = new \DateTime();
        $dateTime = $dateTime->setTimestamp($date);

        // consider job as new if last swipe is older than self::IGNORE_SWIPE_OLDER_THAN_IN_DAYS days
        return $dateTime > new \DateTime(sprintf('now -%d days', self::IGNORE_SWIPE_OLDER_THAN_IN_DAYS));
    }

    /**
     * @param KeyValueStore $store
     * @param JobPosting $jobPosting
     * @return \DateTime|null
     */
    public function getJobPostingSwipeDate(KeyValueStore $store, JobPosting $jobPosting)
    {
        return $store->get($jobPosting->getHash());
    }

    /**
     * @param KeyValueStore $store
     * @param JobPosting $jobPosting
     * @return mixed
     */
    public function markJobPostingAsSwiped(KeyValueStore $store, JobPosting $jobPosting)
    {
        return $store->set($jobPosting->getHash(), time());
    }
}
