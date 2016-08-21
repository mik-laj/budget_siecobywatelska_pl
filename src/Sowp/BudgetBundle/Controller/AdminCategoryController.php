<?php

namespace Sowp\BudgetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sowp\BudgetBundle\Entity\Category;
use Sowp\BudgetBundle\Form\CategoryType;
use Sowp\BudgetBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class AdminCategoryController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @Route("/", name="admin_category_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');

        $query = $em->getRepository('SowpBudgetBundle:Category')->findAllQuery();

        $categories = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('SowpBudgetBundle:CategoryAdmin:index.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Lists all Category entities as tree.
     *
     * @Route("/tree", name="admin_category_tree")
     * @Method("GET")
     */
    public function treeAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $repo */
        $repo = $em->getRepository('SowpBudgetBundle:Category');
        $categories = $repo->childrenHierarchy();

        return $this->render('SowpBudgetBundle:CategoryAdmin:tree.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new/{id}", name="admin_category_new", defaults={"id" = -1})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Category $parent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Category $parent = null)
    {
        $category = new Category();
        $form = $this->createForm('Sowp\BudgetBundle\Form\CategoryType', $category);
        $form->add('Create', SubmitType::class);
        $form->handleRequest($request);
        
        // Set default parent
        $form->get('parent')->setData($parent);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('SowpBudgetBundle:CategoryAdmin:new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="admin_category_show")
     * @Method("GET")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('SowpBudgetBundle:CategoryAdmin:show.html.twig', [
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="admin_category_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm('Sowp\BudgetBundle\Form\CategoryType', $category);
        $editForm->add('Edit', SubmitType::class);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_edit', ['id' => $category->getId()]);
        }

        return $this->render('SowpBudgetBundle:CategoryAdmin:edit.html.twig', [
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="admin_category_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Creates a form to delete a Category entity.
     *
     * @param Category $category The Category entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_category_delete', ['id' => $category->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
