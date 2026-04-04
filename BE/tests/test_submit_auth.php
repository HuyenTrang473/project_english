<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::find(1);
auth('sanctum')->setUser($user);

$req = \App\Http\Requests\SubmitTestRequest::create('/api/bai-tests/4/submit', 'POST', [
    'answers' => [
        ['id_cau_hoi' => 5, 'id_dap_an' => 17],
        ['id_cau_hoi' => 6, 'id_dap_an' => 20]
    ]
]);

$req->setContainer($app);
$req->setRedirector($app->make(\Illuminate\Routing\Redirector::class));
$req->validateResolved();

$ctrl = app(\App\Http\Controllers\BaiTestController::class);

try {
    $res = $ctrl->submitTest($req, 4);
    echo $res->getContent();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
