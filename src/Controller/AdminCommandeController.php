<?php


namespace App\Controller;


use App\Entity\Devis;
use App\Entity\Facturation;
use App\Form\FactureType;
use App\Repository\DevisRepository;
use App\Repository\FacturationRepository;
use App\Service\FactureGenerator;
use App\Service\HtmlToPdf;
use App\Service\MailerGenerator;
use App\Service\ReferenceGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

/**
 * Class AdminCommandeController
 * @package App\Controller
 * @Route("/admin/commande", name="admin_commande_")
 *
 * @IsGranted("ROLE_USER")
 */
class AdminCommandeController extends AbstractController
{
    /**
     * @var ReferenceGenerator
     */
    private $referenceGenerator;
    /**
     * @var MailerGenerator
     */
    private $mailerGenerator;
    /**
     * @var HtmlToPdf
     */
    private $pdf;

    /**
     * AdminCommandeController constructor.
     */
    public function __construct(ReferenceGenerator $referenceGenerator, MailerGenerator $mailerGenerator, HtmlToPdf $pdf)
    {

        $this->referenceGenerator = $referenceGenerator;
        $this->mailerGenerator = $mailerGenerator;
        $this->pdf = $pdf;
    }

    /**
     * @Route("/consultation", name="consultation")
     */
    public function consultation(DevisRepository $devisRepo)
    {
        return $this->render('admin/page_content/devis/consultation.html.twig', [
            'commandes' => $devisRepo->findAll()
        ]);
    }

    /**
     * @param $id
     * @param DevisRepository $devisRepo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/details/{id}", name="details")
     */
    public function showDevis($id, DevisRepository $devisRepo)
    {
        $devis = $devisRepo->find($id);
//        dd($devis);
        if (!$devis)
        {
            $this->addFlash('danger', "Ouppsss, le devis demandé n'existe pas !");
            return $this->redirectToRoute('admin_commande_consultation');
        }

        $response = $this->pdf->showPdf($devis);

        return new Response(
            $response,
            200,
            [
                'content-Type' => 'application/pdf',
                'content-Disposition' => 'inline'
            ]
        );
    }

    public function prepareFacture($facture, $facturation, $solde, $devis, $em)
    {
        $facture->setDate(new \DateTime('now'));
        $facture->setAcompte($facturation->getAcompte());
        $facture->setEcheance(new \DateTime('+30 day'));
        $facture->setSolde($solde);
        $facture->setDevis($devis);
        $facture->setReference(0);
        $facture->setValider(0);

        $em->persist($facture);
        $em->flush();

        return new Response($facture->getId());
    }


    /**
     * @param $id
     * @param DevisRepository $devisRepo
     * @Route("/new_facture/{id}", name="new_facture")
     */
    public function createFacture($id, DevisRepository $devisRepo, Request $request, EntityManagerInterface $em, FacturationRepository $facturationRepo)
    {
        $devis = $devisRepo->find($id);

        if (!$devis)
        {
            $this->addFlash('danger', "Ouppsss, le devis demandé n'existe pas !");
            return $this->redirectToRoute('admin_commande_consultation');
        }

//        dd($devis);
        $facture = new Facturation();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $facturation = $form->getData();
            $solde = $devis->getPrestation()['totalTTC'] - $facturation->getAcompte();
            $prepareFacture = $this->prepareFacture($facture, $facturation, $solde, $devis, $em);
            $facture = $facturationRepo->find($prepareFacture->getContent());

            if (!$facture || $facture->getValider() == true)
                throw $this->createNotFoundException('La facture n\'existe pas !');

            $facture->setValider(1);

            $facture->setReference($this->referenceGenerator->newRefFacture());

            $em->flush();

            return $this->redirectToRoute('admin_commande_factures');
        }
        
        return $this->render('admin/page_content/devis/new_facture.html.twig', [
            'facture' => $form->createView()
        ]);
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function showFactures(FacturationRepository $facturationRepo)
    {
        return $this->render('admin/page_content/devis/facture.html.twig', [
            'commandes' => $facturationRepo->findAll()
        ]);
    }

    /**
     * @Route("/envoi_facture_client/{id}", name="envoi_facture_client")
     */
    public function envoiFactureClient($id, FacturationRepository $facturationRepo)
    {
        $facture = $facturationRepo->findByDevis($id);

        $this->mailerGenerator->sendFacture($facture);

        return $this->redirectToRoute('admin_commande_factures');
    }

    /**
     * @param $id
     * @Route("/facture_pdf/{id}", name="facture_pdf")
     */
    public function pdfFacture($id, FacturationRepository $facturationRepo)
    {
        $facture = $facturationRepo->findByDevis($id);
        $commande = $this->pdf->showFacturePdf($facture);

        $response = new Response($commande);
        $response->headers->set('content-type', 'application/pdf');

        return $response;
    }


    /**
     * @param $id
     * @param FacturationRepository $facturationRepo
     * @param Request $request
     * @param EntityManagerInterface $em
     * @Route("/payement_facture/{id}", name="payement_facture")
     */
    public function payementFacture($id, FacturationRepository $facturationRepo, Request $request, EntityManagerInterface $em)
    {

        $facture = $facturationRepo->find($id);
//        dd($facture);
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $payement = $form->getData();
//            dd($payement->getAcompte());
            $solde = $facture->getSolde() - $payement->getAcompte();
//            dd($solde);
            $facture->setSolde($solde);

            $em->persist($facture);
            $em->flush();

            return $this->redirectToRoute('admin_commande_factures');
        }

        return $this->render('admin/page_content/devis/payement_facture.html.twig', [
            'payement' => $form->createView()
        ]);
    }
}