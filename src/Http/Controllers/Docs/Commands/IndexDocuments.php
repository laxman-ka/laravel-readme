<?php

namespace Diviky\Readme\Console\Commands;

use App\Services\Documentation\Indexer;
use Illuminate\Console\Command;

class IndexDocuments extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'readme:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all documentation on Algolia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(Indexer::class)->indexAllDocuments();
    }
}
