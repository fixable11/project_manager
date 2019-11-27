<?php

declare(strict_types=1);

namespace App\Model\User\Service;

/**
 * Class ResetTokenizerFactory.
 */
class ResetTokenizerFactory
{
    /**
     * @param string $interval
     *
     * @return ResetTokenizer
     *
     * @throws \Exception
     */
    public function create(string $interval): ResetTokenizer
    {
        return new ResetTokenizer(new \DateInterval($interval));
    }
}