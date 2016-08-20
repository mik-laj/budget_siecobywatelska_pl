<?php

namespace Sowp\BudgetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sowp\BudgetBundle\Entity\Category;

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

        $categories = $em->getRepository('SowpBudgetBundle:Category')->getRootNodes();

        return $this->render('SowpBudgetBundle:Budget:index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/{id}", name="budget_show")
     * @Method("GET")
     */
    public function showAction(Category $category)
    {
        return $this->render('SowpBudgetBundle:Budget:show.html.twig', [
            'id' => $category->getId()
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
     */
    public function jsonCategoriesAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('SowpBudgetBundle:Category')->childrenHierarchy($category, false, [], true);

        $data = [
            'category' => $this->serialzeCategories($categories)[0]
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
     */
    public function jsonContractAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $contracts = $em->getRepository('SowpBudgetBundle:Contract')->getContractsInCategory($category);
        $contracts->setFetchMode("SowpBudgetBundle:Category", "category", "EAGER");
        $data = [
            'contracts' => $this->serializeConracts($contracts->getResult())
        ];

        return new JsonResponse($data);
    }


    private function serialzeCategories($categories)
    {
        $raw = [];
        foreach ($categories as $category) {
            $raw[] = [
                "id" => $category['id'],
                "title" => $category['title'],
                "children" => $this->serialzeCategories($category['__children']),
            ];
        }
        return $raw;
    }

    private function serializeConracts($contracts)
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
