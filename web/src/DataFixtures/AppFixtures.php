<?php

namespace App\DataFixtures;

use App\Entity\Leads;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $lead = new Leads();
            $lead->setName('product '.$i);
            $lead->setSourceId(mt_rand(10, 100));
            $lead->setStatus("status".mt_rand(10, 100));
            $lead->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', time())));
            $lead->setCreatedBy(mt_rand(10, 100));


            $manager->persist($lead);
        }
        $manager->flush();
    }
}
