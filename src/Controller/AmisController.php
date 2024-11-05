<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\AjoutAmiType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AmisController extends AbstractController
{
    #[Route('/private-amis', name: 'app_amis')]
    public function amis(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        if ($request->get('id') != null) {
            $id = $request->get('id');
            $userDemande = $userRepository->find($id);
            if ($userDemande) {
                $this->getUser()->removeDemander($userDemande);
                $em->persist($this->getUser());
                $em->flush();
            }
        }
        if ($request->get('idRefuser') != null) {
            $id = $request->get('idRefuser');
            $userRefuser = $userRepository->find($id);
            if ($userRefuser) {
                $this->getUser()->removeUsersDemande($userRefuser);
                $em->persist($this->getUser());
                $em->flush();
            }
        }
        if ($request->get('idAccepter') != null) {
            $id = $request->get('idAccepter');
            $userAccepter = $userRepository->find($id);
            if ($userAccepter) {
                $this->getUser()->addAccepter($userAccepter);
                $userAccepter->addAccepter($this->getUser());
                $this->getUser()->removeUsersDemande($userAccepter);
                $em->persist($this->getUser());
                $em->persist($userAccepter);
                $em->flush();
            }
        }
        $form = $this->createForm(AjoutAmiType::class);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $ami = $userRepository->findOneBy(array('email' => $form->get('email')->getData()));
                if (!$ami) {
                    $this->addFlash('notice', 'Ami introuvable');
                    return $this->redirectToRoute('app_amis');
                } else {
                    $this->getUser()->addDemander($ami);
                    $em->persist($this->getUser());
                    $em->flush();
                    $this->addFlash('notice', 'Invitation envoyée');
                    return $this->redirectToRoute('app_amis');
                }

            }
        }
        return $this->render('amis/amis.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/private-supprimer-amis/{id}', name: 'app_supprimer_amis')]
    public function supprimerAmis(int $id, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();

        $ami = $userRepository->find($id);

        if ($ami !== null && $currentUser->getAccepter()->contains($ami)) {

            $currentUser->removeAccepter($ami);
            $em->persist($currentUser);
            $em->flush();

            $this->addFlash('notice', 'Ami supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Ami non trouvé ou non lié.');
        }

        return $this->redirectToRoute('app_amis');
    }

}
