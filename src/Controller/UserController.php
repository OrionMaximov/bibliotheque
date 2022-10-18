<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(ManagerRegistry $doctrine,Request $request,UserPasswordEncoderInterface $encoder,SluggerInterface $slugger)
    {   
        $user= new User();
        $form= $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $hash= $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            /**
             * @var UploadedFile $imageFile
             */
            $imageFile=$form->get('avatar')->getData();
            if($imageFile){
                $originalFilename= pathinfo($imageFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename=$slugger->slug($originalFilename);
                $newFilename= $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try{
                    $imageFile->move($this->getParameter('dossierImages'),$newFilename);
                }catch(FileException $e){
                    $e->getMessage();
                }
                $user->setAvatar($newFilename);
            }
            $om=$doctrine->getManager();
            $om->persist($user);
            $om->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('user/add.html.twig', [
                "formulaireInscription"=>$form->createView()
        ]);
    }
}
