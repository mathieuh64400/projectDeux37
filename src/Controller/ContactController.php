<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $req,MailerInterface $mailer): Response
    {
        $formcontact= $this->createForm(ContactType::class);
        $formcontact->handleRequest($req);
        if ($formcontact->isSubmitted()&&$formcontact->isValid()) 
        {
          $data=date("Y-m-d H:i:s");
          $email= (new TemplatedEmail())
          ->from($formcontact->get('email')->getData())
          ->to("mespetitsproduits@gmail.com")
          ->subject(" Sujet :".$formcontact->get('sujet')->getData())
          ->htmlTemplate('emails/contact_mail.html.twig')
          ->context([
            'date'=>$data,
            'sujet'=>$formcontact->get('sujet')->getData(),
            'mail'=>$formcontact->get('email')->getData(),
            'message'=>$formcontact->get('message')->getData()
          ]);
         $mailer->send($email);
          $this->addFlash('message','mesage bien envoyé');
          return $this->redirectToRoute('app_home');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $formcontact->createView(),
        ]);
    }
}
