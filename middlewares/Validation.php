<?php
namespace Cisco\Sublime\middlewares;

class Validation
{
    public function validateField($request)
    {
        try {
            $post = $request["body"];
            // Vérifie si le corps de la requête est vide
            if (empty($post)) {
                http_response_code(400);
                echo json_encode(["error" => "Le corps de la requête est vide."]);
                exit;
            }

            // Validez et échappez chaque champ du corps de la requête
            foreach ($post as $key => $value) {
                if ($value === null || $value === "" || $value === "") {
                    http_response_code(400);
                    echo json_encode(["error" => "Le champ \"$key\" ne peut pas être vide."]);
                    exit;
                }

                // Échappez la valeur
                $post[$key] = $this->escapeHtml($value);
            }

            // Si toutes les validations passent, passez à la route suivante
            return $request;
        } catch (\Exception $error) {
            error_log($error->getMessage());
            http_response_code(500);
            echo "Une erreur s'est produite. Veuillez réessayer plus tard.";
            exit;
        }
    }

    // Fonction d'échappement HTML
    private function escapeHtml($unsafe)
    {
        return htmlspecialchars($unsafe, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
