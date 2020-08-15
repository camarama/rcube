<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
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

    /**
     * @Route("/remise_zero_session", name="remize_zero_session")
     */
    public function remise_zero_session(Session $session)
    {
        if ($session->has('prestation'))
            $session->remove('prestation');
        elseif ($session->has('devis'))
            $session->remove('devis');
        else
            $this->addFlash(
                'success',
                'La session est propre !'
            );

        return $this->redirectToRoute('admin_accueil');
    }
}
