<?php

namespace App\Service;

use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

class MailerGenerator
{
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
    public function __construct(Environment $environment, HtmlToPdf $htmlToPdf, ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->environment = $environment;
        $this->htmlToPdf = $htmlToPdf;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    public function transport()
    {
        $transport = (new \Swift_SmtpTransport('mail.infomaniak.com', 587))
            ->setUsername('contact@plasticrcube.fr')
            ->setPassword('Citebiagui1')
        ;

        return $transport;
    }

    public function sendContract($devis)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);
//        dd($devis);
        $pdf = $this->htmlToPdf->showPdf($devis); /* Appel du service de generation de Html to pdf */
        $piece_jointe = (new \Swift_Attachment())
            ->setFilename(sprintf('devis-%s-%s.pdf', $devis->getClient()->getDesignation(), date('dmY')))
            ->setContentType('application/pdf')
            ->setBody($pdf)
        ;

        $email = (new \Swift_Message('Votre demande de devis'))
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
            ->setTo([$devis->getClient()->getEmail() => $devis->getClient()->getDesignation()])
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

        $mailer->send($email);
        /*if (!$this->mailer->send($email, $failures)){
            echo "Failures: ";
            print_r($failures);
        }*/
    }

    public function sendFacture($facture)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);
        $pdf = $this->htmlToPdf->showFacturePdf($facture); /* Appel du service de generation de Html to pdf */
        $piece_jointe = (new \Swift_Attachment())
            ->setFilename(sprintf('devis-%s-%s.pdf', $facture[0]->getDevis()->getClient()->getDesignation(), date('dmY')))
            ->setContentType('application/pdf')
            ->setBody($pdf)
        ;

        $email = (new \Swift_Message('Facture réparation emballages plastiques'))
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
            ->setTo([$facture[0]->getDevis()->getClient()->getEmail() => $facture[0]->getDevis()->getClient()->getDesignation()])
            ->setBcc(['mohamedcra@yahoo.fr' => 'Med']);
        $img = $email->embed(\Swift_Image::fromPath('home/img/logo_180.jpg'));
        $email->setBody(
            $this->environment->render(
                'emails/mail_accompagnement_facture.html.twig', [
                    'devis' => $facture[0],
                    'logo' => $img
                ]
            ),
            'text/html'
        );
        $email->attach($piece_jointe);

        $mailer->send($email);
    }

    public function contactMessage($contact)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message('Formulaire de contact' . ' : ' . $contact->getObjet()))
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
            ->setTo(['info@plasticrcube.fr' => 'PLASTIC R³'])
            ->setBcc('mohamedcra@yahoo.fr')
            ->setBody(
                $this->environment->render('emails/contact_form.html.twig',
                    ['contact' => $contact]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }

    public function prendreRdv($demande)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);
        $notification = (new \Swift_Message('Demande de RDV pour devis' . ' : ' . $demande['entreprise']))
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
            ->setTo(['info@plasticrcube.fr' => 'PLASTIC R³'])
            ->setBcc('mohamedcra@yahoo.fr')
            ->setBody(
                $this->environment->render('emails/prise_de_rdv.html.twig',
                    ['demande' => $demande]
                ),
                'text/html'
            )
        ;
        $mailer->send($notification);
    }

    public function sendResetPassword($user, $resetToken)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);
        $email = (new \Swift_Message('Réinitialisation de votre mot de passe'))
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->environment->render('reset_password/email.html.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]),
                'text/html'
            )
        ;

        $mailer->send($email);
    }
    
    public function newMail($destinataire, $objet, $contenu, $entreprise)
    {
        $transport = $this->transport();
        $mailer = new \Swift_Mailer($transport);
        $newmail = (new \Swift_Message())
            ->setFrom(['contact@plasticrcube.fr' => 'PLASTIC R³'])
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

        $mailer->send($newmail);
    }
}