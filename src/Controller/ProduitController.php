<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\SearchProduitType;
use App\Repository\ProduitRepository;
use App\Service\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $prodrepo, Request $req): Response
    {  
        $produits = $prodrepo->ordonnation();

        // pagination des données
        $nombre = $prodrepo->getTotalArticle();
        $limit = 10;
        $page = (int)$req->query->get("page", 1);
       
 $form = $this->createForm(SearchProduitType::class);

       $form->handleRequest($req);
       if ($form->isSubmitted() &&$form->isValid()) {
        $produits= $prodrepo->search($form->get('mots')->getData());
   
       }else {
        $produits = $prodrepo->pagination($page, $limit);
       }
      
        return $this->render('produit/index.html.twig', [
            "limit"=>$limit,
            "nombre"=>$nombre,
            "page" => $page,
            'produits' => $produits,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/produit_new', name: 'app_produit_create',methods: ['GET', 'POST'])]
    public function create( City $city,  Request $req, ProduitRepository $prodrepo): Response
    {
        $prod = new Produit();
        $form= $this->createForm(ProduitType::class,$prod);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isvalid()) {
          $dep=  $form->get('departement')->getData();
          $dataVilles= $city->call($dep->getCode());
          $numcity= rand(1, count($dataVilles));
            //    recuperation image transmise
            // $images = $form->get('images')->getData();

            // // on boucle sur les images 
            // foreach ($images as $image) {
            //         // on analyse le type d'image:
            //         $newimage='';
            //         $extension = pathinfo( $image, PATHINFO_EXTENSION );
            //         if ($extension !=="png") {
            //             $newimage = imagecreatefrompng($image);
            //         }else{
            //             $newimage =$image;
            //         }
                   
            //     //new nom de fichier
            //     $fichier = md5(uniqid()) . '.' .'png';
            //     //copie dans dossier upload
            //     $image->move($this->getParameter('images_directory'), $fichier);
            //     // stockage du nom dans bdd, image stocké dans le disque
               
            //     $img = new Images();
            //     $img->setName($fichier);
            //     $prod->addImage($img);
            // }
            $prod->setVillecreation($dataVilles[$numcity]['nom']);
            $prodrepo->save($prod, true);
           return $this->redirectToRoute('app_produit');
        }
        return $this->render('produit/create.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/produit_show/{id}', name: 'app_produit_show',methods: ['GET', 'POST'])]
    public function show(ProduitRepository $prodrepo, $id): Response
    {
        $produit = $prodrepo->findOneBy(['id'=>$id]);
        // dd($produit);
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    

// edit
#[Route('/edit/{id}', name: 'app_produit_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Produit $prod,ProduitRepository $produitRepository,$id): Response
{
    $form = $this->createForm(ProduitType::class, $prod);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

    //     $images = $form->get('images')->getData();

    //     foreach ($images as $image) {
    //         // on analyse le type d'image:
    //         $newimage='';
    //         $extension = pathinfo( $image, PATHINFO_EXTENSION );
    //         if ($extension !=="png") {
    //             $newimage = imagecreatefrompng($image);
    //         }else{
    //             $newimage =$image;
    //         }
    //         dd($newimage);
    //     //new nom de fichier
    //     $fichier = md5(uniqid()) . '.' .  $newimage->guessExtension();
    //     //copie dans dossier upload
    //     $image->move($this->getParameter('images_directory'), $fichier);
    //     // stockage du nom dans bdd, image stocké dans le disque
       
    //     $img = new Images();
    //     $img->setName($fichier);
    //     $prod->addImage($img);
    // }
 $produitRepository->save($prod, true);

        return $this->redirectToRoute('app_produit', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('produit/edit.html.twig', [
        'produit' => $prod,
        'form' => $form,
    ]);
}






    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $prodrepos): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $prodrepos->remove($produit, true);
        }

        return $this->redirectToRoute('app_produit', [], Response::HTTP_SEE_OTHER);
    }







//  route suppression image
#[Route('/supprimer/image/{id}', name: 'app_produit_delete_image', methods: ['GET', 'POST'])]
public function deleteImage(Request $request, Images $images,EntityManagerInterface $entityManager)
{

    //    on récupère les donnée qui sont en json
    $data = json_decode($request->getContent(), true);

    // on travaille avec un token pour valider la nature de l'image
    if ($this->isCsrfTokenValid('delete' . $images->getId(),$request->request->get('_token'))) {
        $nomimage = $images->getName();
        //    on supprime physiquement le fichier image
        unlink($this->getParameter('images_directory') . '/' . $nomimage);


        // on cherche le entity manager pour remove et flusher pour supprimer de la base
        $entityManager->remove($images);
        $entityManager->flush();

        //  on répond en json

        return new JsonResponse(['success' => 1]);
    } else {
        new JsonResponse(['error' => 'token invalid'], 400);
    }
 
}



}
