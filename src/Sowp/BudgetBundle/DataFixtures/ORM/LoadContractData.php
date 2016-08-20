<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sowp\BudgetBundle\Entity\Contract;

class LoadContractData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $categories = $em->getRepository('SowpBudgetBundle:Category')->findAll();
        $faker = \Faker\Factory::create();

        foreach ($categories as $category) {
            $contract = new Contract();
            $contract->setCategory($category);
            $contract->setSupplier($faker->name);
            $contract->setTitle($faker->text(255));
            $contract->setConclusionAt($faker->dateTime);
            $contract->setValue($faker->numberBetween(1000,90000));
            $em->persist($contract);

            $contract = new Contract();
            $contract->setCategory($category);
            $contract->setSupplier($faker->name);
            $contract->setTitle($faker->text(255));
            $contract->setConclusionAt($faker->dateTime);
            $contract->setValue($faker->numberBetween(1000,90000));
            $em->persist($contract);

        }
        // for ($i=0; $i < 1000; $i++) { 
        //     $contract = new Contract();
        //     $contract->setCategory($faker->randomElement($categories));
        //     $contract->setSupplier($faker->name);
        //     $contract->setTitle($faker->text(255));
        //     $contract->setConclusionAt($faker->dateTime);
        //     $contract->setValue($faker->numberBetween(1000,90000));

        //     $em->persist($contract);
        // }

        $em->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
