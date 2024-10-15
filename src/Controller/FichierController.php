<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\FichierController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fichier;
use App\Form\FichierType;
use App\Repository\FichierRepository;

class FichierController extends AbstractController
{
    #[Route('/admin-ajout-fichier', name: 'app_ajout_fichier')]
    public function fichier(Request $request, EntityManagerInterface $em): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $fichier->setDateEnvoi(new \Datetime());
                $em->persist($fichier);
                $em->flush();
                $this->addFlash('notice', 'Fichier ajouté');
                return $this->redirectToRoute('app_accueil');
            }
        }
        return $this->render('fichier/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/liste-fichiers', name: 'app_liste_fichiers')]
    public function listeFichiers(FichierRepository $fichierRepository): Response
    {
        $fichiers = $fichierRepository->findAll();
        return $this->render('fichier/liste-fichiers.html.twig', [
            'fichiers' => $fichiers,
        ]);
    }
};
