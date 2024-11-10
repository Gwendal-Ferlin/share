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
        ]);
    }

    #[Route('/private-telechargement-fichier-user/{id}', name: 'app_telechargement_fichier_user',
        requirements: ["id" => "\d+"])]
    public function telechargementFichierUser(Fichier $fichier)
    {
        if ($fichier == null) {
            return $this->redirectToRoute('app_profil');
        } else {
            if ($fichier->getUser() !== $this->getUser()) {
                $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
                return $this->redirectToRoute('app_profil');
            }
            return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNomServeur(),
                $fichier->getNomOriginal());
        }
    }

    #[Route('/private-partage_fichier/{id}', name: 'app_partage_fichier',
        requirements: ["id" => "\d+"])]
    public function partageFichier(Fichier $fichier)
    {
        if ($fichier == null) {
            return $this->redirectToRoute('app_profil');
        } else {
            if ($fichier->getUser() !== $this->getUser()) {
                $this->addFlash('notice', 'Vous n\'êtes pas le propriétaire de ce fichier');
                return $this->redirectToRoute('app_profil');
            }
            return $this->file($this->getParameter('file_directory') . '/' . $fichier->getNomServeur(),
                $fichier->getNomOriginal());
        }
    }

}
