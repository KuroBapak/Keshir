<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
    }

    public function handle(Request $request)
    {
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification: ' . $e->getMessage()], 400);
        }

        $transactionInfo = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id; // e.g., QR-123-timestamp or POS-123-timestamp
        $fraud = $notif->fraud_status;

        // Extract internal transaction ID from Midtrans Order ID
        // Format: 'QR-' . $id . '-' . time() or 'POS-' . $id . '-' . time()
        $parts = explode('-', $orderId);
        if (count($parts) < 2 || !in_array($parts[0], ['QR', 'POS'])) {
            return response()->json(['message' => 'Ignoring unrecognized order prefix'], 200);
        }
        $transactionId = $parts[1];

        $payment = Payment::where('midtrans_reference', $orderId)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment record not found'], 404);
        }

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transactionInfo == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $payment->update(['status' => 'pending']);
                } else {
                    $this->processSuccess($transaction, $payment, ((float)$notif->gross_amount));
                }
            }
        } else if ($transactionInfo == 'settlement') {
            $this->processSuccess($transaction, $payment, ((float)$notif->gross_amount));
        } else if ($transactionInfo == 'pending') {
            $payment->update(['status' => 'pending']);
        } else if ($transactionInfo == 'deny' || $transactionInfo == 'expire' || $transactionInfo == 'cancel') {
            $payment->update(['status' => 'failed']);
            $this->processFailure($transaction);
        }

        return response()->json(['message' => 'Webhook handled']);
    }

    private function processSuccess(Transaction $transaction, Payment $payment, float $amountPayed)
    {
        // Don't process twice
        if ($payment->status === 'success' || $transaction->payment_status === 'paid') {
            return;
        }

        $payment->update([
            'status' => 'success',
            'amount_paid' => $amountPayed,
        ]);

        $transaction->update(['payment_status' => 'paid']);

        // Run FIFO deduction for all transaction details
        $transaction->load('details.addons');
        foreach ($transaction->details as $detail) {
            $this->transactionService->deductIngredients($detail);
        }

        // NOTE: Digital payments are NOT logged to cash drawer
        // because no physical cash enters the register.
        // Cash drawer only tracks physical cash in/out.
    }

    private function processFailure(Transaction $transaction)
    {
        // If unpaid QR order fails payment, void it to free up tables
        if ($transaction->payment_status === 'open' && $transaction->source === 'qr') {
            $this->transactionService->voidBill($transaction);
        }
    }
}
