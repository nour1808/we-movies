<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_stars', [$this, 'renderStars']),
        ];
    }

    public function renderStars(float $voteAverage): string
    {
        $stars = round($voteAverage / 2); // Conversion pour avoir des étoiles sur 5
        $output = '';

        for ($i = 1; $i <= $stars; $i++) {
            $output .= '★'; // étoile pleine
        }

        for ($i = $stars + 1; $i <= 5; $i++) {
            $output .= '☆'; // étoile vide
        }

        return $output;
    }
}
