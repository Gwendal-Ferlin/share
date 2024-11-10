<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Fichier;
use App\Form\ContactType;
use App\Form\FichierType;
use App\Repository\ScategorieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function profil(Request $request, ScategorieRepository $scategorieRepository,
        EntityManagerInterface $em, SluggerInterface $slugger): Response {
        $fichier = new Fichier();
        $scategories = $scategorieRepository->findBy([], ['categorie' => 'asc', 'numero' => 'asc']);
        $form = $this->createForm(FichierType::class, $fichier, ['scategories' => $scategories]);
        $form->handleRequest($request);
        $user = $this->getUser();
        $friends = $user->getUserAccepte();
        if ($friends->isEmpty()) {
            $friends = [];
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedScategories = $form->get('scategories')->getData();
            foreach ($selectedScategories as $scategorie) {
                $fichier->addScategory($scategorie);
            }
            $file = $form->get('fichier')->getData();

            if ($file) {
                $nomFichierServeur = pathinfo($file->getClientOriginalName(),
                    PATHINFO_FILENAME);
                $nomFichierServeur = $slugger->slug($nomFichierServeur);
                $nomFichierServeur = $nomFichierServeur . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $fichier->setNomServeur($nomFichierServeur);
                    $fichier->setNomOriginal($file->getClientOriginalName());
                    $fichier->setDateEnvoi(new \Datetime());
                    $fichier->setExtension($file->guessExtension());
                    $fichier->setTaille($file->getSize());
                    $fichier->setUser($this->getuser());
                    $em->persist($fichier);
                    $em->flush();
                    $file->move($this->getParameter('file_directory'), $nomFichierServeur);
                    $this->addFlash('notice', 'Fichier envoyé');
                    return $this->redirectToRoute('app_profil');
                } catch (FileException $e) {
                    $this->addFlash('notice', 'Erreur d\'envoi');
                }
            }
        }
        return $this->render('base/profil.html.twig', [
            'form' => $form,
            'scategories' => $scategories,
            'friends' => $friends,
        ]);
    }

    #[Route('/private-telechargement-fichier-user/{id}', name: 'app_telechargement_fichier_user',
        requirements: ["id" => "\d+"])]
    public function telechargementFichierUser(Fichier $fichier)
    {
        $user = $this->getUser();
        if ($fichier == null) {
            return $this->redirectToRoute('app_profil');
        } else {
            if ($fichier->getUser() !== $this->getUser() && !$fichier->getAccesAmis()->contains($user)) {
                $this->addFlash('notice', 'Vous n\'avez pas accès a ce fichier');
                return $this->redirectToRoute('app_profil');
            }
            
            return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNomServeur(),
                $fichier->getNomOriginal());
        }
    }

    #[Route('/private-partage_fichier/{fichierId}/{friendId}', name: 'app_partage_fichier', requirements: ["fichierId" => "\d+", "friendId" => "\d+"])]
    public function partageFichier(int $fichierId, int $friendId, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $fichier = $em->getRepository(Fichier::class)->find($fichierId);
        $friend = $userRepository->find($friendId);
        if (!$fichier || !$friend) {
            $this->addFlash('notice', 'Fichier ou utilisateur non trouvé.');
            return $this->redirectToRoute('app_profil');
        }

        if ($fichier->getUser() !== $this->getUser()) {
            $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
            return $this->redirectToRoute('app_profil');
        }

        $fichier->addAccesAmi($friend);
        $em->persist($fichier);
        $em->flush();

        $this->addFlash('notice', "Fichier partagé avec {$friend->getName()}.");
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/private-choix_amis_partage/{fichierId}', name: 'app_choix_amis_partage', requirements: ["fichierId" => "\d+"])]
    public function choix_amis_partage(int $fichierId, EntityManagerInterface $em): Response
    {   $user = $this->getUser();   
        $fichier = $em->getRepository(Fichier::class)->find($fichierId);
        $friends = $user->getUserAccepte();
        $access = $fichier->getAccesAmis();
        //dump($access);
        return $this->render('fichier/choix_amis_partage.html.twig', [
            'amis' => $friends,
            'fichierId' => $fichierId,
            'fichier' => $fichier,
        ]);
    }

    #[Route('/private-retirer_acces_amis/{fichierId}/{friendId}', name: 'app_retirer_acces_amis', requirements: ["fichierId" => "\d+", "friendId" => "\d+"])]
    public function retirerAccesAmis(int $fichierId, int $friendId, EntityManagerInterface $em, UserRepository $userRepository): Response
    {   
        $fichier = $em->getRepository(Fichier::class)->find($fichierId);
        $friend = $userRepository->find($friendId);
        $fichier->removeAccesAmi($friend);
        $em->flush();
        $this->addFlash('notice', "{$friend->getName()} {$friend->getPrenom()} n'a plus accès au fichier");
        return $this->redirectToRoute('app_profil');
    }

}
