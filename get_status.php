<?php
// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auth_type = $_POST['auth_type'] ?? 'Bearer';
    $access_token = $_POST['access_token'] ?? '';
    $cache_control = $_POST['cache_control'] ?? 'no-cache';
    $transaction_id = $_POST['transaction_id'] ?? '';
    $user_account = $_POST['user_account'] ?? ''; // Nouveau champ
    $partner_name = $_POST['partner_name'] ?? 'TestMVola'; // Valeur par défaut

    // URL du statut de transaction MVola Sandbox
    $url = "https://devapi.mvola.mg/mvola/mm/transactions/type/merchantpay/1.0.0/status/" . $transaction_id;

    $headers = [
        "Authorization: $auth_type $access_token",
        "Cache-Control: $cache_control",
        "X-CorrelationID: " . uniqid(),
        "UserLanguage: FR",
        "UserAccountIdentifier: $user_account", // Obligatoire
        "partnerName: $partner_name",
        "Content-Type: application/json"
    ];

    // Initialisation de la requête GET
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => "GET"
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}
?>

<div class="container mt-5">
    <div class="card shadow p-4" style="border-radius: 15px; max-width: 700px; margin: auto;">
        <h4 class="text-center mb-3 text-primary">
            🪙 Consulter le Statut d’une Transaction MVola (Sandbox)
        </h4>
        <p class="text-center text-muted mb-4">
            Entrez le <strong>Transaction ID</strong>, votre <strong>Access Token</strong> et le numéro de compte pour vérifier le statut du paiement.
        </p>

        <form method="POST" class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Auth Type</label>
                <input type="text" name="auth_type" value="Bearer" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Access Token</label>
                <input type="text" name="access_token" placeholder="Collez ici votre access token" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Cache-Control</label>
                <input type="text" name="cache_control" value="no-cache" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Transaction ID</label>
                <input type="text" name="transaction_id" placeholder="Ex: c028cbab-feb7-431c-b750-29893ad71606" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Numéro de Compte (UserAccountIdentifier)</label>
                <input type="text" name="user_account" placeholder="Ex: msisdn;0343500004" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Partner Name</label>
                <input type="text" name="partner_name" value="TestMVola" class="form-control">
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-success px-4">
                    🔍 Vérifier le Statut
                </button>
            </div>
        </form>

        <?php if (isset($response)): ?>
            <hr>
            <div class="mt-3">
                <h5 class="text-secondary">📄 Résultat de la Requête</h5>
                <p><strong>Code HTTP :</strong> <?php echo $http_code; ?></p>
                <pre class="bg-light p-3 border rounded"><?php echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8'); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>
