<?php

namespace Sowp\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sowp\BudgetBundle\Entity\Category;
use Sowp\BudgetBundle\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('SowpBudgetBundle:Category')->findAll();

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

        $categories = $em->getRepository('SowpBudgetBundle:Category')->childrenHierarchy();

        return $this->render('SowpBudgetBundle:CategoryAdmin:tree.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new/{id}", name="admin_category_new", defaults={"id" = -1})
     * @Method({"GET", "POST"})
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

            return $this->redirectToRoute('category_show', array('id' => $category->getId()));
        }

        return $this->render('SowpBudgetBundle:CategoryAdmin:new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="admin_category_show")
     * @Method("GET")
     */
    public function showAction(Category $category)
    {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('SowpBudgetBundle:CategoryAdmin:show.html.twig', array(
            'category' => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="admin_category_edit")
     * @Method({"GET", "POST"})
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

            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        return $this->render('SowpBudgetBundle:CategoryAdmin:edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="admin_category_delete")
     * @Method("DELETE")
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
            ->setAction($this->generateUrl('admin_category_delete', array('id' => $category->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
