<?php
if (!defined('ABS_PATH')) exit('No direct access allowed');

$auth_type = $access_token = $cache_control = $amount = $payer = $receiver = $requestRef = $description = '';
$response = '';
$httpCode = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth_type    = $_POST['auth_type'] ?? '';
    $access_token = $_POST['access_token'] ?? '';
    $cache_control = $_POST['cache_control'] ?? '';
    $amount       = $_POST['amount'] ?? '';
    $payer        = $_POST['payer'] ?? '';
    $receiver     = $_POST['receiver'] ?? '';
    $requestRef   = $_POST['requestRef'] ?? '';
    $description  = $_POST['description'] ?? '';

    $url = "https://devapi.mvola.mg/mvola/mm/transactions/type/merchantpay/1.0.0/";

    $headers = [
        "Authorization: $auth_type $access_token",
        "Version: 1.0",
        "X-CorrelationID: " . uniqid(),
        "UserLanguage: fr",
        "UserAccountIdentifier: msisdn;$payer",
        "PartnerName: TestMvola",
        "Content-Type: application/json",
        "Cache-Control: $cache_control"
    ];

    $body = [
        "amount" => $amount,
        "currency" => "Ar",
        "descriptionText" => $description,
        "requestingOrganisationTransactionReference" => $requestRef,
        "requestDate" => gmdate("Y-m-d\TH:i:s.000\Z"),
        "originalTransactionReference" => uniqid(),
        "debitParty" => [["key" => "msisdn", "value" => $payer]],
        "creditParty" => [["key" => "msisdn", "value" => $receiver]],
        "metadata" => [
            ["key" => "partnerName", "value" => "TestMvola"],
            ["key" => "fc", "value" => "USD"],
            ["key" => "amountFc", "value" => "1"]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($body)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
}
?>

<style>
.mvola-container {
    max-width: 650px;
    margin: 25px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 30px 35px;
    font-family: Arial, sans-serif;
}
.mvola-container h2 {
    text-align: center;
    color: #007bff;
    font-weight: bold;
    margin-bottom: 20px;
}
.mvola-container p {
    text-align: center;
    color: #555;
    margin-bottom: 30px;
}
.form-group {
    margin-bottom: 18px;
}
label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
    color: #333;
}
input.form-control {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}
button {
    width: 100%;
    background: #007bff;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}
button:hover {
    background: #0056b3;
}
.mvola-result {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 25px;
    border: 1px solid #ddd;
}
</style>

<div class="mvola-container">
    <h2>💳 Initier un Paiement MVola (Sandbox)</h2>
    <p>Remplissez le formulaire ci-dessous pour tester une transaction <strong>merchantpay</strong> dans l'environnement MVola Sandbox.</p>

    <form method="post">
        <div class="form-group">
            <label>Auth Type</label>
            <input type="text" name="auth_type" class="form-control" value="<?php echo htmlspecialchars($auth_type ?: 'Bearer'); ?>" required>
        </div>

        <div class="form-group">
            <label>Access Token</label>
            <input type="text" name="access_token" class="form-control" placeholder="Collez ici votre access token" value="<?php echo htmlspecialchars($access_token); ?>" required>
        </div>

        <div class="form-group">
            <label>Cache-Control</label>
            <input type="text" name="cache_control" class="form-control" value="<?php echo htmlspecialchars($cache_control ?: 'no-cache'); ?>">
        </div>

        <div class="form-group">
            <label>Description du Paiement</label>
            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($description ?: 'Client test 0349262379 Tasty Plastic Bacon'); ?>" required>
        </div>

        <div class="form-group">
            <label>Montant (Ar)</label>
            <input type="number" name="amount" class="form-control" value="<?php echo htmlspecialchars($amount ?: '100'); ?>" required>
        </div>

        <div class="form-group">
            <label>Numéro du Payeur (msisdn)</label>
            <input type="text" name="payer" class="form-control" value="<?php echo htmlspecialchars($payer ?: '0349262379'); ?>" required>
        </div>

        <div class="form-group">
            <label>Numéro du Receveur (msisdn)</label>
            <input type="text" name="receiver" class="form-control" value="<?php echo htmlspecialchars($receiver ?: '0343500004'); ?>" required>
        </div>

        <div class="form-group">
            <label>Référence de Transaction</label>
            <input type="text" name="requestRef" class="form-control" value="<?php echo htmlspecialchars($requestRef ?: rand(10000000, 99999999)); ?>" required>
        </div>

        <button type="submit">🚀 Envoyer le Paiement</button>
    </form>
</div>

<?php if (!empty($response)): ?>
<div class="mvola-container">
    <h3 style="text-align:center;color:#28a745;">🧩 Résultat de la Requête</h3>
    <p><strong>Code HTTP :</strong> <?php echo htmlspecialchars($httpCode); ?></p>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><strong>Erreur CURL :</strong> <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <div class="mvola-result">
        <pre><?php echo htmlspecialchars($response); ?></pre>
    </div>
</div>
<?php endif; ?>
