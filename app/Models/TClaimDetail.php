<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Datetime;

class TClaimDetail extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tClaimDetail';

    /**
     * 請求費目一覧(補正額除く)を取得
     */
    public function getExpenseList($companyId, $claimMonth)
    {
        $expenseList = [];
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $query->orderBy('seqNo');

        $data = $query->get();

        foreach($data as $item){

            //補正額をスキップ
            if($item->type === 'adjust'){
                continue;
            }

            $expenseList[] = [
                'type' => $item->type,
                'useFlg' => $item->useFlg,    
                'itemName' => $item->itemName,
                'amount' => $item->amount,
                'unit' => $item->unit,
                'unitPrice' => $item->unitPrice,
                'price' => $item->price,
            ];

        }

        return $expenseList;

    }

    /**
     * 請求費目一覧(補正額)を取得
     */
    public function getExpenseAdjustList($companyId, $claimMonth)
    {
        $expenseAdjustList = [];
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $query->orderBy('seqNo');

        $data = $query->get();

        foreach($data as $item){

            //補正額以外をスキップ
            if($item->type !== 'adjust'){
                continue;
            }

            $expenseAdjustList[] = [
                'type' => $item->type,
                'useFlg' => $item->useFlg,
                'itemName' => $item->itemName,
                'amount' => $item->amount,
                'unit' => $item->unit,
                'unitPrice' => $item->unitPrice,
                'price' => $item->price,
            ];

        }

        return $expenseAdjustList;

    }

    /**
     * 更新
     *
     * @param $companyId
     * @param $claimMonth
     * @param $updateData
     * @throws Exception
     */
    public function claimUpdate($companyId, $claimMonth, $updateData)
    {
        $strClaimMonth = str_replace('-', '', $claimMonth);
        $dt = new Datetime();
        $now = $dt->format('Ymd');

        $this->begin();

        //既存レコード削除
        $del = DB::table($this->table);
        $del->where('companyId', $companyId);
        $del->where('claimMonth', $strClaimMonth);
        $del->delete();

        //seqNo用
        $num = 1;

        //新規レコード追加
        foreach($updateData['detail'] as $list){

            foreach($list as $item){

                $ins = DB::table($this->table);
                $ins->insert([
                    'companyId' => $companyId,
                    'claimMonth' => $strClaimMonth,
                    'seqNo' => $num,
                    'type' => $item['type'],
                    'useFlg' => $item['useFlg'],
                    'itemName' => $item['itemName'],
                    'amount' => $item['amount'],
                    'unit' => $item['unit'],
                    'unitPrice' => $item['unitPrice'],
                    'price' => $item['price'],
                    'createDatetime' => $now,
                    'updateDatetime' => $now
                ]);

                $num++;
            }
                
        }

        $this->commit();

    }

    /**
     * 補正合計額(有効のみ)を取得
     *
     * @param $companyId
     * @param $strClaimMonth
     */
    public function getAdjustPrice($companyId, $strClaimMonth)
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('IFNULL(SUM(price), 0) as adjustPrice'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $query->where('type', 'adjust');
        $query->where('useFlg', 1);
        $list = $query->get();

        return $list[0]->adjustPrice;

    }

    /**
     * 請求額を取得(補正額除く)
     *
     * @param $companyId
     * @param $strClaimMonth
     */
    public function getCalcPrice($companyId, $claimMonth)
    {
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('SUM(price) as price'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $query->where('type', '<>', 'adjust');
        $query->where('useFlg', 1);
        $list = $query->get();

        return $list[0]->price;
        
    }

}
