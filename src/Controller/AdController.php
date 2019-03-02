<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
    
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     *
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager){
        $ad= new Ad();

        $form= $this->createForm(AnnonceType:: class, $ad);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }
            //$manager = $this->getDoctrine()->getManager(); dépendance!
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
               'success',
               "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistré !"
            );


            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
           
        }

        return $this-> render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/ads/{slug}/edit", name = "ads_edit")
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager){
        $form= $this->createForm(AnnonceType:: class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }
            //$manager = $this->getDoctrine()->getManager(); dépendance!
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
               'success',
               "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );


            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
           
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
             'ad' => $ad
        ]);


    }

    /**
     * Permet d'afficher une seule annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show($slug, Ad $ad){ //pas obligé de mettre le slug
        //je récupère l'annonce qui correspond au slug
        //$ad = $repo->findOneBySlug($slug); et on enlève AdRepository $repo des param

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

}
