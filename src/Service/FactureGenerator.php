<?php

namespace App\Service;

use App\Repository\AdressesClientRepository;
use App\Repository\MaterielRepository;
use App\Repository\ProduitRepository;

class FactureGenerator
{
    /**
     * @var AdressesClientRepository
     */
    private $adressesClientRepo;
    /**
     * @var ProduitRepository
     */
    private $produitRepo;
    /**
     * @var MaterielRepository
     */
    private $materielRepo;

    /**
     * FactureGenerator constructor.
     */
    public function __construct(AdressesClientRepository $adressesClientRepo, ProduitRepository $produitRepo, MaterielRepository $materielRepo)
    {
        $this->adressesClientRepo = $adressesClientRepo;
        $this->produitRepo = $produitRepo;
        $this->materielRepo = $materielRepo;
    }

    public function facture($session)
    {
        $prestation = $session->get('prestation');
//        dd($session);
//        $generator = random_bytes(10);
        $adresse = $prestation['adresse'];
        $articles = [];
        $totalHT = 0;
        $totalTVA = 0;
        $produits = $this->produitRepo->findArray(array_keys($prestation));
        $enlevement = $this->adressesClientRepo->find($adresse['enlevement']);
        $facturation = $this->adressesClientRepo->find($adresse['factuartion']);

        foreach ($produits as $produit) {
            $articles['produits'][$produit->getNom()] = [
                'nom' => $produit->getNom(),
                'couleur' => $prestation[$produit->getId()][$produit->getNom()]['couleur'],
                'quantite' => $prestation[$produit->getId()][$produit->getNom()]['quantite']
            ];

            $materiels = $this->materielRepo->findByName(array_keys($prestation[$produit->getId()]['materiels']));
//            dd($materiels);
            for ($i = 0, $iMax = count($materiels); $i < $iMax; $i++) {
                if (array_key_exists($materiels[$i]->getDesignation(), $prestation[$produit->getId()]['materiels'])) {
                    if ($materiels[$i]->getDesignation() != "main d'oeuvre" and $materiels[$i]->getDesignation() != "nettoyage")
                        $quantite = $prestation[$produit->getId()]['materiels'][$materiels[$i]->getDesignation()];
                    else
                        if ($produit->getCategorie()->getId() != 1)
                            $quantite = ceil(($prestation[$produit->getId()]['materiels'][$materiels[$i]->getDesignation()] / 2));
                        else
                            $quantite = ceil(($prestation[$produit->getId()]['materiels'][$materiels[$i]->getDesignation()] / 30));

                    if ($materiels[$i]->getDesignation() != "main d'oeuvre" and $materiels[$i]->getDesignation() != "nettoyage")
                        $prixUnitaire = $materiels[$i]->getPrixUnitaire() + ($materiels[$i]->getPrixUnitaire() * 0.5);
                    else
                        $prixUnitaire = $materiels[$i]->getPrixUnitaire();

                    $prixHT = $quantite * $prixUnitaire;
                    $prixTTC = $prixHT / $materiels[$i]->getTva()->getMultiplicate();
                    $totalHT += $prixHT;


                    if (!isset($articles['tva'][$materiels[$i]->getTva()->getNom()]))
                        $articles['tva'][$materiels[$i]->getTva()->getNom()] = round($prixTTC - $prixHT, 2);
                    else
                        $articles['tva'][$materiels[$i]->getTva()->getNom()] += round($prixTTC - $prixHT, 2);

                    $totalTVA += round($prixTTC - $prixHT, 2);

                    $articles['produits'][$produit->getNom()]['materiels'][$materiels[$i]->getDesignation()] = [
                        'designation' => $materiels[$i]->getDesignation(),
                        'prix_unitaire' => $prixUnitaire,
                        'quantite' => $quantite,
                        'mesure' => $materiels[$i]->getMesure(),
                        'prixHT' => $prixHT,
                        'prixTTC' => round($prixTTC, 2),
                        'tva' => round($prixTTC - $prixHT, 2)
                    ];
                }
            }
        }

        $articles['adresses'] = [
            'enlevement' => [
                'tel' => $enlevement->getTelephone(),
                'rue' => $enlevement->getRue(),
                'cp' => $enlevement->getCp(),
                'ville' => $enlevement->getVille(),
                'pays' => $enlevement->getPays(),
                'complement' => $enlevement->getComplement()
            ],
            'facturation' => [
                'tel' => $facturation->getTelephone(),
                'rue' => $facturation->getRue(),
                'cp' => $facturation->getCp(),
                'ville' => $facturation->getVille(),
                'pays' => $facturation->getPays(),
                'complement' => $facturation->getComplement()
            ]
        ];

        $articles['totalHT'] = round($totalHT, 2);
        $articles['totalTTC'] = round($totalHT + $totalTVA, 2);
//        $articles['token'] = $generator;

        $prestation['devis'] = $articles;
//        dd($articles);
        $session->set('prestation', $prestation);
//        dd($session);
        return $articles;
    }
}