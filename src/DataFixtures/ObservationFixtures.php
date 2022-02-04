<?php

namespace App\DataFixtures;

use App\Entity\Indicator;
use App\Entity\Observation;
use Doctrine\Persistence\ObjectManager;

class ObservationFixtures extends BaseFixture
{
    public function __construct() {

    }

    protected function loadData(ObjectManager $manager)
    {
        for ($year=2021;$year<=2022; $year++){
            $indicators = $manager->getRepository(Indicator::class)->findAll();
            foreach ($indicators as $indicator) {
                $this->createMany(12, "haz_observations".$year."_".$indicator, function ($i) use ($manager, $year, $indicator) {
                    $observation = new Observation();
                    $observation->setYear($year);
                    $observation->setMonth($i+1);
                    $observation->setValue($this->faker->numberBetween(0, 100));
                    $observation->setIndicator($indicator);
        
                    return $observation;
                });
            }
        }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            IndicatorFixtures::class,
        );
    }

}
