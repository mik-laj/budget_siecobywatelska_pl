<?php

namespace Sowp\BudgetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sowp\BudgetBundle\Entity\Category;
use Sowp\BudgetBundle\Entity\Contract;
use Sowp\BudgetBundle\Repository\CategoryRepository;
use Sowp\BudgetBundle\Repository\ContractRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Budget controller.
 *
 * @Route("/")
 */
class BudgetController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @Route("/", name="budget_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $repo */
        $repo = $em->getRepository('SowpBudgetBundle:Category');
        $categories = $repo->getRootNodes();

        return $this->render('SowpBudgetBundle:Budget:index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/{id}", name="budget_show")
     * @Method("GET")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Category $category)
    {
        return $this->render('SowpBudgetBundle:Budget:show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * Lists all Category entities.
     *
     * @Route(
     *   "{id}/categories.{_format}",
     *   name="budget_categories_json",
     *   defaults={"_format": "json"})
     * @Method("GET")
     * @param Category $category
     * @return JsonResponse
     */
    public function jsonCategoriesAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $repo */
        $repo = $em->getRepository('SowpBudgetBundle:Category');
        $categories = $repo->childrenHierarchy($category, false, [], true);

        $data = [
            'category' => $this->serializeCategories($categories)[0]
        ];

        return new JsonResponse($data);
    }

    /**
     * Lists all Contract entities.
     *
     * @Route(
     *   "{id}/contracts.{_format}",
     *   name="budget_contracts_json",
     *   defaults={"_format": "json"})
     * @Method("GET")
     * @param Category $category
     * @return JsonResponse
     */
    public function jsonContractAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ContractRepository $repo */
        $repo = $em->getRepository('SowpBudgetBundle:Contract');
        $contracts = $repo->getContractsInCategoryQuery($category);
        $contracts->setFetchMode("SowpBudgetBundle:Category", "category", "EAGER");
        $data = [
            'contracts' => $this->serializeContracts($contracts->getResult())
        ];

        return new JsonResponse($data);
    }


    /**
     * @param array $categories
     * @return array
     */
    private function serializeCategories(array $categories)
    {
        $raw = [];
        foreach ($categories as $category) {
            $raw[] = [
                "id" => $category['id'],
                "title" => $category['title'],
                "children" => $this->serializeCategories($category['__children']),
            ];
        }
        return $raw;
    }


    /**
     * @param Contract[] $contracts
     * @return array
     */
    private function serializeContracts(array $contracts)
    {
        $raw = [];
        foreach ($contracts as $contract) {
            $raw[] = [
                'id' => $contract->getId(),
                'title' => $contract->getTitle(),
                'category' => $contract->getCategory()->getTitle(),
                'value' => $contract->getValue(),
                'supplier' => $contract->getSupplier(),
                'conclusion_at' => $contract->getConclusionAt(),
            ];
        }
        return $raw;
    }
}
