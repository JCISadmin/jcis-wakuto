<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * お知らせ情報
 */
class TInfomation extends BaseModel
{
    /**
     * クエリ接続先DB
     *
     * @var string
     */
    public $searchDBConnection = 'mysql_search';

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tInfomation';

    /**
     * お知らせ種別: お知らせ
     */
    const INFO_TYPE_INFO = 'info';

    /**
     * お知らせ種別: メンテナンス
     */
    const INFO_TYPE_MAINTENANCE = 'maintenance';

    /**
     * 公開状態: 公開
     */
    const IS_PUBLIC_ON = 1;

    /**
     * 公開状態: 非公開
     */
    const IS_PUBLIC_OFF = 0;

    /**
     * 一覧取得
     *
     * @param string $infoType
     * @param string $isPublic
     * @param string $startDate
     * @param string $endDate
     * @param string $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($infoType = '', $isPublic = '', $startDate = '', $endDate = '', $pageLine = '')
    {
        $connection = DB::connection($this->searchDBConnection);
        $query = $connection->table($this->table);
        $query->where('delFlg', self::DEL_FLG_OFF);

        if ($infoType != '') {
            $query->where('infoType', $infoType);
        }

        if ($isPublic !== '') {
            $query->where('isPublic', $isPublic);
        }

        if ($startDate != '') {
            $query->where('infoDate', '>=', $startDate);
        }

        if ($endDate != '') {
            $query->where('infoDate', '<=', $endDate);
        }

        $query->orderBy('infoDate', 'desc');
        $query->orderBy('infomationId', 'desc');

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);
    }

    /**
     * 詳細取得（管理画面用）
     *
     * @param int $infomationId
     * @return object|null
     */
    public function get($infomationId)
    {
        $connection = DB::connection($this->searchDBConnection);
        $query = $connection->table($this->table);
        $query->where('infomationId', $infomationId);
        $query->where('delFlg', self::DEL_FLG_OFF);

        return $query->first();
    }

    /**
     * 詳細取得（ユーザー画面用）
     *
     * @param int $infomationId
     * @return object|null
     */
    public function getPublic($infomationId)
    {
        $connection = DB::connection($this->searchDBConnection);
        $query = $connection->table($this->table);
        $query->where('infomationId', $infomationId);
        $query->where('delFlg', self::DEL_FLG_OFF);
        $query->where('isPublic', self::IS_PUBLIC_ON);

        return $query->first();
    }

    /**
     * 公開中のお知らせ一覧取得（ユーザー画面用）
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPublicList($limit = 3)
    {
        $connection = DB::connection($this->searchDBConnection);
        $query = $connection->table($this->table);
        $query->where('delFlg', self::DEL_FLG_OFF);
        $query->where('isPublic', self::IS_PUBLIC_ON);
        $query->orderBy('infoDate', 'desc');
        $query->orderBy('infomationId', 'desc');
        $query->limit($limit);

        return $query->get();
    }

}