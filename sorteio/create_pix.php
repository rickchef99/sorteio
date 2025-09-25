<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $document = $_POST['document'];
    $numbers = (float)$_POST['numbers'];

    $amount = $numbers * 0.02; // cálculo do valor baseado em "numbers"

    // Gera UUID no formato padrão
    function generateUUID() {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // versão 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    $identifier = generateUUID();

    $data = [
        "identifier" => $identifier,
        "amount" => $amount,
        "client" => [
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "document" => $document
        ],
        "products" => [
            ["id" => "sorteio001", "name" => "Cotas do sorteio", "quantity" => (int)$numbers, "price" => 0.02]
        ],
        "dueDate" => date("Y-m-d", strtotime("+2 days")),
        "metadata" => ["numbers" => $numbers],
    ];

    // Requisição para criar PIX
    $ch = curl_init("https://app.dustpay.com.br/api/v1/gateway/pix/receive"); // coloque a URL correta
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-public-key: rickdamirars_3ul3jb2d0mb20jcm",
        "x-secret-key: 70j8uc9rkt8pleywxcgjspxh6u634isc90rikmi3ah4tmz9eifnnurg7n8nd316a",
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);

    if (isset($json['transactionId'])) {
        $pushcutUrl = "https://api.pushcut.io/HnecwhbQfyosxDwSK9un-/notifications/Vega%20aprovada";
        $chPush = curl_init($pushcutUrl);
        curl_setopt($chPush, CURLOPT_RETURNTRANSFER, true);
        curl_exec($chPush);
        curl_close($chPush);

        header("Location: status.php?clientIdentifier=" . $identifier . "&id=" . $json['transactionId']);
        exit;
    } else {
        echo "Erro ao criar PIX: " . $response;
    }
}
?>
