<?php

namespace App\Service;

use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

class MailerGenerator
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var HtmlToPdf
     */
    private $htmlToPdf;

    private $resetPasswordHelper;

    /**
     * MailerGenerator constructor.
     */
    public function __construct(\Swift_Mailer $mailer, Environment $environment, HtmlToPdf $htmlToPdf, ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
        $this->htmlToPdf = $htmlToPdf;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    public function sendContract($devis)
    {
        $pdf = $this->htmlToPdf->showPdf($devis); /* Appel du service de generation de Html to pdf */
        $piece_jointe = (new \Swift_Attachment())
            ->setFilename(sprintf('devis-%s-%s.pdf', $devis->getClient()->getDesignation(), date('dmY')))
            ->setContentType('application/pdf')
            ->setBody($pdf)
        ;

        $email = (new \Swift_Message('Votre demande de devis'))
            ->setFrom(['mamedcra@gmail.com' => 'PLASTIC R³'])
            ->setTo(['mohamedcra@gmail.com' => $devis->getClient()->getDesignation()])
            ->setBcc(['mohamedcra@yahoo.fr' => 'Med']);
        $img = $email->embed(\Swift_Image::fromPath('home/img/logo_180.jpg'));
        $email->setBody(
            $this->environment->render(
                'emails/mail_accompagnement_contrat.html.twig', [
                    'devis' => $devis,
                    'logo' => $img
                ]
            ),
            'text/html'
        );
        $email->attach($piece_jointe);

        $this->mailer->send($email);
        /*if (!$this->mailer->send($email, $failures)){
            echo "Failures: ";
            print_r($failures);
        }*/
    }

    public function contactMessage($contact)
    {
        $message = (new \Swift_Message('Formulaire de contact' . ' : '.$contact->getObjet()))
            ->setFrom([$contact->getEmail() => $contact->getEntreprise()])
            ->setTo('mohamedcra@yahoo.fr')
//            ->setBcc('mohamedcra@yahoo.fr')
            ->setBody(
                $this->environment->render('emails/contact_form.html.twig',
                    ['contact' => $contact]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    public function prendreRdv($demande)
    {
        $notification = (new \Swift_Message('Demande de RDV pour devis' ))
            ->setFrom([$demande['email'] => $demande['entreprise']])
            ->setTo('mohamedcra@yahoo.fr')
            ->setBody(
                $this->environment->render('emails/prise_de_rdv.html.twig',
                    ['demande' => $demande]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($notification);
    }

    public function sendResetPassword($user, $resetToken)
    {
        $email = (new \Swift_Message('Réinitialisation de votre mot de passe'))
            ->setFrom(['mamedcra@gmail.com' => 'PLASTIC R³'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->environment->render('reset_password/email.html.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]),
                'text/html'
            )
        ;

        $this->mailer->send($email);
    }
    
    public function newMail($destinataire, $objet, $contenu, $entreprise)
    {
        $newmail = (new \Swift_Message())
            ->setFrom(['mamedcra@gmail.com' => 'PLASTIC R³'])
            ->setTo([$destinataire => $entreprise])
            ->setSubject($objet);
        $img = $newmail->embed(\Swift_Image::fromPath('home/img/logo_180.jpg'));
        $newmail->setBody(
                $this->environment->render('emails/nouveau_mail.html.twig', [
                    'contenu' => $contenu,
                    'logo' => $img
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($newmail);
    }
}