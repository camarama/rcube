<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 *
 * @Route("/admin", name="admin_")
 *
 * @IsGranted("ROLE_USER")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function tableauDeBord()
    {
        return $this->render('admin/page_content/admin_accueil.html.twig');
    }
}
