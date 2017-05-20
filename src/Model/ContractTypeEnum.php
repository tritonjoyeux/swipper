<?php

namespace Fashiongroup\Swiper\Model;

use CommerceGuys\Enum\AbstractEnum;

class ContractTypeEnum extends AbstractEnum
{
    const ROLLING_CONTRACT = 'rolling_contract';
    const FIXED_TERM_CONTRACT = 'fixed_term_contract';
    const INTERIM = 'interim';
    const INDEPENDENT = 'independent';
    const INTERNSHIP = 'internship';
    const WORK_STUDY = 'work_study';
}
