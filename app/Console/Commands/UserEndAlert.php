<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\Mail\UserEndAlert as UserEndAlertMail;
use App\Models\MUserDetail;
use Datetime;
use Illuminate\Support\Facades\Log;

class UserEndAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UserEndAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UserEndAlert';

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
     * ユーザー利用終了メール通知
     *
     * @return int
     */
    public function handle()
    {
        Log::info('UserEndAlert START');
        $model = new MUserDetail();
        $userDatas = $model->getAllData();

        $today = new Datetime();
        $todayFormat = $today->format('Y-m-d');

        foreach ($userDatas as $userItem) {

            if ($todayFormat === $userItem->useEndAlertDate) {

                $data = [];
                Mail::to($userItem->mail)->send(new UserEndAlertMail($data));
            }
        }

        Log::info('UserEndAlert FINISH');

        return 0;
    }
}
