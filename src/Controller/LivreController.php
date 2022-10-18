<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class LivreController extends AbstractController
{
    /**
     * @Route("/showlivres", name="showlivre")
     */
    public function showLivre(ManagerRegistry $doctrine)
    {   
        $repo= $doctrine->getRepository(Livre::class);
        $livres=$repo->findAll();
        
        return $this->render('livre/index.html.twig', [
            'livres'=>$livres
        ]);
    }
    /**
     * @route ("/modifLivre/{id}", name="modifLivre")
     * @Route("/ajoutLivre", name="ajoutLivre")
     */
    public function addLivre(ManagerRegistry $doctrine,Request $request,UserInterface $user, Livre $livres=null)
    {   
        if(!$livres){
            $livres= new Livre(); 
        }
       
        $form=$this->createForm(LivreType::class,$livres);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $livres->setUser($user);
            $om= $doctrine->getManager();
            $om->persist($livres);
            $om->flush();
            return $this->redirectToRoute("showlivre");
        }
        $mode=false;
        if($livres->getId() !== null){
            $mode=true;
        }
        
        return $this->render('livre/add.html.twig', [
            "formulaireLivre"=>$form->createView(),
            'mode'=>$mode
        ]);
    }
    /**
     * @Route("/supprimerLivre/{id}", name="delLivre")
     */
    public function del(ManagerRegistry $doctrine, Livre $livres)
    {
        $om=$doctrine->getManager();
        $om->remove($livres);
        $om->flush();
        return $this->redirectToRoute(('showlivre'));
    }
    /**
     * @Route("/categoryShowlivres/{id}", name="categoryLivre")
     */
    public function findLivreByCategory(ManagerRegistry $doctrine,Categorie $categorie,$id)
    {   
        $categorie= $doctrine->getRepository(Categorie::class);
        $livres=$categorie->findBooksByCategory($id);
        
        return $this->render('livre/categorie.html.twig', [
            'livres'=>$livres
        ]);
    }
}
