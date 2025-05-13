<!-- 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController extends Controller
{
    private $client;

    public function __construct()
    {
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_SECRET');

        $environment = new SandboxEnvironment($clientId, $clientSecret);
        $this->client = new PayPalHttpClient($environment);
    }

    public function createOrder(Request $request)
    {
        $requestPaypal = new OrdersCreateRequest();
        $requestPaypal->prefer('return=representation');
        $requestPaypal->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "10.00" // Monto estático para pruebas
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => url('/payment/success'),
                "cancel_url" => url('/payment/cancel')
            ]
        ];

        try {
            $response = $this->client->execute($requestPaypal);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->input('orderId');

        $requestPaypal = new OrdersCaptureRequest($orderId);
        $requestPaypal->prefer('return=representation');

        try {
            $response = $this->client->execute($requestPaypal);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} -->