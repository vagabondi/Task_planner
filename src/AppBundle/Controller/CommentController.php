<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * @Route("/create/{id}")
     */
    public function createAction(Request $request, $id)
    {
        $comment = new Comment();

        $form = $this -> createForm(
            new CommentType(), $comment,
            array(
                'action'=>$this->generateUrl('app_task_create', ['id' => $id])
            )
        );
        $task = $this->getDoctrine() -> getRepository( 'AppBundle:Task' ) -> find( $id );
        $form -> add( 'submit', 'submit' );
        $form -> handleRequest( $request );

        if( $form -> isValid() ) {
            $comment -> setTask( $task );
            $task -> addComment( $comment );
            $em = $this -> getDoctrine() -> getManager();
            $em -> persist( $comment );
            $em -> flush();

            return $this -> redirectToRoute( "app_task_show", ['id' => $id] );
        }

        return [ 'form' => $form->createView() ];

    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        $comment = $this -> getDoctrine() -> getRepository( 'AppBundle:Comment' ) -> find( $id );
        $user = $this -> getUser();

        if( !$comment || $comment -> getTask() -> getCategory() -> getUser() !== $user ) {
            throw $this -> createNotFoundException( 'Category not found' );
        }

        $em = $this -> getDoctrine() -> getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('app_task_show', ['id'=>$comment->getTask()->getId()]);
    }
}
