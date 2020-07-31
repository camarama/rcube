<?php


namespace App\Service;

use App\Repository\DevisRepository;

class ReferenceGenerator
{
    /**
     * @var DevisRepository
     */
    private $devisRepo;

    /**
     * ReferenceGenerator constructor.
     */
    public function __construct(DevisRepository $devisRepo)
    {

        $this->devisRepo = $devisRepo;
    }

    public function newReference()
    {
        $reference = $this->devisRepo->findOneBy(['valider' => 1], ['id' => 'DESC'], 1, 1);
//        dd($reference);
        if (!$reference)
            return 1;
        else
            return $reference->getReference() + 1;
    }
}