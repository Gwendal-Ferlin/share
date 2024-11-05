<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Scategorie;
use App\Form\AjoutScategorieType;
use App\Form\ModifierCategorieType;
use App\Form\ModifierSousCategorieType;
use App\Form\SupprimerCategorieType;
use App\Form\SupprimerSousCategorieType;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;

class CategorieController extends AbstractController
{
    #[Route('/private-categorie', name: 'app_categorie')]
    public function categorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('notice', 'Message envoyé');
                return $this->redirectToRoute('app_categorie');
            }
        }
        return $this->render('categorie/categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/private-liste-categories', name: 'app_liste_categories', methods: ['GET', 'POST'])]
    public function listeCategories(Request $request, CategorieRepository $categorieRepository,
        EntityManagerInterface $em): Response {
        $categories = $categorieRepository->findAll();
        $form = $this->createForm(SupprimerCategorieType::class, null, [
            'categories' => $categories,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCategories = $form->get('categories')->getData();
            foreach ($selectedCategories as $categorie) {
                $em->remove($categorie);
            }
            $em->flush();
            $this->addFlash('notice', 'Catégories supprimées avec succès');
            return $this->redirectToRoute('app_liste_categories');
        }
        return $this->render('categorie/liste-categories.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/private-modifier-categorie/{id}', name: 'app_modifier_categorie')]
    public function modifierCategorie(Request $request, Categorie
         $categorie, EntityManagerInterface $em): Response {
        $form = $this->createForm(ModifierCategorieType::class, $categorie);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('notice', 'Catégorie modifiée');
                return $this->redirectToRoute('app_liste_categories');
            }
        }
        return $this->render('categorie/modifier-categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/private-supprimer-categorie/{id}', name: 'app_supprimer_categorie')]
    public function supprimerCategorie(Request $request, Categorie
         $categorie, EntityManagerInterface $em): Response {
        if ($categorie != null) {
            $em->remove($categorie);
            $em->flush();
            $this->addFlash('notice', 'Catégorie supprimée');
        }
        return $this->redirectToRoute('app_liste_categories');
    }

    #[Route('/ajout-scategorie', name: 'app_ajout-scategorie')]
    public function ajoutScategorie(Request $request, EntityManagerInterface $em): Response
    {
        $scategorie = new Scategorie();
        $form = $this->createForm(AjoutScategorieType::class, $scategorie);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em->persist($scategorie);
                    $em->flush();
                } catch (\RuntimeException $e) {
                    $this->addFlash('notice', $e->getMessage());
                    return $this->redirectToRoute('app_ajout-scategorie');
                }
                $this->addFlash('notice', 'Sous catégorie insérée');
                return $this->redirectToRoute('app_ajout-scategorie');
            }
        }
        return $this->render('sousCategorie/ajout_scategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/private-supprimer-sous-categorie/{id}', name: 'app_supprimer_sous_categorie')]
    public function supprimerSousCategorie(Request $request, Scategorie
         $Scategorie, EntityManagerInterface $em): Response {
        if ($Scategorie != null) {
            $em->remove($Scategorie);
            $em->flush();
            $this->addFlash('notice', 'Sous Catégorie supprimée');
        }
        return $this->redirectToRoute('app_liste_categories');
    }

    #[Route('/private-modifier-sous-categorie/{id}', name: 'app_modifier_sous_categorie')]
    public function modifierSousCategorie(Request $request, Scategorie
         $Scategorie, EntityManagerInterface $em): Response {
        $form = $this->createForm(ModifierSousCategorieType::class, $Scategorie);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($Scategorie);
                $em->flush();
                $this->addFlash('notice', 'Sous Catégorie modifiée');
                return $this->redirectToRoute('app_liste_categories');
            }
        }
        return $this->render('sousCategorie/modifier-sous-categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
