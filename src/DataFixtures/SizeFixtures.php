<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Size;

class SizeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];

        foreach ($sizes as $sizeLabel) {
            $size = new Size();
            $size->setSizeLabel($sizeLabel);
            $manager->persist($size);
        }

        $manager->flush();
    }
}