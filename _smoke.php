<?php
// End-to-end HTTP smoke test using the app's own server + Guzzle (already installed).
// Covers public pages, admin login flow, and every admin page.

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$port = 8021;
$base = "http://127.0.0.1:{$port}";

$serverProc = proc_open(
    'php artisan serve --host=127.0.0.1 --port='.$port.' --no-reload',
    [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    __DIR__,
    ['APP_ENV' => 'local']
);
sleep(3);

$jar = new CookieJar();
$client = new Client([
    'base_uri' => $base,
    'cookies' => $jar,
    'http_errors' => false,
    'allow_redirects' => false,
    'verify' => false,
]);

$failures = [];
$totals = ['ok' => 0, 'fail' => 0];

function hit(Client $client, string $method, string $uri, array $opts = []): array
{
    try {
        $res = $client->request($method, $uri, $opts);
        return [$res->getStatusCode(), (string) $res->getBody()];
    } catch (\Throwable $e) {
        return [0, $e->getMessage()];
    }
}

// ---------- Public pages ----------
$publicPages = [
    '/' => 200,
    '/shop' => 200,
    '/policies' => 200,
    '/about' => 200,
    '/how-to-apply' => 200,
    '/contact' => 200,
    '/cart' => 200,
    '/checkout' => 200,
    '/login' => 200,
    '/register' => 200,
    '/forgot-password' => 200,
    '/admin/login' => 200,
    '/products/rosy-quartz-ombre' => 200,
];

foreach ($publicPages as $uri => $expected) {
    [$code, $body] = hit($client, 'GET', $uri);
    $name = "PUBLIC GET {$uri}";
    if ($code === $expected) {
        $totals['ok']++;
        echo "OK   {$code}  {$name}\n";
    } else {
        $totals['fail']++;
        echo "FAIL {$code} (expected {$expected}) {$name}\n";
        $failures[] = $name;
    }
    // Guard against leaked blade error directives on any page
    if (str_contains($body, '@errorEnd') || str_contains($body, '@error(')) {
        $totals['fail']++;
        echo "FAIL unrendered blade directive on {$uri}\n";
        $failures[] = "blade directive {$uri}";
    }
}

// ---------- Admin login flow ----------
[$loginCode, $loginBody] = hit($client, 'GET', '/admin/login');
preg_match('/name="_token" value="([^"]+)"/', $loginBody, $m);
$token = $m[1] ?? '';

if (! $token) {
    echo "FAIL could not extract CSRF token from admin login page\n";
    $failures[] = 'admin-login-csrf';
} else {
    // Login with the seeded admin credentials from .env
    $adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@alishenails.com';
    $adminPass = getenv('ADMIN_SEED_PASSWORD') ?: 'ChangeMe123!';

    // Read .env directly in case env vars are not inherited through proc_open
    if (file_exists(__DIR__.'/.env')) {
        foreach (file(__DIR__.'/.env') as $line) {
            if (preg_match('/^ADMIN_EMAIL=(.*)$/', trim($line), $mm)) {
                $adminEmail = trim($mm[1], '"');
            }
            if (preg_match('/^ADMIN_SEED_PASSWORD=(.*)$/', trim($line), $mm)) {
                $adminPass = trim($mm[1], '"');
            }
        }
    }

    [$postCode, $postBody] = hit($client, 'POST', '/admin/login', [
        'form_params' => ['_token' => $token, 'email' => $adminEmail, 'password' => $adminPass],
        'headers' => ['Referer' => $base.'/admin/login'],
    ]);

    if ($postCode === 302) {
        $totals['ok']++;
        echo "OK   302  ADMIN LOGIN as {$adminEmail}\n";
    } else {
        $totals['fail']++;
        echo "FAIL {$postCode}  ADMIN LOGIN\n";
        $failures[] = 'admin-login-post';
    }

    // Follow redirect to the dashboard
    $adminPages = [
        '/admin' => 200,
        '/admin/dashboard' => 200,
        '/admin/products' => 200,
        '/admin/products/create' => 200,
        '/admin/categories' => 200,
        '/admin/categories/create' => 200,
        '/admin/shipping' => 200,
        '/admin/shipping/create' => 200,
        '/admin/orders' => 200,
        '/admin/customers' => 200,
        '/admin/reviews' => 200,
        '/admin/analytics' => 200,
        '/admin/settings' => 200,
        // A real order detail page (id 1 exists in the seeded MySQL DB)
        '/admin/orders/1' => 200,
    ];

    foreach ($adminPages as $uri => $expected) {
        [$code, $body] = hit($client, 'GET', $uri);
        $name = "ADMIN GET {$uri}";
        if ($code === $expected) {
            $totals['ok']++;
            echo "OK   {$code}  {$name}\n";
        } else {
            $totals['fail']++;
            echo "FAIL {$code} (expected {$expected}) {$name}\n";
            $failures[] = $name;
        }
    }
}

// Admin should NOT be able to hit customer-only pages that require web auth
[$code] = hit($client, 'GET', '/account/orders');
echo ($code === 302 ? "OK" : "FAIL {$code}")."  web-auth redirect /account/orders\n";
if ($code !== 302) {
    $totals['fail']++;
    $failures[] = 'account-orders-redirect';
} else {
    $totals['ok']++;
}

proc_terminate($serverProc);

echo "\n==========================================\n";
echo "SUMMARY: {$totals['ok']} ok, {$totals['fail']} failed\n";
if (! empty($failures)) {
    echo "FAILURES:\n  - ".implode("\n  - ", $failures)."\n";
    exit(1);
}
echo "ALL CHECKS PASSED\n";