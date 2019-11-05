<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Form\DiscussionType;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\DiscussionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    


class DiscussionController extends AbstractController
{


 /**   
     * @var DiscussionRepository
     */
    private $discussionrepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(DiscussionRepository $discussionrepository, EntityManagerInterface $em)
    {
        $this->discussionrepository = $discussionrepository;
        $this->em = $em;
    }



     
    /**
     * @Route("/discussion", name="discussion_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator,DiscussionRepository $discussionRepository, Request $request): Response
    {
         $user = $this->getUser();
          $discussions = $paginator->paginate(
            $discussionRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('discussion/index.html.twig', [
		     'discussions' => $discussions ,
             
    ]);
	}
	
	 /**
     * @Route("/discussion/list", name="discussion_list")
	 * @IsGranted("ROLE_USER")
     */
    public function list(DiscussionRepository $discussionRepository)
    {
	     $user = $this->getUser();
		
       
        return $this->render('discussion/list.html.twig', [
            'alldiscussions' => $discussionRepository->getDiscussionWithUser($user),
        ]);
    }
	

    /**
     * @Route("/discussion/new", name="discussion_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
	
	    $user = $this->getUser();
		// $author = $user->getAuthor();
	     //$user = $this->user->getAuthor();
		//if(!$user->getAuthor()){
		//  $this->addFlash('error', 'Inscrivez-vous pour devenir un auteur!');
       // return $this->redirectToRoute('admin_author_new');
		
		//}
	
	    
		
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
		 $discussion->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('discussion_index');
        }

        return $this->render('discussion_admin/new.html.twig', [
            'discussion' => $discussion,
            'form' => $form->createView(),
        ]);
    }

    
	
	 /**
     * @Route("/discussion/news/{id}/heart", name="discussion_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart(Discussion $discussion, LoggerInterface $logger,EntityManagerInterface $em)
    {
        // TODO - actually heart/unheart the article!
        $logger->info('Article is being hearted!');
        $discussion->incrementHeartCount();
        $em->flush();
        return new JsonResponse(['hearts' => $discussion->getHeartCount()]);
    }
	

    /**
     * @Route("/discussion/{slug}/edit", name="discussion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Discussion $discussion): Response
    {
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('discussion_index', [
                'id' => $discussion->getId(),
            ]);
        }

        return $this->render('discussion_admin/edit.html.twig', [
            'discussion' => $discussion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/discussion/{slug}/delete", name="discussion_delete", methods={"DELETE","POST","GET"})
     */
    public function delete(Request $request, Discussion $discussion): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($discussion);
            $entityManager->flush();
			 $this->addFlash('success', 'Article supprimé avec succès');
        //}

        return $this->redirectToRoute('discussion_list');
    }
	
   
	  /**
     * @Route("/discussion/{slug}", name="discussion_show", methods={"GET", "POST"})
     */
    public function show(Discussion $discussion, $slug, Request $request): Response
    {
        return $this->render('discussion/show.html.twig', [
            'discussion' => $discussion,
        ]);
    }
	
}
