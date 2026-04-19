<?php

namespace App\Services;

use App\Models\AgentLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WhatsappConfig;
use App\Notifications\WhatsAppNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ReportingService
{
    protected AgentService $agent;

    public function __construct(AgentService $agent)
    {
        $this->agent = $agent;
    }

    public function generateAndSendReport(?int $businessId = null)
    {
        $panicMode = Setting::where('key', 'is_panic_mode')->first()?->value == '1';

        if ($panicMode) {
            Log::warning('AI Reporting skipped due to Panic Mode being active.');
            return 'Panic Mode is active. No reports sent.';
        }

        $query = WhatsappConfig::where('is_active', true);
        if ($businessId) {
            $query->where('business_id', $businessId);
        }
        $configs = $query->get();

        foreach ($configs as $config) {
            $report = $this->buildReport($config);
            
            // Send Mock Notification
            // Notification::send($config, new WhatsAppNotification($report));
            
            AgentLog::create([
                'business_id' => $config->business_id,
                'activity_type' => 'report_sent',
                'message' => "Report sent to {$config->phone_number} ({$config->label})",
                'metadata' => ['report' => $report],
            ]);
        }

        return 'Reports generated successfully.';
    }

    protected function buildReport(WhatsappConfig $config): string
    {
        if ($config->is_hidden) {
            // Hidden numbers get detailed technical error logs
            $logs = $this->agent->ask('Scan recent logs and identify any technical errors that need immediate attention.');
            return "🚨 [TECHNICAL ERROR LOGS] - {$config->label}\n\n{$logs}";
        }

        // Public numbers get sales and general stats
        $sales = Order::whereDate('created_at', today());
        if ($config->business_id) {
            $sales->where('business_id', $config->business_id);
        }
        $sales = $sales->get();
        
        $totalSales = $sales->sum('total_amount');
        $orderCount = $sales->count();
        
        $lowStockQuery = Product::where('stock_quantity', '<', 5);
        if ($config->business_id) {
            $lowStockQuery->where('business_id', $config->business_id);
        }
        $lowStock = $lowStockQuery->get();
        $stockReport = $lowStock->map(fn($p) => "{$p->name} (SKU: {$p->sku}): {$p->stock_quantity}")->implode("\n");

        return "📊 [DAILY SALES REPORT] - {$config->label}\n\n" .
               "Total Sales Today: {$totalSales}\n" .
               "Total Orders: {$orderCount}\n\n" .
               "⚠️ Low Stock Alert:\n" . ($stockReport ?: 'All items in stock.');
    }
}
