<?php


namespace App\Service;



use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HtmlToPdf
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Pdf
     */
    private $snappy;

    /**
     * HtmlToPdf constructor.
     */
    public function __construct(Environment $twig, Pdf $snappy)
    {
        $this->twig = $twig;
        $this->snappy = $snappy;
    }

    public function showPdf($devis)
    {
        $html = $this->twig->render('pdf/html_to_pdf.html.twig', ['commande' => $devis]);
        $header = $this->twig->render('pdf/pdf_components/header.html.twig');
        $footer = $this->twig->render('pdf/pdf_components/footer.html.twig');

        $pdf = $this->snappy->getOutputFromHtml($html, [
            'header-html' => $header,
            'footer-html' => $footer,
        ]);

        return $pdf;
    }
}