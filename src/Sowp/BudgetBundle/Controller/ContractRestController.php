<?php

namespace Sowp\BudgetBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sowp\BudgetBundle\Entity\Contract;
use Sowp\BudgetBundle\Form\ContractType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ContractRestController extends FOSRestController
{

    /**
     * List of contracts
     *
     * @Rest\QueryParam(
     *     name = "page",
     *     requirements = "\d+",
     *     default = "1",
     *     nullable = true,
     *     description = "Page from which to start listing contracts.")
     *
     * @Rest\QueryParam(
     *     name = "limit",
     *     requirements = "\d+",
     *     default = "20",
     *     description = "How many contracts to return. Max 100")
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful"
     *     },
     *     section="Contract"
     * )
     *
     * @Rest\View(
     *     templateVar = "data",
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"list", "Default"}
     * )
     *
     * @param Request $request
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function getContractsAction(ParamFetcherInterface $paramFetcher)
    {
        $repo = $this->getRepo();
        $page = $paramFetcher->get('page');
        $limit = min($paramFetcher->get('limit'), 100);
        $parameters = array();

        $qb = $repo->findAllQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        return (new PagerfantaFactory('page', 'limit'))->createRepresentation(
            $pagerfanta,
            new Route('sowp_budget_contract_api_get_contracts', $parameters, true)
        );
    }


    /**
     * Presents the form to use to create a new contract.
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *          200 = "Returned when successful"
     *     },
     *     section = "Contract"
     * )
     *
     * @return \Symfony\Component\Form\Form
     */
    public function newContractAction()
    {
        return $this->createForm(ContractType::class);
    }

    /**
     * Get a single contract.
     *
     * @ApiDoc(
     *     output = "Sowp\BudgetBundle\Entity\Contract",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         404 = "Returned when the contract is not found"
     *     },
     *     section = "Contract"
     * )
     *
     * @Rest\View(
     *     serializerEnableMaxDepthChecks = true,
     *     serializerGroups = {"detail", "Default"})
     *
     * @param int $id the contract id
     *
     * @return object
     *
     * @throws NotFoundHttpException when contract not exist
     */
    public function getContractAction($id)
    {
        $repo = $this->getRepo();
        $item = $repo->find($id);

        if (!$item) {
            throw $this->createNotFoundException("Contract does not exist.");
        }
        return $item;
    }


    /**
     * Creates a new contract from the submitted data.
     *
     * @ApiDoc(
     *     resource = true,
     *     input = "Sowp\BudgetBundle\Form\ContractType",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *     },
     *     section = "Contract"
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
    public function postContractsAction(Request $request)
    {
        $item = new Contract();
        $form = $this->createForm(ContractType::class, $item, array('csrf_protection' => false, 'api' => true));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->routeRedirectView('sowp_budget_contract_api_get_contract', array('id' => $item->getId()));
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
     *     input = "Sowp\BudgetBundle\Form\ContractType",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors",
     *         404 = "Returned when the category is not found"
     *     },
     *     section = "Contract"
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
    public function patchContractsAction($id, Request $request)
    {

        $item = $this->getRepo()->find($id);
        if (!$item) {
            throw $this->createNotFoundException("Contract does not exist.");
        }
        $form = $this->createForm(ContractType::class, $item, array(
            'csrf_protection' => false,
            'api' => true,
            'method' => 'PATCH'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            return $this->routeRedirectView('sowp_budget_category_api_get_category', array('id' => $item->getId()), Response::HTTP_OK);
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Presents the form to use to update an existing contract.
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         404 = "Returned when the category is not found"
     *     },
     *     section = "Contract"
     * )
     *
     * @Rest\View()
     *
     * @param int $id the contract id
     *
     * @return Form
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function editContractsAction($id)
    {
        $item = $this->getRepo()->find($id);
        if (!$item) {
            throw $this->createNotFoundException("Contract does not exist.");
        }
        return $this->createForm(ContractType::class, $item, array('csrf_protection' => false, 'api' => true));
    }

    /**
     * Update existing contract from the submitted data or create a new contract at a specific location.
     *
     * @ApiDoc(
     *     resource = true,
     *     input = "Sowp\BudgetBundle\Form\ContractType",
     *     statusCodes = {
     *         201 = "Returned when a new resource is created",
     *         204 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *     },
     *     section = "Contract"
     * )
     *
     * @Rest\View()
     *
     * @param Request $request the request object
     * @param int     $id      the contract id
     *
     * @return Form|View
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function putContractsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getRepo();

        $item = $repo->find(array('id' => $id));

        if (!$item) {
            $item = new Contract();
            $statusCode = Response::HTTP_CREATED;
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }
        $form = $this->createForm(ContractType::class, $item, array(
            'method' => 'PUT',
            'csrf_protection' => false,
            'api' => true,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($item);
            $em->flush();

            return $this->routeRedirectView('sowp_budget_contract_api_get_contract', array('id' => $item->getId()), $statusCode);
        }
        return $form;
    }

    /**
     * Removes a contract.
     *
     * @ApiDoc(
     *      resource = true,
     *      statusCodes = {
     *         204 = "Returned when successful"
     *     },
     *     section = "Contract"
     * )
     *
     * @param int $id the contract id
     *
     * @return View
     */
    public function deleteContractsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getRepo();

        $item = $repo->findOneBy(array('id' => $id));

        $em->remove($item);
        $em->flush();

        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('sowp_budget_contract_api_get_contracts', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \Sowp\BudgetBundle\Repository\ContractRepository
     */
    public function getRepo()
    {
        return $this->getDoctrine()->getRepository(Contract::class);
    }

}