<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TcpdfFontAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tcpdfFontAdd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tcpdf用Font追加';

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
     */
    public function handle()
    {

        // フォント名：ipamjm
        $ret = \TCPDF_FONTS::addTTFfont(resource_path('fonts/ipamjm.ttf'));
        dump($ret);
    }
}
