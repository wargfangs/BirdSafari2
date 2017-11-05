<?php

namespace Birds\ObservationsBundle\ValidateData;

class Validator
{
    /**
     * Vérifie si une heure
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < 50;
    }
}