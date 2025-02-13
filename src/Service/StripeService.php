<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeService
{
    private string $stripeSecretKey;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->stripeSecretKey= $parameterBag->get('env(STRIPE_SECRET_KEY)');
    }

    public function getStripeSecretKey(): string
    {
        return $this->stripeSecretKey;
    }
}

