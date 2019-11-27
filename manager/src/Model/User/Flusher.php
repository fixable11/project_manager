<?php

declare(strict_types=1);

namespace App\Model\User;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}