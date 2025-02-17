<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Clipping;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller {

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Category::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $categories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'categories' => $categories,
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The new category was created.');
            return $this->redirectToRoute('category_show', array('id' => $category->getId()));
        }

        return array(
            'category' => $category,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     * @param Category $category
     * @param Request $request
     */
    public function showAction(Request $request, Category $category) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Clipping::class);
        $query = $repo->categoryQuery($category);
        $paginator = $this->get('knp_paginator');
        $clippings = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'category' => $category,
            'clippings' => $clippings,
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Category $category
     */
    public function editAction(Request $request, Category $category) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The category has been updated.');
            return $this->redirectToRoute('category_show', array('id' => $category->getId()));
        }

        return array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}/delete", name="category_delete")
     * @Method("GET")
     * @param Request $request
     * @param Category $category
     */
    public function deleteAction(Request $request, Category $category) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'The category was deleted.');

        return $this->redirectToRoute('category_index');
    }

}
