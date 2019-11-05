<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Form\DiscussionType;
use App\Repository\DiscussionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
  * @IsGranted("ROLE_ADMIN")
 */
class DiscussionAdminController extends AbstractController
{
    /**
     * @Route("/discussion", name="admin_discussion_index", methods={"GET"})
     */
    public function index(DiscussionRepository $discussionRepository): Response
    {
        return $this->render('discussion_admin/index.html.twig', [
            'discussions' => $discussionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/discussion/new", name="admin_discussion_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
	
	    $user = $this->getUser();
		// $author = $user->getAuthor();
	     //$user = $this->user->getAuthor();
		//if(!$user->getAuthor()){
		//  $this->addFlash('error', 'Inscrivez-vous pour devenir un auteur!');
        //return $this->redirectToRoute('admin_author_new');
		
		//}
	
	    
		
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
		    $discussion->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
			
            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('admin_discussion_index');
        }

        return $this->render('discussion_admin/new.html.twig', [
            'discussion' => $discussion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("discussion/{id}", name="admin_discussion_show", methods={"GET"})
     */
    public function show(Discussion $discussion): Response
    {
        return $this->render('discussion_admin/show.html.twig', [
            'discussion' => $discussion,
        ]);
    }

    /**
     * @Route("/discussion/{slug}/edit", name="admin_discussion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Discussion $discussion): Response
    {
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
		$discussion = $form->getData();
            $this->getDoctrine()->getManager()->flush();
               $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin_discussion_index', [
                'slug' => $discussion->getSlug(),
            ]);
        }

        return $this->render('discussion_admin/edit.html.twig', [
            'discussion' => $discussion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/discussion/{slug}/delete", name="admin_discussion_delete", methods={"DELETE","POST","GET"})
     */
    public function delete(Request $request, Discussion $discussion): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($discussion);
            $entityManager->flush();
			 $this->addFlash('success', 'Article supprimé avec succès');
        //}

        return $this->redirectToRoute('admin_discussion_index');
    }
}
