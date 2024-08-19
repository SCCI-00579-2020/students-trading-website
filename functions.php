<?php
include 'config.php';

function getAccessToken() {
    $consumerKey = 'E0mdVoNp0abhWvOJvXGuKwvzYzsTadMY3ubh9G1F3gSOGZyl';
    $consumerSecret = 'Lj4n8WGGbrIBh8JSStW9FBfubJnbM1AmHl32PAblAPqhbuV9ZZxPoOtqwNZhrmGQ';
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    return $result->access_token;
}

function initiateSTKPush($transaction_id, $amount, $partyA, $partyB, $phoneNumber, $callBackURL) {
    $accessToken = getAccessToken();
    $timestamp = date('YmdHis');
    $password = base64_encode('174379' . 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919' . $timestamp);
    
    $curl_post_data = [
        'BusinessShortCode' => '174379', 
        'Password' => $password,
        'Timestamp' => $timestamp,
        
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $partyA, 
        'PartyB' => '174379',
        'PhoneNumber' => $phoneNumber, 
        'CallBackURL' => $callBackURL,
        'AccountReference' => 'Transaction ' . $transaction_id,
        'TransactionDesc' => 'Payment for Transaction ' . $transaction_id
        //  'storeNumber' => '5145656'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    print_r($result);
    return $result;
}

function createTransaction($buyer_id, $seller_id, $product_id,$amount) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO transactions (buyer_id, seller_id,product, amount, status) VALUES (?, ?, ?,?, 'completed')");
    $stmt->bind_param("iiid", $buyer_id, $seller_id,$product_id, $amount);
    $stmt->execute();
    return [
        'id' => $stmt->insert_id,
        'buyer_id' => $buyer_id,
        'seller_id' => $seller_id,
        'product'=>$product_id,
        'amount' => $amount,
        'status' => 'completed',
    ];
}

function getBuyerByUsername($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM buyers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getSellerByUsername($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM sellers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getTransactionsByUser($user_id, $role) {
    global $conn;
    if ($role == 'buyer') {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE buyer_id = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE seller_id = ?");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getBuyerUsernameById($buyer_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM buyers WHERE id = ?");
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $buyer = $result->fetch_assoc();
    return $buyer['username'];
}

function getSellerUsernameById($seller_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM sellers WHERE id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();
    return $seller['username'];
}
