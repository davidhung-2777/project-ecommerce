<?php

return [
    'cod' => [
        'enabled' => true,
        'name'    => 'Thanh toán khi nhận hàng (COD)',
    ],

    'bank_transfer' => [
        'enabled'        => true,
        'name'           => 'Chuyển khoản ngân hàng',
        'account_name'   => $_ENV['BANK_ACCOUNT_NAME'] ?? ($_ENV['VIETQR_ACCOUNT_NAME'] ?? ''),
        'account_number' => $_ENV['BANK_ACCOUNT_NUMBER'] ?? ($_ENV['VIETQR_ACCOUNT_NUMBER'] ?? ''),
        'bank_name'      => $_ENV['BANK_NAME'] ?? ($_ENV['VIETQR_BANK_CODE'] ?? ''),
        'bank_code'      => $_ENV['BANK_CODE'] ?? ($_ENV['VIETQR_BANK_CODE'] ?? 'VCB'),
        'bank_branch'    => $_ENV['BANK_BRANCH'] ?? '',
        'vietqr_api_key' => $_ENV['VIETQR_API_KEY'] ?? '',
    ],

    'momo' => [
        'enabled'      => true,
        'name'         => 'Ví MoMo',
        'partner_code' => $_ENV['MOMO_PARTNER_CODE'] ?? '',
        'access_key'   => $_ENV['MOMO_ACCESS_KEY'] ?? '',
        'secret_key'   => $_ENV['MOMO_SECRET_KEY'] ?? '',
        'endpoint'     => $_ENV['MOMO_ENDPOINT'] ?? 'https://test-payment.momo.vn/v2/gateway/api/create',
        'return_url'   => $_ENV['MOMO_RETURN_URL'] ?? '',
        'ipn_url'      => $_ENV['MOMO_IPN_URL'] ?? '',
    ],

    'vnpay' => [
        'enabled'     => true,
        'name'        => 'VNPay',
        'tmn_code'    => $_ENV['VNPAY_TMN_CODE'] ?? '',
        'hash_secret' => $_ENV['VNPAY_HASH_SECRET'] ?? '',
        'url'         => $_ENV['VNPAY_URL'] ?? 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
        'return_url'  => $_ENV['VNPAY_RETURN_URL'] ?? '',
        'ipn_url'     => $_ENV['VNPAY_IPN_URL'] ?? '',
        'api_url'     => 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction',
    ],
];
