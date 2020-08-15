<?php

namespace App\Controller;

use App\Entity\AdressesClient;
use App\Entity\Client;
use App\Form\AdresseType;
use App\Repository\AdressesClientRepository;
use App\Repository\CategorieRepository;
use App\Repository\ClientRepository;
use App\Repository\MaterielRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class DevisController
 * @package App\Controller
 *
 * @Route("/admin", name="admin_")
 * @IsGranted("ROLE_USER")
 */
class DevisController extends AbstractController
{
    /**
     * @Route("/presentation", name="presentation")
     */
    public function presentation(CategorieRepository $categorieRepo, ProduitRepository $produitRepo, Session $session)
    {
//        dd($session);
        /*$session->clear();
        die();*/
        $session->getFlashBag()->clear();
//        dd($session);

        return $this->render('devis/presentation.html.twig', [
            'categories' => $categorieRepo->findAll(),
            'produits' => $produitRepo->findAll(),
        ]);
    }

    /**
     * @param Session $session
     * @Route("/choix_produit/{id}", name="choix_produit")
     */
    public function choixProduit($id, ProduitRepository $produitRepo, MaterielRepository $materielRepo, Session $session)
    {
        if (!$session->has('prestation'))
            $session->set('prestation', []);

        return $this->render('devis/choix_produit.html.twig', [
            'produit' => $produitRepo->find($id),
            'materiels' => $materielRepo->findAll(),
        ]);
    }

    /**
     * @param $id
     * @param Session $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/ajout/produit/{id}", name="ajout_produit")
     */
    public function ajoutProduit($id, Session $session, Request $request, MaterielRepository $materielRepo, ProduitRepository $produitRepo)
    {
//        dd($request);
        if (!$session->has('prestation'))
            $session->set('prestation', []);

        $prestation = $session->get('prestation');
        $produit = $produitRepo->find($id);
        $main_oeuvre = $materielRepo->findOneBy(['designation' => "main d'oeuvre"]);
        $nettoyage = $materielRepo->findOneBy(['designation' => "nettoyage"]);
        $transport = $materielRepo->findOneBy(['designation' => "transport"]);

        if (array_key_exists($id, $prestation)) {
            if ($request->request->get('qte') != null && $request->request->get('couleur') != null) {
                $prestation[$id][$produit->getNom()]['quantite'] = $request->request->get('qte');
                $prestation[$id][$produit->getNom()]['couleur'] = $request->request->get('couleur');
//                $prestation[$id][$produit->getNom()]['nettoyage'] = $request->request->get('nettoyage');
                foreach ($request->request->get('fournitures') as $key => $value) {
                    if ($value != null) {
                        $prestation[$id]['materiels'][$main_oeuvre->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$nettoyage->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$transport->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$key] = $value;
                    }
                }
                $this->addFlash(
                    'success',
                    'Le produit a bien été modifié !'
                );
            }
        } else {
            if ($request->request->get('qte') != null && $request->request->get('couleur') != null) {
                $prestation[$id][$produit->getNom()]['quantite'] = $request->request->get('qte');
                $prestation[$id][$produit->getNom()]['couleur'] = $request->request->get('couleur');
//                $prestation[$id][$produit->getNom()]['nettoyage'] = $request->request->get('nettoyage');
                foreach ($request->request->get('fournitures') as $key => $value) {
                    if ($value != null) {
                        $prestation[$id]['materiels'][$main_oeuvre->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$nettoyage->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$transport->getDesignation()] = $request->request->get('qte');
                        $prestation[$id]['materiels'][$key] = $value;
                    }
                }
                $this->addFlash(
                    'success',
                    'Le produit a bien été ajouté à votre panier !'
                );
            } else {
                $prestation[$id][$produit->getNom()] = false;
            }
        }
//        dd($request);
        $session->set('prestation', $prestation);
//        dd($prestation);

        return $this->redirectToRoute('admin_panier');
    }

    /**
     * @Route("/panier", name="panier")
     */
    public function panier(Session $session, Request $request, ClientRepository $clientRepo,ProduitRepository $produitRepo, MaterielRepository $materielRepo)
    {
        if (!$session-> has('prestation'))
            $session->set('prestation', []);
        $prestation = $session->get('prestation');

//        dd(count($prestation));
        if (count($prestation) == 0)
            return $this->redirectToRoute('admin_presentation');

        $produits = $produitRepo->findArray(array_keys($prestation));

        foreach ($produits as $produit){
            $fournitures[$produit->getNom()] = $materielRepo->findByName(array_keys($prestation[$produit->getId()]['materiels']));
        }

        if ($request->isMethod('POST'))
        {
            $client = $clientRepo->find($request->request->get('client'));
            if ($client){
                $prestation['client'] = $client;
//                dd($client);
                $session->set('prestation', $prestation);

                return $this->redirectToRoute('admin_adresses_client', ['id' => $client->getId()]);
            } else{
                $this->addFlash("warning", "Choissisez un client pour continuer");

                return $this->redirectToRoute('admin_panier');
            }
        }

        return $this->render('devis/panier.html.twig', [
            'prestation' => $session->get('prestation'),
            'fournitures' => $fournitures,
            'produits' => $produits,
            'clients' => $clientRepo->findAll(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer($id, Session $session)
    {
//        dd($session);
        $prestation = $session->get('prestation');

        if (array_key_exists($id, $prestation)) {
            unset($prestation[$id]);
            $session->set('prestation', $prestation);
//            dd($session);
            $this->addFlash(
              'success',
              'Le produit est supprimé avec succès !'
            );
        }

        return $this->redirectToRoute('admin_panier');
    }

    /**
     * @Route("/supprimer_nettoyage/{id}", name="supprimer_nettoyage")
     */
    public function supprimerNettoyage($id, Session $session)
    {
        $prestation = $session->get('prestation');

        if (array_key_exists($id, $prestation)){
            unset($prestation[$id]['materiels']['nettoyage']);
            $session->set('prestation', $prestation);

            $this->addFlash('success', 'Le nettoyage a bien été supprimé');

        }
        return $this->redirectToRoute('admin_panier');
    }

    /**
     * @param $id
     * @Route("/adresse/{id}", name="adresse_suppression")
     */
    public function adresseSuppression($id, Session $session, EntityManagerInterface $manager, AdressesClientRepository $adressesClientRepo)
    {
        if ($session->has('prestation'))
            $prestation =  $session->get('prestation');
        else
            throw new NotFoundHttpException("Ouppss, vous n'êtes pas autorisé à faire cette manipulation !!!");

        $client = $prestation['client'];
        $adresse = $adressesClientRepo->find($id);

        $manager->remove($adresse);
        $manager->flush();
        
        return $this->redirectToRoute('admin_adresses_client', ['id' => $client->getId()]);
    }

    /**
     * @Route("/adresse_client/{id}", name="adresses_client")
     */
    public function adressesClient($id, Request $request, ClientRepository $clientRepo, EntityManagerInterface $manager)
    {
        $client = $clientRepo->find($id);

        $adresse = new AdressesClient();
        $form = $this->createForm(AdresseType::class, $adresse);
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $adresse->setClient($client);
                $manager->persist($adresse);
                $manager->flush();

                return $this->redirectToRoute('admin_adresses_client', ['id' => $id]);
            }
        }

        return $this->render('devis/adresses_client.html.twig', [
            'client' => $client,
            'form' => $form->createView()
        ]);
    }
}
