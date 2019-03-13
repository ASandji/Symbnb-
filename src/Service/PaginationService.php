<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class PaginationService{

    private $entityClass;
    private $limit = 10 ; 
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath){

        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath;
    }

    public function setTemplatePath(){
        $this->templatePath = $templatePath;
    }

    public function getTemplatePath(){
        return $this->templatePath;

    }

    public function setRoute($route){
        $this->route = $route;

        return $this;
    }

    public function getRoute(){
        return $this->route;

    }

    public function display(){
        $this->twig->display('admin/partials/pagination.html.twig', [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception ("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        //1.connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        //2. faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;

    }

    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception ("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        //1. calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        //2. demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit , $offset);

        //3. Renvoyer les éléments en question
        return $data;
    }

    public function setPage($page){
        $this->currentPage = $page ;

        return $this;
    }

    public function getPage(){
        return $this->currentPage ;
    }

    public function setLimit(){
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }


}