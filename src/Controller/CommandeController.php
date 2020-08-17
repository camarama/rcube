<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Repository\DevisRepository;
use App\Service\FactureGenerator;
use App\Service\MailerGenerator;
use App\Service\ReferenceGenerator;
use App\Service\StoreInDataBase;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommandeController
 * @package App\Controller
 *
 * @Route("/admin", name="admin_")
 * @IsGranted("ROLE_USER")
 */
class CommandeController extends AbstractController
{
    /**
     * @var FactureGenerator
     */
    private $factureGenerator;
    /**
     * @var ReferenceGenerator
     */
    private $referenceGenerator;
    /**
     * @var MailerGenerator
     */
    private $mailer;
    /**
     * @var StoreInDataBase
     */
    private $storeInDataBase;

    /**
     * CommandeController constructor.
     */
    public function __construct(FactureGenerator $factureGenerator, ReferenceGenerator $referenceGenerator, MailerGenerator $mailer, StoreInDataBase $storeInDataBase)
    {
        $this->factureGenerator = $factureGenerator;
        $this->referenceGenerator = $referenceGenerator;
        $this->mailer = $mailer;
        $this->storeInDataBase = $storeInDataBase;
    }

    public function setAdresseOnSession($request)
    {
        $session = $request->getSession();
//        dd($session);
        if ($session->has('prestation'))
            $prestation = $session->get('prestation');
        else
            throw new NotFoundHttpException('Ouppss, mauvaise manip !!!');

        $adresse_enlevement = $request->request->get('enlevement');
        $adresse_facturation = $request->request->get('facturation');
        if ($adresse_enlevement != null && $adresse_facturation != null) {
            $prestation['adresse']['enlevement'] = $adresse_enlevement;
            $prestation['adresse']['factuartion'] = $adresse_facturation;
            $this->addFlash(
                'success',
                'Vos adresses ont bien été ajouter à votre commande !'
            );
        } else {
            return $this->redirectToRoute('admin_adresses_client', [
                'id' => $prestation['client']->getId()
            ]);
        }
        $session->set('prestation', $prestation);

        return $this->redirectToRoute('admin_commande');
    }


    /**
     * @Route("/commande", name="commande")
     */
    public function commande(Request $request)
    {
//        dd($request);
        if ($request->isMethod('POST'))
        $this->setAdresseOnSession($request);

        $session = $request->getSession();
        if (!$session->has('prestation'))
            throw $this->createNotFoundException("Il n'y a aucune commande dans votre panier !");

        /* Appel du service pour enregister toute la commande dans la session */
        $this->factureGenerator->facture($session);

        // Appel du service pour enregistrer la commande dans la base de données
        $this->storeInDataBase->prepareCommande();

        $devis = $session->get('devis');


        return $this->render('devis/commande.html.twig', [
            'commande' => $session->get('prestation'),
            'devis' => $devis
        ]);
    }

    /**
     * @param Session $session
     * @param DevisRepository $devisRepo
     * @param $id
     * @Route("/attente_validation/{id}", name="attente_validation")
     */
    public function attenteValidationCommande($id, Session $session, DevisRepository $devisRepo)
    {
        $devis = $devisRepo->find($id);

        /* Appel du service d'envoi de mail */
        $this->mailer->sendContract($devis);

        $session->remove('prestation');
        $session->remove('devis');
        $session->getFlashBag()->clear();

        $this->addFlash(
            'success',
            'Votre commande est en attente de validation, le devis est envoyé au client !'
        );
//        dd($session);
        return $this->redirectToRoute('admin_accueil');
    }

    /**
     * @param Session $session
     * @param EntityManagerInterface $manager
     * @param DevisRepository $devisRepo
     * @param Devis $devis
     * @throws \Exception
     * @Route("/validation/{id}", name="validation")
     */
    public function validationCommande($id, Session $session, EntityManagerInterface $manager, DevisRepository $devisRepo)
    {
        $devis = $devisRepo->find($id);

        if (!$devis || $devis->getValider() == 1)
            throw $this->createNotFoundException("La commande n'existe pas !!!");

        $devis->setValider(1);

        /* Appel du service pour générer la reference du devis pour la comptabilité */
        $devis->setReference($this->referenceGenerator->newReference());

        $manager->flush();

        /* Appel du service d'envoi de mail */
        $this->mailer->sendContract($devis);

        $session->remove('prestation');
        $session->remove('devis');
        $session->getFlashBag()->clear();

        $this->addFlash(
            'success',
            'Votre commande a été valider avec succès et le devis est envoyé au client.'
        );
//        dd($session);
        return $this->redirectToRoute('admin_accueil');
    }

    /**
     * @Route("/suppression_commande", name="suppression_commande")
     * @IsGranted("ROLE_ADMIN")
     */
    public function suppressionCommande(Session $session)
    {
        $session->clear();
        $session->getFlashBag()->clear();

        $this->addFlash(
            'danger',
            'Votre devis vient d\'etre supprimer avec succès !'
        );

        return $this->redirectToRoute('admin_accueil');
    }
}
