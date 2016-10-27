<?php

namespace Sowp\BudgetBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sowp\BudgetBundle\Entity\Category;
use Sowp\BudgetBundle\Entity\Contract;
use Sowp\BudgetBundle\Form\CategoryType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CategoryRestController extends FOSRestController
{

    /**
     * List of categories
     *
     * @Rest\QueryParam(
     *     name = "page",
     *     requirements = "\d+",
     *     default = "1",
     *     nullable = true,
     *     description = "Page from which to start listing categories.")
     *
     * @Rest\QueryParam(
     *     name = "limit",
     *     requirements = "\d+",
     *     default = "20",
     *     description = "How many categories to return. Max 100")
     *
     * @Rest\QueryParam(
     *     name = "only_root",
     *     requirements = "(true|false)",
     *     default = "false",
     *     description = "Specifies the filter to only root")
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful"
     *     },
     *     section="Category"
     * )
     *
     * @Rest\View(
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"list", "Default"}
     * )
     *
     * @param Request $request
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function getCategoriesAction(ParamFetcherInterface $paramFetcher)
    {
        $repo = $this->getRepo();
        $page = $paramFetcher->get('page');
        $limit = min($paramFetcher->get('limit'), 100);
        $only_root = $paramFetcher->get('only_root');
        $parameters = array();

        $qb = $repo->findAllQueryBuilder();

        if($only_root == 'true'){
            $repo->filterOnlyRoot($qb);
            $parameters['only_root'] = 'true';
        }

        $adapter = new DoctrineORMAdapter($qb);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        return (new PagerfantaFactory('page', 'limit'))->createRepresentation(
            $pagerfanta,
            new Route('sowp_budget_category_api_get_categories', $parameters, true)
        );
    }

    /**
     * List child categories
     *
     * @Rest\QueryParam(
     *     name = "page",
     *     requirements = "\d+",
     *     default = "1",
     *     nullable = true,
     *     description = "Page from which to start listing categories."
     * )
     *
     * @Rest\QueryParam(
     *     name = "limit",
     *     requirements = "\d+",
     *     default = "20",
     *     description = "How many categories to return. Max 100"
     * )
     *
     * @Rest\QueryParam(
     *     name = "only_root",
     *     requirements = "(true|false)",
     *     default = "false",
     *     description = "Specifies the filter to only root"
     * )
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"list", "Default"}
     * )
     *
     * @param $id int the category id
     * @param $paramFetcher ParamFetcherInterface $paramFetcher
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     *
     */
    public function getCategoryChildrenAction($id, ParamFetcherInterface $paramFetcher)
    {
        $repo = $this->getRepo();
        $page = $paramFetcher->get('page');
        $limit = min($paramFetcher->get('limit'), 100);
        $parameters = array();


        $qb = $repo->findAllQueryBuilder();
        $qb->where($qb->expr()->eq('a.parent', $id));
        $adapter = new DoctrineORMAdapter($qb);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        return (new PagerfantaFactory('page', 'limit'))->createRepresentation(
            $pagerfanta,
            new Route('sowp_budget_category_api_get_categories', $parameters, true)
        );
    }

    /**
     * List contracts in category
     *
     * @Rest\QueryParam(
     *     name = "page",
     *     requirements = "\d+",
     *     default = "1",
     *     nullable = true,
     *     description = "Page from which to start listing categories."
     * )
     *
     * @Rest\QueryParam(
     *     name = "limit",
     *     requirements = "\d+",
     *     default = "20",
     *     description = "How many categories to return. Max 100"
     * )
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"list", "Default"}
     * )
     *
     * @param $id int the category id
     * @param $paramFetcher ParamFetcherInterface $paramFetcher
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     *
     */
    public function getCategoryContractsAction($id, ParamFetcherInterface $paramFetcher)
    {
        $contract_repo = $this->getDoctrine()->getRepository(Contract::class);
        $category_repo = $this->getDoctrine()->getRepository(Category::class);
        $page = $paramFetcher->get('page');
        $limit = min($paramFetcher->get('limit'), 100);
        $parameters = array('id' => $id);

        $category = $category_repo->find($id);
        if(!$category){
            $this->createNotFoundException();
        }
        $query = $contract_repo->getContractsInCategoryQuery($category);

        $adapter = new DoctrineORMAdapter($query);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        return (new PagerfantaFactory('page', 'limit'))->createRepresentation(
            $pagerfanta,
            new Route('sowp_budget_category_api_get_category_contracts', $parameters, true)
        );
    }

    /**
     * Presents the form to use to create a new categpry.
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *          200 = "Returned when successful"
     *     },
     *     section = "Category"
     * )
     *
     * @return \Symfony\Component\Form\Form
     */
    public function newCategoryAction()
    {
        return $this->createForm(CategoryType::class);
    }

    /**
     * Get a single category.
     *
     * @ApiDoc(
     *     output = "Sowp\BudgetBundle\Entity\Category",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         404 = "Returned when the category is not found"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *     templateVar = "category",
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"detail", "Default"})
     *
     * @param int $id the category id
     *
     * @return array
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function getCategoryAction($id)
    {
        $repo = $this->getRepo();
        $item = $repo->findByIdWithChildren($id);

        if (!$item) {
            throw $this->createNotFoundException("Category does not exist.");
        }
        return $item;
    }


    /**
     * Creates a new category from the submitted data.
     *
     * @ApiDoc(
     *     resource = true,
     *     input = "Sowp\BudgetBundle\Form\CategoryType",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return Form[]|View
     */
    public function postCategoriesAction(Request $request)
    {
        $item = new Category();
        $form = $this->createForm(CategoryType::class, $item, array('csrf_protection' => false, 'api' => true));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->routeRedirectView('sowp_budget_category_api_get_category', array('id' => $item->getId()));
        }
        return array(
            'form' => $form
        );
    }

    /**
     * Update entry, leaving the previous values
     *
     * @ApiDoc(
     *     resource = true,
     *     input = "Sowp\BudgetBundle\Form\CategoryType",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors",
     *         404 = "Returned when the category is not found"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *   template = "AppBundle:Note:newNote.html.twig"
     * )
     *
     * @param Request $request the request object
     *
     * @return Form[]|View
     */
    public function patchCategoriesAction($id, Request $request)
    {

        $item = $this->getRepo()->find($id);
        if (!$item) {
            throw $this->createNotFoundException("Category does not exist.");
        }
        $form = $this->createForm(CategoryType::class, $item, array(
            'csrf_protection' => false,
            'api' => true,
            'method' => 'PATCH'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            dump("!OK");
            return $this->routeRedirectView('sowp_budget_category_api_get_category', array('id' => $item->getId()), Response::HTTP_OK);
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Presents the form to use to update an existing category.
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         404 = "Returned when the category is not found"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View()
     *
     * @param int $id the category id
     *
     * @return Form
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function editCategoriesAction($id)
    {
        $item = $this->getRepo()->find($id);
        if (!$item) {
            throw $this->createNotFoundException("Category does not exist.");
        }
        return $this->createForm(CategoryType::class, $item, array('csrf_protection' => false, 'api' => true));
    }

    /**
     * Update existing category from the submitted data or create a new category at a specific location.
     *
     * @ApiDoc(
     *     resource = true,
     *     input = "Sowp\BudgetBundle\Form\CategoryType",
     *     statusCodes = {
     *         201 = "Returned when a new resource is created",
     *         204 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *     },
     *     section = "Category"
     * )
     *
     * @Rest\View(
     *   template = "AppBundle:Note:editNote.html.twig",
     *   templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the category id
     *
     * @return Form|View
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function putCategoriesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getRepo();

        $item = $repo->find(array('id' => $id));

        if (!$item) {
            $item = new Category();
            $statusCode = Response::HTTP_CREATED;
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }
        $form = $this->createForm(CategoryType::class, $item, array(
            'method' => 'PUT',
            'csrf_protection' => false,
            'api' => true,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($item);
            $em->flush();

            return $this->routeRedirectView('sowp_budget_category_api_get_category', array('id' => $item->getId()), $statusCode);
        }
        return $form;
    }

    /**
     * Removes a category.
     *
     * @ApiDoc(
     *      resource = true,
     *      statusCodes = {
     *         204 = "Returned when successful"
     *     },
     *     section = "Category"
     * )
     *
     * @param int $id the category id
     *
     * @return View
     */
    public function deleteCategoriesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getRepo();

        $item = $repo->findOneBy(array('id' => $id));

        $em->remove($item);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('sowp_budget_category_api_get_categories', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \Sowp\BudgetBundle\Repository\CategoryRepository
     */
    public function getRepo()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        return $repo;
    }

}