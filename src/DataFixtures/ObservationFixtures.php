<?php

namespace App\DataFixtures;

use App\Entity\Observation;
use Doctrine\Persistence\ObjectManager;

class ObservationFixtures extends BaseFixture
{
    public function __construct() {

    }

    protected function loadData(ObjectManager $manager)
    {
        $year = 2021;
        $this->createMany(12, 'haz_observations_2021', function ($i) use ($manager, $year) {
            $observation = new Observation();
            $observation->setYear($year);
            $observation->setMonth($i+1);
            $observation->setValue($this->faker->numberBetween(0, 100));
            $observation->setIndicator($this->getRandomReference('haz_indicators'));

            return $observation;
        });

        $this->createMany(12, 'informatika_indicators_2021', function ($i) use ($manager, $year) {
            $observation = new Observation();
            $observation->setYear($year);
            $observation->setMonth($i+1);
            $observation->setValue($this->faker->numberBetween(0, 100));
            $observation->setIndicator($this->getRandomReference('informatika_indicators'));

            return $observation;
        });

        $year = 2022;
        $this->createMany(12, 'haz_observations_2022', function ($i) use ($manager, $year) {
            $observation = new Observation();
            $observation->setYear($year);
            $observation->setMonth($i+1);
            $observation->setValue($this->faker->numberBetween(0, 100));
            $observation->setIndicator($this->getRandomReference('haz_indicators'));

            return $observation;
        });

        $this->createMany(12, 'informatika_indicators_2022', function ($i) use ($manager, $year) {
            $observation = new Observation();
            $observation->setYear($year);
            $observation->setMonth($i+1);
            $observation->setValue($this->faker->numberBetween(0, 100));
            $observation->setIndicator($this->getRandomReference('informatika_indicators'));

            return $observation;
        });
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            IndicatorFixtures::class,
        );
    }

}
