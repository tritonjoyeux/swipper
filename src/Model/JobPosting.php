<?php

namespace Fashiongroup\Swiper\Model;

class JobPosting
{
    private $title;

    private $jobLocation;

    private $summary;

    /**
     * @var Organization
     */
    private $hiringOrganization;

    private $occupationalCategory;

    private $industry;

    private $publishedAt;

    private $description;

    private $url;

    private $source;

    private $contractType;

    private $employmentType;

    private $baseSalary;

    private $contact;

    private $experience;

    private $skills;

    private $complement;

    /**
     * @var \DateTime
     */
    private $swipeAt;

    /**
     * JobPosting constructor.
     */
    public function __construct()
    {
        $this->swipeAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return JobPosting
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|Address
     */
    public function getJobLocation()
    {
        return $this->jobLocation;
    }

    /**
     * @param string|Address $jobLocation
     * @return JobPosting
     */
    public function setJobLocation($jobLocation)
    {
        $this->jobLocation = ($jobLocation instanceof Address) ? $jobLocation->toArray() : $jobLocation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     * @return JobPosting
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return Organization
     */
    public function getHiringOrganization()
    {
        return $this->hiringOrganization;
    }

    /**
     * @param Organization $hiringOrganization
     * @return JobPosting
     */
    public function setHiringOrganization(Organization $hiringOrganization)
    {
        $this->hiringOrganization = $hiringOrganization;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOccupationalCategory()
    {
        return $this->occupationalCategory;
    }

    /**
     * @param mixed $occupationalCategory
     * @return JobPosting
     */
    public function setOccupationalCategory($occupationalCategory)
    {
        $this->occupationalCategory = $occupationalCategory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param mixed $industry
     * @return JobPosting
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return JobPosting
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return JobPosting
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return JobPosting
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     * @return JobPosting
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContractType()
    {
        return $this->contractType;
    }


    /**
     * @param mixed $contractType
     * @return JobPosting
     */
    public function setContractType($contractType)
    {
        ContractTypeEnum::assertExists($contractType);

        $this->contractType = $contractType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * @param mixed $employmentType
     * @return JobPosting
     */
    public function setEmploymentType($employmentType)
    {
        EmploymentTypeEnum::assertExists($employmentType);

        $this->employmentType = $employmentType;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSwipeAt()
    {
        return $this->swipeAt;
    }

    /**
     * @param \DateTime $swipeAt
     * @return $this
     */
    public function setSwipeAt(\DateTime $swipeAt)
    {
        $this->swipeAt = $swipeAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseSalary()
    {
        return $this->baseSalary;
    }

    /**
     * @param mixed $baseSalary
     * @return JobPosting
     */
    public function setBaseSalary($baseSalary)
    {
        $this->baseSalary = $baseSalary;

        return $this;
    }

    protected function createHash()
    {
        $hashParts = [];

        if ($this->getTitle()) {
            $hashParts[] = $this->getTitle();
        }

        if ($this->getHiringOrganization()->getName()) {
            $hashParts[] = $this->getHiringOrganization()->getName();
        }

        if ($this->getJobLocation()) {
            $hashParts[] = $this->getJobLocation();
        }

        if ($this->getEmploymentType()) {
            $hashParts[] = $this->getEmploymentType();
        }

        if ($this->getBaseSalary()) {
            $hashParts[] = $this->getBaseSalary();
        }

        return sha1(join(" - ", $hashParts));
    }


    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->createHash();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'jobLocation' => $this->jobLocation,
            'summary' => $this->summary,
            'hiringOrganization' => $this->hiringOrganization ? $this->hiringOrganization->toArray() : null,
            'publishedAt' => $this->publishedAt ? $this->publishedAt->format('U') : null,
            'description' => $this->description,
            'url' => $this->url,
            'hash' => $this->getHash(),
            'source' => $this->source,
            'swipedAt' => $this->swipeAt->format('U'),
            'occupationalCategory' => $this->getOccupationalCategory(),
            'industry' => $this->getIndustry(),
            'skills' => $this->getSkills(),
            'contractType' => $this->getContractType(),
            'employmentType' => $this->getEmploymentType(),
            'contact' => $this->contact ? $this->contact->toArray() : null,
            'baseSalary' => $this->baseSalary,
            'complement' => $this->getComplement()
        ];
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     * @return JobPosting
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * @param mixed $experience
     * @return $this
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param mixed $skills
     * @return $this
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param mixed $complement
     * @return $this
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;
        return $this;
    }
}
