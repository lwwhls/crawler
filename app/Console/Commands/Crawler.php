<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries;

class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler some url data';

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
     * @return mixed
     */
    public function handle()
    {
        $config_arr = $this->configSource();
        foreach($config_arr as $key=>$value){
            $html = file_get_contents($key);
            $obj = new Libraries\CctvCrawler();
            $obj->deal_html($html,'news');
        }

    }

    public function configSource()
    {
        $config_rules = array(
            'http://people.cctv.com/'=>array(
                'file'=>'Cctv.test',
                'type'=>'news',
            ),
        );
        return $config_rules;

    }
}
