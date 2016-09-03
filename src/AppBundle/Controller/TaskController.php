<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Task;
use AppBundle\Form\CommentType;
use AppBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/create")
     * @Template("AppBundle:Category:create.html.twig")
     */
    public function createAction(Request $request) {
        $task=new Task();
        $form = $this->createForm(
            new TaskType($this->getUser()),
            $task
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute("app_task_show", ['id' => $task->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/show/{id}")
     * @Template
     *
     */
    public function showAction($id) {
        $task =
            $this
                ->getDoctrine()
                ->getRepository('AppBundle:Task')
                ->find($id);

        $user = $this->getUser();

        if(!$task || ($task->getCategory()->getUser() !== $user)) {
            throw $this->createNotFoundException('Category not found');
        }

        $comment=new Comment();
        $form = $this->createForm(
            new CommentType(),
            $comment, array('action'=>$this->generateUrl('app_comment_create', ['id'=>$id]))
        );
        $form->add('submit', 'submit');

        return [
                'task' => $task,
                'form' => $form->createView()
        ];
    }

    /**
     * @Route("/showAll")
     * @Template
     */
    public function showAllAction() {
        $user = $this->getUser();
        $allTasks=[];
        $categories=$this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['user' => $user]);
        foreach ($categories as $category) {
            $task = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(['category'=>$category]);
            $allTasks[]=$task;
        }


        return ['allTasks'=>$allTasks];
    }

    /**
     * @Route("/{cat_id}/showAll")
     * @Template
     */
    public function showAllByCategoryAction($cat_id) {
        $user=$this->getUser();
        $category=$this->getDoctrine()->getRepository('AppBundle:Category')->find($cat_id);
        if(!$category || $category->getUser()!==$user) {
            throw $this->createNotFoundException('Category not found');
        }
        $tasks=$this->getDoctrine()->getRepository('AppBundle:Task')->findBy(['category'=>$category]);

        return ['tasks'=>$tasks];
    }

    /**
     * @Route("/edit/{id}")
     * @Template("AppBundle:Category:create.html.twig")
     */
    public function editAction(Request $request, $id) {
        $task =
            $this
                ->getDoctrine()
                ->getRepository('AppBundle:Task')
                ->find($id);
        $user=$this->getUser();

        if(!$task || $task->getCategory()->getUser()!==$user) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(
            new TaskType($user),
            $task
        );

        $form->add('submit', 'submit');
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute("app_task_show", ['id' => $task->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $task=$this->getDoctrine()->getRepository('AppBundle:Task')->find($id);
        $user=$this->getUser();

        if(!$task || $task->getCategory()->getUser()!==$user) {
            throw $this->createNotFoundException('Category not found');
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('app_task_showall');
    }
}
