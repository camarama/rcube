<?php


namespace App\Controller;


use App\Repository\DevisRepository;
use App\Service\HtmlToPdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function showDevis($id, DevisRepository $devisRepo, HtmlToPdf $pdf)
    {
        $devis = $devisRepo->find($id);
//        dd($devis);
        if (!$devis)
        {
            $this->addFlash('danger', "Ouppsss, le devis demandé n'existe pas !");
            return $this->redirectToRoute('admin_commande_consultation');
        }

        $response = $pdf->showPdf($devis);

        return new Response(
            $response,
            200,
            [
                'content-Type' => 'application/pdf',
                'content-Disposition' => 'inline'
            ]
        );
    }

    /**
     * @param $id
     * @param DevisRepository $devisRepo
     * @Route("facture/{id}", name="facture")
     */
    public function createFacture($id, DevisRepository $devisRepo, Request $request)
    {
        $devis = $devisRepo->find($id);

        if (!$devis)
        {
            $this->addFlash('danger', "Ouppsss, le devis demandé n'existe pas !");
            return $this->redirectToRoute('admin_commande_consultation');
        }

//        dd($devis);
        $facture = ["message" => "Créer la facture du client"];
        $form = $this->createFormBuilder($facture)
            ->add('date', DateType::class)
            ->add('accompte', MoneyType::class, ['divisor' => 100])
            ->add('reduction', PercentType::class, [
                'label_format' => 'réduction',
                'scale' => 1,
                'type' => 'integer'
            ])
            ->add('créer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $info = $form->getData();
        }
        
        return $this->render('admin/page_content/devis/facture.html.twig', [
            'commande' => $devis,
            'facture' => $form->createView()
        ]);
    }
}