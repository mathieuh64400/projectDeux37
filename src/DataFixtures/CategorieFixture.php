<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategorieFixture extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for ($i = 0; $i < 10; $i++) {

            $categorie = new Categorie();
            $categorie->setLibelle(($faker->word) . ($faker->word));
            $manager->persist($categorie);
        }
      




        $manager->flush();
    }
}
