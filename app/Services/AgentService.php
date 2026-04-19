<?php

namespace App\Services;

use App\Models\AgentLog;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentService
{
    protected ?string $apiKey;
    protected ?int $businessId;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct(?int $businessId = null)
    {
        $this->apiKey = config('services.gemini.key');
        $this->businessId = $businessId;
    }

    public function ask(string $prompt, bool $useTools = true)
    {
        if (!$this->apiKey) {
            Log::error('Gemini API Key is missing. Please set GEMINI_API_KEY in your .env file.');
            return ['error' => 'Gemini API Key is missing.'];
        }
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
        ];

        if ($useTools) {
            $payload['tools'] = [
                [
                    'function_declarations' => $this->getTools()
                ]
            ];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

        if (!$response->successful()) {
            Log::error('Gemini API Error: ' . $response->body());
            return ['error' => 'Failed to communicate with AI'];
        }

        $result = $response->json();
        $candidate = $result['candidates'][0]['content']['parts'][0] ?? null;

        if (isset($candidate['functionCall'])) {
            return $this->handleFunctionCall($candidate['functionCall']);
        }

        return $candidate['text'] ?? 'No response from AI';
    }

    protected function getTools(): array
    {
        return [
            [
                'name' => 'query_orders',
                'description' => 'Get total sales and order counts from the database.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => ['type' => 'string', 'description' => 'Filter by status (e.g., completed, pending)']
                    ]
                ]
            ],
            [
                'name' => 'query_inventory',
                'description' => 'Get current stock levels for products.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'sku' => ['type' => 'string', 'description' => 'Optional SKU to filter']
                    ]
                ]
            ],
            [
                'name' => 'scan_logs',
                'description' => 'Scan laravel.log for errors and site uptime issues.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'description' => 'Number of lines to scan']
                    ]
                ]
            ]
        ];
    }

    protected function handleFunctionCall(array $functionCall)
    {
        $name = $functionCall['name'];
        $args = $functionCall['args'] ?? [];

        $result = match ($name) {
            'query_orders' => $this->getOrders($args['status'] ?? null),
            'query_inventory' => $this->getInventory($args['sku'] ?? null),
            'scan_logs' => $this->getLogs($args['limit'] ?? 100),
            default => 'Unknown tool called',
        };

        // Log the tool usage
        AgentLog::create([
            'business_id' => $this->businessId,
            'activity_type' => 'tool_use',
            'message' => "AI used tool: $name",
            'ai_thought' => json_encode($args),
            'metadata' => ['result' => $result],
        ]);

        return $result;
    }

    protected function getOrders(?string $status)
    {
        $query = Order::query();
        if ($this->businessId) {
            $query->where('business_id', $this->businessId);
        }
        
        if ($status) $query->where('status', $status);
        
        return [
            'total_sales' => $query->sum('total_amount'),
            'order_count' => $query->count(),
        ];
    }

    protected function getInventory(?string $sku)
    {
        $query = Product::query();
        if ($this->businessId) {
            $query->where('business_id', $this->businessId);
        }
        
        if ($sku) $query->where('sku', $sku);

        return $query->select('name', 'sku', 'stock_quantity')->get()->toArray();
    }

    protected function getLogs(int $limit)
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) return 'Log file not found.';

        $logs = shell_exec("tail -n $limit $logPath");
        return $logs ?: 'No errors found in recent logs.';
    }
}
