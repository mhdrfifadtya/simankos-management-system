<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class UpdateOverdueInvoices extends Command
{
    protected $signature = 'invoices:update-overdue';
    protected $description = 'Update status invoice yang sudah melewati jatuh tempo menjadi overdue';

    public function __construct(private InvoiceService $invoiceService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->invoiceService->updateOverdueStatus();
        $this->info("Updated {$count} invoice(s) to overdue status.");
        return self::SUCCESS;
    }
}
