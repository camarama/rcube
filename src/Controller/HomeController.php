<?php

namespace App\Controller;

use App\Entity\AdressesClient;
use App\Entity\Client;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailerGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 *
 * @Route("/", name="app_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(Request $request, MailerGenerator $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                /* Appel du service d'envoi de mail */
                $mailer->contactMessage($contact);

                $this->addFlash(
                  'success',
                    'Merci pour votre message, nous y répondrons dans les plus brefs délais.'
                );

                return $this->redirect($this->generateUrl('app_accueil').'#contact');
            }
        }

        return $this->render('home/accueil.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/rdv", name="prise_rdv")
     */
    public function prise_rdv(Request $request, EntityManagerInterface $manager, MailerGenerator $mailer)
    {
        $client = new Client();
        $adresse = new AdressesClient();
        $rdv = [];

        $form = $this->createFormBuilder($rdv)
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre entreprise'
                ]
            ])
            /*->add('siret', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Siret de votre entreprise'
                ]
            ])*/
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre adresse mail'
                ]
            ])
            ->add('tel', TelType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre numéro de téléphone'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Numéro et nom de rue'
                ]
            ])
            ->add('region', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Region'
                ]
            ])
            ->add('cp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Code Postal'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Ville'
                ]
            ])
            ->add('pays', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Pays'
                ]
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control bg-light datetime',
                    'placeholder' => 'Choisissez une date et une heure'
                ],
                'format' => 'dd/mm/yyyy H:m',

            ])
            ->add('note', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Votre message',
                    'rows' => 5
                ]
            ])
            ->getForm();

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $demande = $form->getData();
//                dd($demande);
                $client->setDesignation($demande['entreprise'])
                        ->setEmail($demande['email'])
//                        ->setSiret($demande['siret'])
                ;
//                $manager->persist($client);

                $adresse->setClient($client)
                        ->setTelephone($demande['tel'])
                        ->setComplement($demande['note'])
                        ->setRue($demande['adresse'])
                        ->setCp($demande['cp'])
                        ->setVille($demande['ville'])
                        ->setPays($demande['pays'])
                ;
                $manager->persist($adresse);

                $manager->flush();

                $mailer->prendreRdv($demande);

                $this->addFlash(
                    'success',
                    'Votre Rendez_vous à été pris en compte, 
                    vous recevrez une confirmation par email dans les minutes qui suivent !'
                );

                return $this->redirectToRoute('app_accueil');
            }
        }

        return $this->render('home/demande_rdv.html.twig', [
            'contact' => $form->createView()
        ]);
    }
}