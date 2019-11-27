<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use App\Model\User\Entity\User\Email;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ConfirmTokenSender
{
    private $mailer;
    private $twig;

    private $from;

    /**
     * ConfirmTokenSender constructor.
     *
     * @param Swift_Mailer $mailer Mailer.
     * @param Environment  $twig   The Twig configuration.
     * @param array        $from   Set the from address of this message.
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    /**
     * @param Email  $email
     * @param string $token
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(Email $email, string $token): void
    {
        $message = (new Swift_Message('Sig Up Confirmation'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]), 'text/html');
        if (! $this->mailer->send($message)) {
            throw new RuntimeException('Unable to send message.');
        }
    }
}