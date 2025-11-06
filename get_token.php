<?php
if (!defined('OC_ADMIN')) exit('No direct access allowed.');

if (Params::getParam('submit') == '1') {
    $consumer_key = trim(Params::getParam('consumer_key'));
    $consumer_secret = trim(Params::getParam('consumer_secret'));
    $auth_type = trim(Params::getParam('auth_type'));
    $cache_control = trim(Params::getParam('cache_control'));
    $grant_type = trim(Params::getParam('grant_type'));
    $scope = trim(Params::getParam('scope'));

    $url = "https://devapi.mvola.mg/token/";

    // === Préparation des headers ===
    $headers = [
        "Authorization: $auth_type " . base64_encode($consumer_key . ":" . $consumer_secret),
        "Cache-Control: $cache_control",
        "Content-Type: application/x-www-form-urlencoded"
    ];

    // === Corps de la requête ===
    $body = http_build_query([
        'grant_type' => $grant_type,
        'scope' => $scope
    ]);

    // === Envoi de la requête avec cURL ===
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<h3>🧩 Résultat de la requête</h3>";
    echo "<strong>Code HTTP :</strong> $httpCode<br><br>";
    echo "<pre style='background:#f7f7f7; padding:10px; border-radius:8px;'>" . htmlspecialchars($response) . "</pre>";

    // Enregistrement du token si succès
    $decoded = json_decode($response, true);
    if (isset($decoded['access_token'])) {
        osc_set_preference('mvola_access_token', $decoded['access_token'], 'plugin-mvola', 'STRING');
        echo "<p style='color:green; font-weight:bold;'>✅ Access Token enregistré avec succès !</p>";
    }
}
?>

<h2>🪙 Obtenir un Access Token - MVola Sandbox</h2>

<form method="post" action="">
    <input type="hidden" name="submit" value="1">

    <fieldset style="border:1px solid #ccc; padding:15px; border-radius:8px; width:600px;">
        <legend><strong>🔐 Authorization</strong></legend>

        <div style="margin-bottom:10px;">
            <label><strong>Auth Type :</strong></label><br>
            <input type="text" name="auth_type" value="Basic" required style="width:300px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Consumer Key :</strong></label><br>
            <input type="text" name="consumer_key" required style="width:300px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Consumer Secret :</strong></label><br>
            <input type="text" name="consumer_secret" required style="width:300px;">
        </div>
    </fieldset>

    <fieldset style="border:1px solid #ccc; padding:15px; border-radius:8px; width:600px; margin-top:15px;">
        <legend><strong>📋 Headers</strong></legend>

        <div style="margin-bottom:10px;">
            <label><strong>Cache-Control :</strong></label><br>
            <input type="text" name="cache_control" value="no-cache" required style="width:300px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Content-Type :</strong></label><br>
            <input type="text" value="application/x-www-form-urlencoded" readonly style="width:300px;">
        </div>
    </fieldset>

    <fieldset style="border:1px solid #ccc; padding:15px; border-radius:8px; width:600px; margin-top:15px;">
        <legend><strong>🧠 Body</strong></legend>

        <div style="margin-bottom:10px;">
            <label><strong>grant_type :</strong></label><br>
            <input type="text" name="grant_type" value="client_credentials" required style="width:300px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>scope :</strong></label><br>
            <input type="text" name="scope" value="EXT_INT_MVOLA_SCOPE" required style="width:300px;">
        </div>
    </fieldset>

    <fieldset style="border:1px solid #ccc; padding:15px; border-radius:8px; width:600px; margin-top:15px;">
        <legend><strong>🌐 URL & Méthode</strong></legend>

        <div style="margin-bottom:10px;">
            <label><strong>URL :</strong></label><br>
            <input type="text" value="https://devapi.mvola.mg/token/" readonly style="width:500px;">
        </div>

        <div style="margin-bottom:10px;">
            <label><strong>Méthode :</strong></label><br>
            <input type="text" value="POST" readonly style="width:100px;">
        </div>
    </fieldset>

    <div style="margin-top:20px;">
        <input type="submit" value="🚀 Envoyer la requête" class="btn btn-primary">
    </div>
</form>
