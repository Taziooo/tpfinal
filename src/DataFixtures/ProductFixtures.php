<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            ['Sticker Pack', 'Pack de stickers vinyle (démo).', 599],
            ['Poster A3', 'Poster A3 premium (démo).', 1299],
            ['Tote Bag', 'Tote bag coton (démo).', 1499],
            ['Mug', 'Mug céramique (démo).', 1199],
            ['Hoodie', 'Hoodie confortable (démo).', 3999],
        ];

        foreach ($items as [$name, $desc, $price]) {
            $p = new Product();
            $p->setName($name);
            $p->setDescription($desc);
            $p->setPriceCents($price);
            $p->setIsActive(true);
            $manager->persist($p);
        }

        $manager->flush();
    }
}
