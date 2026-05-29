<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CustomerController,
    PropertyController,
    BoilerController,
    JobController,
    CertificateController,
    FindingController,
    QuoteController,
    InvoiceController,
    ReminderController,
    AddressController,
    CalculatorController,
    SyncController,
    WebhookController,
};

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register-device', [AuthController::class, 'registerDevice']);
    Route::get('/addresses/search', [AddressController::class, 'search']);
    Route::post('/calc/gas-rate', [CalculatorController::class, 'gasRate']);
    Route::post('/calc/pipe-sizing', [CalculatorController::class, 'pipeSizing']);
    Route::post('/calc/heat-loss', [CalculatorController::class, 'heatLoss']);
    Route::post('/calc/volume', [CalculatorController::class, 'volume']);
    Route::post('/webhooks/stripe', [WebhookController::class, 'stripe']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('customers', CustomerController::class);
        Route::get('customers/recent', [CustomerController::class, 'recent']);
        Route::get('customers/{customer}/properties', [PropertyController::class, 'index']);
        Route::post('customers/{customer}/properties', [PropertyController::class, 'store']);

        Route::apiResource('properties', PropertyController::class)->except(['index', 'store']);
        Route::get('properties/search', [PropertyController::class, 'searchByPostcode']);
        Route::get('properties/{property}/boilers', [BoilerController::class, 'index']);
        Route::post('properties/{property}/boilers', [BoilerController::class, 'store']);

        Route::apiResource('boilers', BoilerController::class)->except(['index', 'store']);

        Route::apiResource('jobs', JobController::class);
        Route::get('jobs/today', [JobController::class, 'today']);
        Route::get('jobs/week', [JobController::class, 'thisWeek']);
        Route::post('jobs/{job}/complete', [JobController::class, 'complete']);

        Route::apiResource('certificates', CertificateController::class);
        Route::get('certificates/{certificate}/pdf', [CertificateController::class, 'pdf']);
        Route::post('certificates/{certificate}/email', [CertificateController::class, 'email']);
        Route::post('certificates/{certificate}/sign', [CertificateController::class, 'sign']);

        Route::apiResource('findings', FindingController::class);

        Route::apiResource('quotes', QuoteController::class);
        Route::post('quotes/{quote}/send', [QuoteController::class, 'send']);
        Route::post('quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice']);

        Route::apiResource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid']);
        Route::post('invoices/{invoice}/stripe-link', [InvoiceController::class, 'stripeLink']);

        Route::apiResource('reminders', ReminderController::class);
        Route::post('reminders/{reminder}/send', [ReminderController::class, 'sendNow']);

        Route::post('sync/pull', [SyncController::class, 'pull']);
        Route::post('sync/push', [SyncController::class, 'push']);
    });
});
