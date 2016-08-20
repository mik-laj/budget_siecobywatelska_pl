<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sowp\BudgetBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $treeRepository = $em->getRepository('SowpBudgetBundle:Category');

        $root = new Category();
        $root->setTitle('Wydatki');

        $treeRepository
            ->persistAsLastChild($root);

        $it = new Category();
        $it->setTitle('Usługi IT');

        $travel = new Category();
        $travel->setTitle('Podróże');

        $office = new Category();
        $office->setTitle('Biuro');

        $events = new Category();
        $events->setTitle('Wydarzenia i konferencje');

        $realEstate = new Category();
        $realEstate->setTitle('Nieruchomosci');

        $treeRepository
            ->persistAsLastChildOf($it, $root)
            ->persistAsLastChildOf($travel, $root)
            ->persistAsLastChildOf($office, $root)
            ->persistAsLastChildOf($events, $root)
            ->persistAsLastChildOf($realEstate, $root);

        // IT
        $people_it = new Category();
        $people_it->setTitle('Ludzie');

        $software_it = new Category();
        $software_it->setTitle('Oprogramowanie');

        $treeRepository
            ->persistAsLastChildOf($people_it, $it)
            ->persistAsLastChildOf($software_it, $it);

        // IT - Software
        $server_software_it = new Category();
        $server_software_it->setTitle('Oprogramowanie serwerowe');

        $client_software_it = new Category();
        $client_software_it->setTitle('Oprogramowanie klienckie');

        $treeRepository
            ->persistAsLastChildOf($server_software_it, $software_it)
            ->persistAsLastChildOf($client_software_it, $software_it);

        // IT - Software - Client

        $antiwirus_client_software_it = new Category();
        $antiwirus_client_software_it->setTitle('Oprogramowanie antywirusowe');

        $os_client_software_it = new Category();
        $os_client_software_it->setTitle('Systemy operacyjne');

        $treeRepository
            ->persistAsLastChildOf($antiwirus_client_software_it, $client_software_it)
            ->persistAsLastChildOf($os_client_software_it, $client_software_it);

        // Travel
        $ticket_travel = new Category();
        $ticket_travel->setTitle('Bilety');

        $duties_travel = new Category();
        $duties_travel->setTitle('Oplaty celne');

        $treeRepository
            ->persistAsLastChildOf($ticket_travel, $travel)
            ->persistAsLastChildOf($duties_travel, $travel);

        // Office
        $paper_office = new Category();
        $paper_office->setTitle('Papier');

        $people_office = new Category();
        $people_office->setTitle('Ludzie');

        $treeRepository
            ->persistAsLastChildOf($paper_office, $office)
            ->persistAsLastChildOf($people_office, $office);

        // Events
        $room_events = new Category();
        $room_events->setTitle('Sala');

        $marketing_events = new Category();
        $marketing_events->setTitle('Marketing');

        $treeRepository
            ->persistAsLastChildOf($room_events, $events)
            ->persistAsLastChildOf($marketing_events, $events);

        // Revemies
        $revenues = new Category();
        $revenues->setTitle('Przychody');

        $treeRepository
            ->persistAsLastChild($revenues);

        $dotation = new Category();
        $dotation->setTitle('Dotacje indywidualne');

        $tax_writeoff = new Category();
        $tax_writeoff->setTitle('Odpis podatkowy');

        $members_fee = new Category();
        $members_fee->setTitle('Oplaty czlonkowskie');

        $treeRepository
            ->persistAsLastChildOf($dotation, $revenues)
            ->persistAsLastChildOf($tax_writeoff, $revenues)
            ->persistAsLastChildOf($members_fee, $revenues);

        $em->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
