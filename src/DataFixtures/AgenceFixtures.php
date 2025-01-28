<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $agences = [
            [
                'numero' => 'AG_0',
                'adresse' => 'Point E',
                'telephone' => '33 800 10 10'
            ],
            [
                'numero' => 'AG_1',
                'adresse' => 'FANN',
                'telephone' => '33 800 10 11'
            ],
            [
                'numero' => 'AG_2',
                'adresse' => 'Guele Tappee',
                'telephone' => '33 800 10 12'
            ],
        ];

        foreach ($agences as $agenceData) {
            $agence = new Agence();
            $agence->setNumero($agenceData['numero']);
            $agence->setAdresse($agenceData['adresse']);
            $agence->setTelephone($agenceData['telephone']);
            
            $manager->persist($agence);
        }

        $manager->flush();
    }
} 