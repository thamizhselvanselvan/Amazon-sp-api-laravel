<?php

namespace App\Console\Commands;

use App\Jobs\BOE\RemoveUploadedFile;
use Illuminate\Console\Command;

class RemoveUploadedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:remove-uploaded-boe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove uploaded file from server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        RemoveUploadedFile::dispatch();
    }
}
