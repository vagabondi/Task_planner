<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/create")
     * @Template
     */
    public function createAction(Request $request) {
        $category = new Category();
        $user = $this->getUser();

        $form = $this->createForm(
            new CategoryType(),
            $category
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $category->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute("app_category_show", ['id' => $category->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/show/{id}")
     * @Template
     */
    public function showAction($id) {
        $category =
            $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        $user = $this->getUser();

        if(!$category || ($category->getUser() !== $user)) {
            throw $this->createNotFoundException('Category not found');
        }

        return ['category' => $category];
    }

    /**
     * @Route("/showAll")
     * @Template
     */
    public function showAllAction() {
        $user = $this->getUser();
        $categories =
            $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['user'=>$user]);

        return ['categories'=>$categories];
    }

    /**
     * @Route("/edit/{id}")
     * @Template("AppBundle:Category:create.html.twig")
     */
    public function editAction(Request $request, $id) {
        $category =
            $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);
        $user=$this->getUser();

        if(!$category || $category->getUser()!==$user) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(
            new CategoryType(),
            $category
        );

        $form->add('submit', 'submit');
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute("app_category_show", ['id' => $category->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $category=$this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        $user=$this->getUser();

        if(!$category || $category->getUser()!==$user) {
            throw $this->createNotFoundException('Category not found');
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('app_category_showall');
    }
}
