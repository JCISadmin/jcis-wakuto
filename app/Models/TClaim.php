<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

class TClaim extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tClaim';

    /**
     * 請求情報を取得
     *
     * @param $claimMonth
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($claimMonth, $companyName, $pageLine): LengthAwarePaginator
    {

        $claimMonth = str_replace('-', '', $claimMonth); 

        $claim = DB::table('tClaim');
        $claim->where('claimMonth', $claimMonth);
       
        $query = DB::table('mUserCompany');
        $query->select(
            'mUserCompany.companyId',
            'mUserCompany.name as companyName',
            'claim.claimStatus',
            'claim.paymentStatus',
            'claim.claimNo',
            'claim.claimDate',
            'claim.paymentDate',
            'claim.price',
        );
        $query->leftJoinSub($claim, 'claim', function($join){
                    $join->on('mUserCompany.companyId', '=', 'claim.companyId');
        });

        if(is_null($companyName) === false){
            $query->where('mUserCompany.name', $companyName);
        }

        $query->orderBy('mUserCompany.name');

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);
    }


    /**
     * 請求ステータスを請求済に変更
     *
     * @param $companyId
     * @param $colum
     */
    public function changeClaimStatus($companyId, $claimMonth)
    {
        $this->begin();
        
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $claimMonth = str_replace('-', '', $claimMonth);
        
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $count = $query->first();

        if($count->count > 0){
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $upd->where('claimMonth', $claimMonth);
            $upd->update([
                'claimDate' => $now,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'updateDatetime' => $now,
            ]);

        }else{
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'claimMonth' => $claimMonth,
                'claimNo' => '',
                'price' => '',
                'claimDate' => $now,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                'updateDatetime' => $now,
                'updateDatetime' => $now,
            ]);
        }

        $this->commit();

    }

    /**
     * 入金ステータスを入金済に変更
     *
     * @param $companyId
     * @param $colum
     */
    public function changePaymentStatus($companyId, $claimMonth)
    {
        $this->begin();
        
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $claimMonth = str_replace('-', '', $claimMonth);
        
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $query->update([
            'paymentStatus' => self::PAYMENT_STATUS_DONE,
            'updateDatetime' => $now,
        ]);

        $this->commit();
    }


}
