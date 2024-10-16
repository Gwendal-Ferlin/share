<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use App\Entity\Categorie;
use App\Entity\Contact;
use App\Form\CategorieType;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', [

        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $contact->setDateEnvoi(new \Datetime());
                $em->persist($contact);
                $em->flush();
                $this->addFlash('notice', 'Message envoyé');
                return $this->redirectToRoute('app_contact');
            }
        }
        return $this->render('base/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/propos', name: 'app_propos')]
    public function propos(): Response
    {
        return $this->render('base/propos.html.twig', [

        ]);
    }
    #[Route('/mention', name: 'app_mention')]
    public function mention(): Response
    {
        return $this->render('base/mention.html.twig', [

        ]);
    }

    #[Route('/admin-liste-user', name: 'app_liste_users')]
    public function listeUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('base/liste-users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/private-profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('base/profil.html.twig');
    }
}
