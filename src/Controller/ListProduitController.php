<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Listeproduit;
use App\Form\ListeproduitType;
use App\Repository\ProduitRepository;
use App\Repository\ListeproduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListProduitController extends AbstractController
{#[IsGranted('ROLE_USER')]
    #[Route('/listProduit', name: 'app_list_produit')]
    public function indexList(ProduitRepository $prod, SessionInterface $session,
    Request $req, ListeproduitRepository $listprod): Response
    {
        $list= $session->get("list",[]);
        $prod_inList=[];
        // $total=0;

        foreach($list as $id=>$qte){
            
            $produit = $prod->find($id); //list des id des articles du panier
           $prod_inList[]=[
               "produit"=>$produit,
               "qte"=>$qte,
           ] ;
        //    $total += ($article->getPrice()*$qte);
        // dd($prod_inList);
       
       
        }
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length=rand(5, 20);
        $string = '';
        for($i=0; $i<$length; $i++){
            $string .= $chars[rand(0, strlen($chars)-1)];
        }

       $newlistproduit= new Listeproduit();
        $form= $this->createForm(ListeproduitType::class,$newlistproduit);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isvalid()) {

            $newlistproduit-> setListe($prod_inList);
            $newlistproduit-> setNom($string);
            $time=new \DateTimeImmutable();
            $newlistproduit->setCreatedAt($time);
            $newlistproduit->setUsers($this->getUser());
        
            $listprod->save($newlistproduit, true);
       
           return $this->redirectToRoute('app_see_All', array('nom'=>$string)    );

        }
        return $this->render('list_produit/index.html.twig', [
            'list_Produit'=>$prod_inList,
            'form'=>$form->createView()
        ]);
    }


    // #[Route('/listProduitValidate', name: 'app_list_produit_validate')]


    #[IsGranted('ROLE_USER')]
    #[Route('/add/{id}', name: 'app_list_produit_add')]
    public function add(Produit $prod, SessionInterface $session){
        $id= $prod->getId();
        // dd($id);
        $list=$session->get("list",[]);
        if (!empty($list[$id])) {
            $list[$id]++; // si il existe deja dans panier qtéfin= qté+1;
        }
        else{
            $list[$id]=1; //n'existe pas alors on crée
        }
        //on sauvegarde dans la session

        $session->set("list",$list);
       return $this->redirectToRoute("app_list_produit");
    }

    #[Route('/remove/{id}', name: 'app_remove_list_produit')]
    public function remove(Produit $prod, SessionInterface $session){
        $id= $prod->getId();
        $list=$session->get("list",[]);
        if (!empty($list[$id])) {
            if ($list[$id]>1) {
                $list[$id]--;// si il existe deja dans la list qtéfin= qté-1;
            }else {
               unset($list[$id]);// si il existe deja dans la list qtéfin= 0;
            }
           
        }
      
        //on sauvegarde dans la session

        $session->set("list",$list);
        return $this->redirectToRoute("app_list_produit");
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/delete', name: 'app_delete_All')]
    public function deleteList(  SessionInterface $session)
    {
         $list=$session->get("list",[]); 
              unset($list);
        $session->set("list",[]);
       return $this->redirectToRoute("app_list_produit");
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/seeList/{nom}', name: 'app_see_All')]
    public function seelist(ListeproduitRepository $list, $nom):Response
    {
        $data=$list->findOneBy(['nom'=>$nom]);
        $nomlist=$data->getNom();
        $date=$data->getCreatedAt();
        // dd($date);
       $listcourse=[];
       $qteproduit=[];

       for ($i=0; $i < count($data->getListe()); $i++) {
        array_push($listcourse,$data->getListe()[$i]["produit"]->getNom()); 
        array_push( $qteproduit,$data->getListe()[$i]["qte"]);
       }

    //    dd($qteproduit);
        return $this->render('list_produit/see.html.twig', [
            'nom'=>$nom,
            'list_Produit'=>$listcourse,
            'qte_Produit'=>$qteproduit,
            
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/seeList/{nom}/download', name: 'app_data_course')]
    public function download(ListeproduitRepository $list,  SessionInterface $session, $nom):Response
    {

        $data=$list->findOneBy(['nom'=>$nom]);
       
       $listcourse=[];
       $qteproduit=[];

       for ($i=0; $i < count($data->getListe()); $i++) {
        array_push($listcourse,$data->getListe()[$i]["produit"]->getNom()); 
        array_push( $qteproduit,$data->getListe()[$i]["qte"]);
       }
        //on definit les options du pdf
        $options = new Options();
        //police par defaut
        $options->set('defaultFont', 'Arial');
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf();
        $context = stream_context_create(
            [
                'ssl' => [
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                    'allow_self_signed' => TRUE

                ]
            ]
        );
        $dompdf->setHttpContext($context);

        $html = $this->render('list_produit/seedownlaod.html.twig', [
            'nom'=>$nom,
            'list_Produit'=>$listcourse,
            'qte_Produit'=>$qteproduit,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $newstring= substr($nom, 0, 5);
       

        $fichier = 'malist : ' .$newstring ;
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        $list=$session->get("list",[]); 
        unset($list);
        $session->set("list",[]);


        return new Response();

        

            return  $this->render('list_produit/download.html.twig', [
                
            ]);
    }



}
