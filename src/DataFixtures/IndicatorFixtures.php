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
        $indicator = new Indicator();
        $indicator->setId(1);
        $indicator->setDescriptionEs('Entradas telemáticas - Mensual');
        $indicator->setDescriptionEu('Sarrera telemátikoak - Hilero');
        $indicator->setRequiredRoles(["ROLE_HAZ"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(2);
        $indicator->setDescriptionEs('Salidas SIR - Mensual');
        $indicator->setDescriptionEu('SIR irteerak - Hilero');
        $indicator->setRequiredRoles(["ROLE_HAZ"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(3);
        $indicator->setDescriptionEs('Facturas telemáticas - Mensual');
        $indicator->setDescriptionEu('Faktura telematikoak - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(4);
        $indicator->setDescriptionEs('Expedientes abiertos - Mensual');
        $indicator->setDescriptionEu('Espediente zabalduta – Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(5);
        $indicator->setDescriptionEs('Notificaciones postales - Mensual');
        $indicator->setDescriptionEu('Jakinarazpen postalak – Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(6);
        $indicator->setDescriptionEs('Notificaciones telemáticas - Mensual');
        $indicator->setDescriptionEu('Jakinarazpen telematikoak – Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(7);
        $indicator->setDescriptionEs('Publicaciones en el tablón de anuncios en papel - Mensual');
        $indicator->setDescriptionEu('Iragarki taula argitalpenak paparean – Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(8);
        $indicator->setDescriptionEs('Publicaciones en el tablón de anuncios en telemáticas - Mensual');
        $indicator->setDescriptionEu('Iragarki taula argitalpenak telematikoak – Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(9);
        $indicator->setDescriptionEs('Volantes de empadronamiento en el cajero ciudadano - Mensual');
        $indicator->setDescriptionEu('Errolda agiriak herritarren kutxazainean - Hilero');
        $indicator->setRequiredRoles(["ROLE_HAZ"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(10);
        $indicator->setDescriptionEs('Volantes de empadronamiento por interoperabilidad - Mensual');
        $indicator->setDescriptionEu('Errolda agiriak interoperabilidade bidez - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(11);
        $indicator->setDescriptionEs('Volantes de empadronamiento en el SAC - Mensual');
        $indicator->setDescriptionEu('Errolda agiriak HAZean - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(12);
        $indicator->setDescriptionEs('Pagos en el cajero ciudadano - Mensual');
        $indicator->setDescriptionEu('Ordainketak herritar kutxazainean- Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(13);
        $indicator->setDescriptionEs('Pagos a través de mipago - Mensual');
        $indicator->setDescriptionEu('Ordainetak Mipago pasarelatik - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(14);
        $indicator->setDescriptionEs('Pagos en el SAC - Mensual');
        $indicator->setDescriptionEu('Ordainetak HAZean - Hilero');
        $indicator->setRequiredRoles(["ROLE_HAZ"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(15);
        $indicator->setDescriptionEs('Mensajes de Whatsapp recibidos - Mensual');
        $indicator->setDescriptionEu('Whatappsez jasotako mezuak - Hilero');
        $indicator->setRequiredRoles(["ROLE_HAZ"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(16);
        $indicator->setDescriptionEs('Visitas a la web municipal - Mensual');
        $indicator->setDescriptionEu('Udalwebguneko bisitak - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(17);
        $indicator->setDescriptionEs('Visitas a la sede electrónica - Mensual');
        $indicator->setDescriptionEu('Egoitza elektronikoko bisitak - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(18);
        $indicator->setDescriptionEs('% de documentos firmados digitalmente - Trimestral');
        $indicator->setDescriptionEu('Digitalki sinatutako documentuak % - Hiruhilabetero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(19);
        $indicator->setDescriptionEs('% de expedientes electrónicos - Trimestral');
        $indicator->setDescriptionEu('Espediente elektronikoen % - Hiruhilabetero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(20);
        $indicator->setDescriptionEs('% de expedientes archivados electrónicamente - Mensual');
        $indicator->setDescriptionEu('Artxibatutako Espediente elektronikoen % - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $indicator = new Indicator();
        $indicator->setId(21);
        $indicator->setDescriptionEs('% de flujos de información realizados - Mensual');
        $indicator->setDescriptionEu('Egindako Informazio fluxuen % - Hilero');
        $indicator->setRequiredRoles(["ROLE_INFORMATIKA"]);
        $manager->persist($indicator);

        $manager->flush();
    }
}
