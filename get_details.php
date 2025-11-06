<?php
// Empêche l’accès direct
if (!defined('OC_ADMIN')) exit('No direct access allowed.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Champs du formulaire
    $auth_type = $_POST['auth_type'] ?? 'Bearer';
    $access_token = trim($_POST['access_token'] ?? '');
    $cache_control = $_POST['cache_control'] ?? 'no-cache';
    $user_account = trim($_POST['user_account'] ?? '');
    $partner_name = trim($_POST['partner_name'] ?? 'TestMVola');

    // ID de transaction fixe demandé
    $transaction_id = "658759649";

    if (empty($access_token) || empty($user_account)) {
        $response = json_encode(["error" => "Veuillez remplir tous les champs requis."]);
        $http_code = 400;
    } else {
        // URL pour obtenir les détails de transaction
        $url = "https://devapi.mvola.mg/mvola/mm/transactions/type/merchantpay/1.0.0/" . $transaction_id;

        // Préparer les en-têtes HTTP
        $headers = [
            "Authorization: $auth_type $access_token",
            "Version: 1.0",
            "X-CorrelationID: " . uniqid(),
            "UserLanguage: FR",
            "UserAccountIdentifier: $user_account",
            "partnerName: $partner_name",
            "Cache-Control: $cache_control",
            "Content-Type: application/json"
        ];

        // Exécuter la requête GET
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $response = json_encode(["error" => curl_error($ch)]);
            $http_code = 500;
        }

        curl_close($ch);
    }
}
?>

<div class="container mt-5">
    <div class="card shadow p-4" style="border-radius: 15px; max-width: 750px; margin: auto;">
        <h4 class="text-center mb-3 text-primary">
            🪙 Détails d’une Transaction MVola (Sandbox)
        </h4>
        <p class="text-center text-muted mb-4">
            Cette page affiche les informations de la transaction <strong>#658759649</strong>.
        </p>

        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Auth Type</label>
                <input type="text" name="auth_type" value="Bearer" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Cache-Control</label>
                <input type="text" name="cache_control" value="no-cache" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Access Token</label>
                <input type="text" name="access_token" placeholder="Collez ici votre access token" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Numéro MVola (UserAccountIdentifier)</label>
                <input type="text" name="user_account" placeholder="Ex: msisdn;0343500004" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Partner Name</label>
                <input type="text" name="partner_name" value="TestMVola" class="form-control">
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-4">
                    🔍 Voir les Détails
                </button>
            </div>
        </form>

        <?php if (isset($response)): ?>
            <hr class="my-4">
            <div class="mt-3">
                <h5 class="text-secondary">📄 Résultat de la Requête</h5>
                <p><strong>Code HTTP :</strong> <?php echo htmlspecialchars($http_code); ?></p>
                <pre class="bg-light p-3 border rounded" style="max-height: 400px; overflow:auto;"><?php echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8'); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>
