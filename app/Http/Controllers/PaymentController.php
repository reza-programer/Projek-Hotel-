<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with(['user', 'room'])->findOrFail($request->reservation_id);

        $subtotal = $reservation->total_price;
        $tax = $subtotal * 0.11;
        $service = $subtotal * 0.05;
        $totalGross = $subtotal + $tax + $service;

        $params = [
            'transaction_details' => [
                'order_id' => $reservation->booking_code . '-' . time(),
                'gross_amount' => (int) $totalGross,
            ],
            'customer_details' => [
                'first_name' => $reservation->user->name,
                'email' => $reservation->user->email,
            ],
            'item_details' => [
                [
                    'id' => $reservation->room_id,
                    'price' => (int) $reservation->room->price,
                    'quantity' => $reservation->nights,
                    'name' => $reservation->room->name . ' (' . $reservation->nights . ' mlm)',
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderIdStr = $notif->order_id;
        $fraud = $notif->fraud_status;

        // MYB-2025-001-168123456
        $parts = explode('-', $orderIdStr);
        if (count($parts) >= 3) {
            $bookingCode = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
        } else {
            $bookingCode = $orderIdStr;
        }

        $reservation = Reservation::where('booking_code', $bookingCode)->first();

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $reservation->update(['payment_status' => 'pending']);
                } else {
                    $reservation->update(['payment_status' => 'paid', 'status' => 'confirmed']);
                    $this->createPaymentRecord($reservation, $type, $notif->gross_amount, $transaction);
                    
                    // Send Invoice Email
                    try {
                        Mail::to($reservation->user->email)->send(new InvoiceMail($reservation));
                    } catch (\Exception $e) {
                        Log::error('Failed to send invoice email: ' . $e->getMessage());
                    }
                }
            }
        } else if ($transaction == 'settlement') {
            $reservation->update(['payment_status' => 'paid', 'status' => 'confirmed']);
            $this->createPaymentRecord($reservation, $type, $notif->gross_amount, $transaction);

            // Send Invoice Email
            try {
                Mail::to($reservation->user->email)->send(new InvoiceMail($reservation));
            } catch (\Exception $e) {
                Log::error('Failed to send invoice email: ' . $e->getMessage());
            }
        } else if ($transaction == 'pending') {
            $reservation->update(['payment_status' => 'pending']);
        } else if ($transaction == 'deny') {
            $reservation->update(['payment_status' => 'rejected']);
        } else if ($transaction == 'expire') {
            $reservation->update(['payment_status' => 'rejected', 'status' => 'cancelled']);
        } else if ($transaction == 'cancel') {
            $reservation->update(['payment_status' => 'cancelled', 'status' => 'cancelled']);
        }

        return response()->json(['message' => 'Notification processed']);
    }

    private function createPaymentRecord($reservation, $method, $amount, $status)
    {
        Payment::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'method' => $method,
                'amount' => $amount,
                'verified_at' => now(),
                'notes' => 'Midtrans: ' . $status,
            ]
        );
    }
}
