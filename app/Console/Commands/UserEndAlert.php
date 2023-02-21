<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\Mail\UserEndAlert as UserEndAlertMail;
use App\Models\MUserDetail;
use App\Models\MUserCompany;
use App\Models\TContractPlan;
use App\Models\TContractPlanDetail;
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
        $MUserCompany = new MUserCompany();
        $companyList = $MUserCompany->getAllData();

        $today = new Datetime();
        $todayFormat = $today->format('Y-m-d');

        $TContractPlan = new TContractPlan();
        $TContractPlanDetail = new TContractPlanDetail();

        foreach ($companyList as $item) {

            if ($todayFormat === $item->useEndAlertDate) {
                // 当日が利用終了通知日のユーザーの場合

                $userItem = $MUserCompany->get($item->companyId);
                $planItem = $TContractPlan->getPlanUsePlanId($item->companyId, $item->contractPlanId);
                $planDetailItem = $TContractPlanDetail->getPlanUsePlanId($item->companyId, $item->contractPlanId);

                $data = [
                    'companyName' => $userItem['userCompany']['name'],
                    'staffName' => $userItem['userCompany']['staffName'],
                    'staffDepartmentJob' => $userItem['userCompany']['staffDepartmentJob'],
                    'useStartDate' => $planItem->useStartDate,
                    'useUpdateDate' => $planItem->useUpdateDate,
                    'useEndDate' => $planItem->useEndDate,
                    'idUnitPrice' => $planDetailItem->idUnitPrice,
                    'searchUnitPrice' => $planDetailItem->searchUnitPrice,
                    'chargeName' => $userItem['userCompany']['chargeName'],
                    'chargeMail' => $userItem['userCompany']['chargeMail'],
                ];

                // ユーザーの担当者に利用終了通知メールを送信
                Mail::to($userItem['userCompany']['staffMail'])->send(new UserEndAlertMail($data));
                Log::info("Sent companyId:$item->companyId contractPlanId:$item->contractPlanId");
            }
        }

        Log::info('UserEndAlert FINISH');

        return 0;
    }
}
