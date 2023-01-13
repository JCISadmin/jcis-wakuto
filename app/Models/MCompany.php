<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 会社情報
 */
class MCompany extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mCompany';

    /**
     * 会社情報を取得
     * @param
     * @return array $companyInfo
     */
    public function getCompanyInfo(): array
    {
        $query = DB::table('mCompany');
        $companyInfo = $query->first();
        return (array)$companyInfo;
    }
}