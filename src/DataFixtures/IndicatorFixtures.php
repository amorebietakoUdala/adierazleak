<?php

namespace App\DataFixtures;

use App\Entity\Indicator;
use Doctrine\Persistence\ObjectManager;

class IndicatorFixtures extends BaseFixture
{
    public function __construct() {

    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(5, 'haz_indicators', function ($i) use ($manager) {
            $indicator = new Indicator();
            $indicator->setDescriptionEs(sprintf("Indicador SAC %d", $i));
            $indicator->setDescriptionEu(sprintf("HAZ adierazlea %d", $i));
            $indicator->setRequiredRoles(["ROLE_HAZ"]);

            return $indicator;
        });

        $this->createMany(3, 'informatika_indicators', function ($i) {
         $indicator = new Indicator();
         $indicator->setDescriptionEs(sprintf("Indicador informática %d", $i));
         $indicator->setDescriptionEu(sprintf("Informatika adierazlea %d", $i));
         $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);

         return $indicator;
     });

        $manager->flush();
    }
}
