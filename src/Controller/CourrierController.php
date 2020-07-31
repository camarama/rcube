<?php

namespace App\Controller;

use App\Service\MailerGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CourrierController
 * @package App\Controller
 * @Route("/admin/courrier", name="admin_courrier_")
 * @IsGranted("ROLE_USER")
 */
class CourrierController extends AbstractController
{
    /**
     * @var MailerGenerator
     */
    private $mailerGenerator;

    /**
     * CourrierController constructor.
     */
    public function __construct(MailerGenerator $mailerGenerator)
    {
        $this->mailerGenerator = $mailerGenerator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/accueil", name="accueil")
     */
    public function accueilCourrier()
    {
        return $this->render('admin/page_content/courrier/tableau_de_bord.html.twig');
    }

    /**
     * @Route("/nouveau", name="nouveau")
     */
    public function nouveauCourrier(Request $request)
    {
        $newmail = [];

        $form = $this->createFormBuilder($newmail)
            ->add('destinataire', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Entrer le mail du destinataire'
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Le nom de l\'entreprise destinataire'
                ]
            ])
            ->add('objet', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => 'Entrer l\'objet du mail'
                ]
            ])
            ->add('contenu', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $destinataire = $data['destinataire'];
            $entreprise = $data['entreprise'];
            $objet = $data['objet'];
            $contenu = $data['contenu'];

            $this->mailerGenerator->newMail($destinataire, $objet, $contenu, $entreprise);

            return $this->redirectToRoute('admin_accueil');
        }


        return $this->render('admin/page_content/courrier/nouveau.html.twig', [
            'newmail' => $form->createView()
        ]);
    }
}
