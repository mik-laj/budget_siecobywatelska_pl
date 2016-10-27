<?php

use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Sowp\BudgetBundle\Entity\Category;
use Sowp\BudgetBundle\Entity\Contract;

trait ProjectContext
{
    use Behat\Symfony2Extension\Context\KernelDictionary;

    private $categories = [];
    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $this->truncateAllTables();
    }

    private function truncateAllTables(){
        $em = $this->getManager();
        $connection = $em->getConnection();
        $sm = $connection->getSchemaManager();

        $tables = $sm->listTables();

        $connection->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table){
            $tableName = $table->getName();
            $connection->query("TRUNCATE TABLE `${tableName}`");
        }
        $connection->query('SET FOREIGN_KEY_CHECKS = 1;');

    }
//    /**
//     * @Given /^the user "([^"]*)" exists$/
//     */
//    public function theUserExists($username)
//    {
//        $this->thereIsAUserWithPassword($username, 'foo');
//    }
//    /**
//     * @Given /^there is a user "([^"]*)" with password "([^"]*)"$/
//     */
//    public function thereIsAUserWithPassword($username, $password)
//    {
//        $this->createUser($username.'@foo.com', $password, $username);
//    }

    /**
     * @Given /^the following categories exist:$/
     */
    public function theFollowingCategoriesExist(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $title = $row['title'];
            $parent = isset($row['parent']) ? $row['parent'] : null;

            $this->createCategory($title, $parent);
        }
    }

    public function createCategory($title, $parent_title = null){
        $em = $this->getManager();

        $category = new Category();
        $category->setTitle($title);

        if($parent_title){
            $parent = $this->fetchCategory($parent_title);
            $category->setParent($parent);
        }

        $em->persist($category);
        $em->flush();

        $this->categories[$title] = $category;
    }

    /**
     * @Given /^the following contracts exist:$/
     */
    public function theFollowingContractsExist(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $conclusionAt = $row['conclusionAt'];
            $supplier = $row['supplier'];
            $title = $row['title'];
            $value = $row['value'];
            $category_title = isset($row['category']) ? $row['category'] : null;

            $this->createContract($conclusionAt, $supplier, $title, $value, $category_title);
        }
    }

    public function createContract($conclusionAt, $supplier, $title, $value, $category_title = null){
        $em = $this->getManager();

        $conclusionAtDate = \DateTime::createFromFormat('d-m-Y', $conclusionAt);

        $contract = new Contract();
        $contract->setTitle($title);
        $contract->setSupplier($supplier);
        $contract->setTitle($title);
        $contract->setValue($value);
        $contract->setConclusionAt($conclusionAtDate);

        if($category_title){
            $category = $this->fetchCategory($category_title);
            $contract->setCategory($category);
        }

        $em->persist($contract);
        $em->flush();
    }

//    public function getUserRepo(){
//        return $this->getManager()->getRepository(User::class);
//    }

    public function getContractRepo(){
        return $this->getManager()->getRepository(Contract::class);
    }

    public function getCategoryRepo(){
        return $this->getManager()->getRepository(Category::class);
    }

    public function getManager(){
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function fetchCategory($title)
    {
        if (isset($this->categories[$title])) {
            return $this->categories[$title];
        }
        $repo = $this->getCategoryRepo();
        $category = $repo->findOneBy(['title' => $title]);

        if (!$category) {
            throw new Exception("Unable to find fetch category with title: {$title}");
        }

        $this->categories[$title] = $category;
        return $category;
    }
}