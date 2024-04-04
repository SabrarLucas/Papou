<?php

namespace App\Service;

class TokenGenerator
{
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');  // création et envoie du token 
    }
}
