<?php

namespace spec\Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Agents\Indeed\DateParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateParser::class);
    }

    function it_should_parse_tr_dates() {

        $this->setQuantityTypeMap([
            'gün' => 'day'
        ]);

        $this->setPattern('/(\d+)\+? (\w+) önce$/u');

        $expected = new \DateTime('now -5 days');

        $this->parse('5 gün önce')->shouldMatchDate($expected->format('Y-m-d H:i:s'));
    }

    function it_should_parse_fr_dates() {

        $this->setQuantityTypeMap([
            'jour' => 'day'
        ]);

        $this->setPattern('/il y a (\d+)\+? (\w+)/u');

        $expected = new \DateTime('now -30 days');

        $this->parse('il y a 30+ jours')->shouldMatchDate($expected->format('Y-m-d H:i:s'));
    }


    public function getMatchers()
    {
        return [
            'matchDate' => function ($subject, $date) {
                if (!$subject instanceof \DateTime) {
                    return false;
                }

                return $subject->format('Y-m-d H:i:s') === $date;
            },
        ];
    }
}
