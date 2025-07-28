<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 販売店・代理店
 */
class MAgent extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mAgent';
	protected $guarded = ['id', ];

	// 販売店データベースへ接続する
	// 	protected $connection = 'mysql_agent1';
    /**
     * 販売店情報を取得
     * @param
     * @return array 
     */
    public function getDistributorist($distributor_cd = "0")
    {
		if ($distributor_cd === "0") {
			return  MAgent::where('level', '0')->get();
		} 
		return MAgent::where('distributor_cd', $distributor_cd)->where('level', '0')->get();
    }

    /**
     * 代理店情報を取得
     * @param distributor_cd:販売店コード、agent_cd：代理店コード
     * @return array 
     */
    public function getAgentlist($distributor_cd, $agent_cd, $level = -1)
    {
		if ($agent_cd !== "0") {
			return MAgent::where('agent_cd', $agent_cd)->get();
		}
		if ($distributor_cd === "0") {
			return MAgent::where('level', '1')->get();
		}
		else if ($level !== -1 ){
			return  MAgent::where('distributor_cd', $distributor_cd)->where('level', $level)->get();
		}

		return  MAgent::where('distributor_cd', $distributor_cd)->get();
    }
}
