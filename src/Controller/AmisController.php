<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AjoutAmiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
class AmisController extends AbstractController
{
 #[Route('/private-amis', name: 'app_amis')]
 public function amis(Request $request, EntityManagerInterface $em, UserRepository
$userRepository): Response
 {
 if($request->get('id')!=null){
 $id = $request->get('id');
 $userDemande = $userRepository->find($id);
 if($userDemande){
 $this->getUser()->removeDemander($userDemande);
 $em->persist($this->getUser());
 $em->flush();
 }
}
$form = $this->createForm(AjoutAmiType::class);
if($request->isMethod('POST')){
$form->handleRequest($request);
if ($form->isSubmitted()&&$form->isValid()){
$ami = $userRepository->findOneBy(array('email'=>$form->get('email')->getData()));
if(!$ami){
$this->addFlash('notice','Ami introuvable');
return $this->redirectToRoute('app_amis');
}
else{
$this->getUser()->addDemander($ami);
$em->persist($this->getUser());
$em->flush();
$this->addFlash('notice','Invitation envoyée');
return $this->redirectToRoute('app_amis');
}

}
}
return $this->render('amis/amis.html.twig', [
'form'=>$form
]);
}
}
