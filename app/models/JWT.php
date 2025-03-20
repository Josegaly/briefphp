<?php
class JWT {
    private $secretKey = "ma_cle_secrete_tres_securisee"; // Clé secrète pour signer les tokens

    // Générer un token JWT
    public function generateToken($payload) {
        // Header : informations sur le type de token
        $header = [
            'alg' => 'HS256', // Algorithme de signature
            'typ' => 'JWT'    // Type de token
        ];
        $headerEncoded = $this->base64UrlEncode(json_encode($header));

        // Payload : données qu’on veut inclure (ex. : user_id, role_id)
        $payload['exp'] = time() + 3600; // Expiration dans 1 heure
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        // Signature : pour vérifier que le token n’a pas été modifié
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secretKey, true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        // Retourner le token complet : header.payload.signature
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    // Vérifier un token JWT
    public function verifyToken($token) {
        // Séparer les parties du token
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return ['success' => false, 'message' => "Token invalide : format incorrect."];
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        // Recréer la signature pour vérifier
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $this->secretKey, true)
        );
        if ($signatureEncoded !== $expectedSignature) {
            return ['success' => false, 'message' => "Token invalide : signature incorrecte."];
        }

        // Décoder le payload
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        if (!$payload || !isset($payload['exp'])) {
            return ['success' => false, 'message' => "Token invalide : payload corrompu."];
        }

        // Vérifier l’expiration
        if ($payload['exp'] < time()) {
            return ['success' => false, 'message' => "Token expiré."];
        }

        return ['success' => true, 'payload' => $payload];
    }

    // Encoder en Base64 URL-safe
    private function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    // Décoder en Base64 URL-safe
    private function base64UrlDecode($data) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}?>