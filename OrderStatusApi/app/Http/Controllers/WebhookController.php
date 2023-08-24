<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Get order ID from the Dialogflow webhook request Body
        $orderId = $request->queryResult['parameters']['orderId'];

        try {
                // Make POST request to the order status API
                $response = Http::post('https://orderstatusapi-dot-organization-project-311520.uc.r.appspot.com/api/getOrderStatus', [
                    'orderId' => $orderId,
                ]);

                // Get shipment date from the API response
                $shipmentDate = $response['shipmentDate'];

                // Convert the shipment date from ISO 8601 format to human readable format
                $shipmentDate = Carbon::parse($shipmentDate)->format('l, jS F Y');

                // Build Dialogflow response
                $fulfillmentText = "Your order $orderId is scheduled to be shipped on $shipmentDate.";

                $webhookResponse = [
                    'fulfillmentText' => $fulfillmentText,
                ];

                return response()->json($webhookResponse);
        } catch (Exception $ex) {
            return response()->json([
                'fulfillmentText' => 'Something went wrong'
            ]);
        }
    }
}
