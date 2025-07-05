<?php
// 販売店、代理店の検索画面
// 上記から代理店に紐づくユーザを検索する
//
namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MContractStatus;
use App\Models\MContractPlan;
use App\Models\AgentUsageStatus;
use App\Models\MUserCompany;
use App\Models\CsvAgentUsageStatus;
use App\Models\MAgent;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
//
//  use 販売店モデル
//
use DateTime;
use Illuminate\Support\Facades\Crypt;

use App\Models\MAdminUser;

/**
 * 代理店検索
 */
class AgentSearchController extends Controller
{
		private const JCIS_AGENT_CODE = "000";

		// 販売店
		private $agent_list = [];
		/*
		// 以下はDBから取得するまでのダミーデータ
		private $agent_list = [
		 [
			'id' =>  1,
			'name' => "日本リスクセンター",
			'agentid' => "1",
			'agentid2' => "1",
			'level' => 1,
		 ],
		 [
			'id' => 2,
			'name' =>  "販売店その１",
			'agentid' => "2",
			'agentid2' => "2",
			'level' => 1,
		 ],
		 [
			'id' => 3,
			'name' => "日本信用情報",
			'agentid' =>  "3",
			'agentid2' => "3",
			'level' => 1,
		 ],

 		// 販売店１の代理店
		 [
			'id' => 4,
			'name' => "日本リスクセンター代理店その１",
			'agentid' => "1",
			'agentid2' => "11",
			'level' => 2,
			'status' => 0,
			'clientnum' => 5,
		 ],
		 [
			'id' => 5,
			'name' => "日本リスクセンター代理店その２",
			'agentid' => "1",
			'agentid2' => "12",
			'level' => 2,
			'status' => 0,
			'clientnum' => 10,
		 ],
 		// 販売店２の代理店
		 [
			'id' => 6,
			'name' => "販売店その１代理店その１",
			'agentid' => "2",
			'agentid2' => "21",
			'level' => 2,
			'status' => 0,
			'clientnum' => 15,
		 ],
		 [
			'id' => 7,
			'name' => "販売店その１代理店その２",
			'agentid' => "2",
			'agentid2' => "22",
			'level' => 2,
			'status' => 0,
			'clientnum' => 25,
		 ],
		 [
			'id' => 8,
			'name' => "販売店その１代理店その３",
			'agentid' => "2",
			'agentid2' => "23",
			'level' => 2,
			'status' => 0,
			'clientnum' => 5,
		 ],

 		// 販売店３の代理店
		 [
			'id' => 9,
			'name' => "日本信用情報代理店その１",
			'agentid' => "3",
			'agentid2' => "31",
			'level' => 2,
			'status' => 0,
			'clientnum' => 2,
		 ],
		 [
			'id' => 10,
			'name' => "日本信用情報代理店その２",
			'agentid' => "3",
			'agentid2' => "32",
			'level' => 2,
			'status' => 0,
			'clientnum' => 3,
		 ],
		 [
			'id' => 11,
			'name' => "日本信用情報代理店その３",
			'agentid' => "3",
			'agentid2' => "33",
			'level' => 2,
			'status' => 0,
			'clientnum' => 30,
		 ],
		];
		*/
    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }
		$agentinfo = $request->session()->get('agentinfo'); 
		$agent_cd = $agentinfo["agent_cd"];
		$distributor_cd = $agentinfo["distributor_cd"];
		$level = $agentinfo["level"];

        $this->actionLog(__CLASS__, __FUNCTION__ . " agent_cd = [" . $agent_cd . "] distributor_cd = [". $distributor_cd . "]");

		$model = new MAgent();
		$distributorlist = $model->getDistributorist($distributor_cd);
		if ($level == 1) 
			$agentcdlist = $model->getAgentlist($distributor_cd, $agent_cd, 1);
		else
			$agentcdlist = $model->getAgentlist($distributor_cd, "0", 1);
        // $this->actionLog(__CLASS__, __FUNCTION__ . "[[".  var_export($agentlist, true) ."]");
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributorlist ::[".  var_export($distributorlist, true) ."]");
        $this->actionLog(__CLASS__, __FUNCTION__ . " agentcdlist::[[".  var_export($agentcdlist, true) ."]]");

		/*
		*/
        return view('manage/agentUsageStatus/search', [
				"distributorlist" => $distributorlist,
				"agentcdlist" => $agentcdlist,
			]);
    }

	/**
 	*
 	*  代理店検索結果画面
 	*/ 
	/*
    public function result(Request $request) {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);

		// 代理店コードを取得する
		$agentid1 = $request->input('agentid1');
		if ( $agentid1 === null ) $agentid1 = "";
		$agentid2 = $request->input('agentid2');
		if ( $agentid2 === null ) $agentid2 = "";
        $this->actionLog(__CLASS__, __FUNCTION__ . " agentlid:[".$agentid1 . "] agentid2[" .$agentid2 ."]");
		$agentlist  = $this->agentlist("");
		$agentlist_sub  = $this->agentlist_sub($agentid1, $agentid2);
				
        // 検索条件
        // $this->actionLog(__CLASS__, __FUNCTION__ . " agentlist_sub:[". var_export($agentlist_sub, true). "]");
        return view('manage/agentUsageStatus/result', [
				"result1" => $agentlist['agent1'],
				"result2" => $agentlist['agent2'],
				"agentid1" => $agentid1,
				"agentid2" => $agentid2,
				"result_sub" => $agentlist_sub,
			]
		);
    }
	*/

	/**
 	*
 	*  代理店検索結果画面
 	*/ 
    public function result(Request $request) {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

		$agentinfo = $request->session()->get('agentinfo'); 
		$level = $agentinfo["level"];
        $this->actionLog(__CLASS__, __FUNCTION__);

		// 代理店コードを取得する
		$distributor_cd = $request->input('distributor_cd');
		$agent_cd = $request->input('agent_cd');
		if ( $agent_cd === null ) $agent_cd = "0";
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributor_cd:[".$distributor_cd . "] agent_cd[" .$agent_cd ."]");
				
		$model = new MAgent();
		$distributorlist = $model->getDistributorist($distributor_cd);
		if ($level == 1) 
			$agentcdlist = $model->getAgentlist($distributor_cd, $agent_cd, 1);
		else
			$agentcdlist = $model->getAgentlist($distributor_cd, "0", 1);
		$result_sub = $model->getAgentlist($distributor_cd, $agent_cd, 1);
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributorlist ::[".  var_export($distributorlist, true) ."]");
        $this->actionLog(__CLASS__, __FUNCTION__ . " agentcdlist::[[".  var_export($agentcdlist, true) ."]]");
        // 検索条件
        // $this->actionLog(__CLASS__, __FUNCTION__ . " agentlist_sub:[". var_export($agentlist_sub, true). "]");
        return view('manage/agentUsageStatus/result', [
				"distributorlist" => $distributorlist,
				"agentcdlist" => $agentcdlist,
				"distributor_cd" => $distributor_cd,
				"agent_cd" => $agent_cd,
				"result_sub" => $result_sub,
			]
		);


    }


	/**
 	*
 	*  代理店新規・編集画面
 	*/ 
    public function edit(Request $request, $editId = '') {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);

		$distributor_cd = $request->input('distributor_cd');
		if ( $distributor_cd === null ) {
			// 本来販売店コードはないといけないので、JCISコードにする。
			$distributor_cd = "000";
		}
		// 代理店コードを取得する
		// $agent_cd = $request->input('agent_cd');
		$agent_cd = $editId;
		if ( $agent_cd === "" || $agent_cd === null) {
			// 代理店コードがないということは新規
			$agent = new MAgent();
		} else {
			$agent = MAgent::where('agent_cd', $agent_cd)->get()->first();
		}
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributor_cd:[".$distributor_cd . "] agent_cd[" .$agent_cd ."]");
        $this->actionLog(__CLASS__, __FUNCTION__ . " agent:[". var_export($agent, true). "]");
				
        return view('manage/agentUsageStatus/edit', [
				"distributor_cd" => $distributor_cd,
				"agent_cd" => $agent_cd,
				"agent" => $agent,
			]
		);
    }

	/**
 	*
 	*  代理店確認処理
 	*/ 
    public function confirm(Request $request) {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);

		$params = $request->input('agent');
        $this->actionLog(__CLASS__, __FUNCTION__ . " params(agent):[". var_export($params, true). "]");
		// 代理店コードを取得する
		$distributor_cd = $request->input('distributor_cd');
		$agent_cd = $params['agent_cd'];
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributor_cd:[".$distributor_cd . "] agent_cd[" .$agent_cd ."]");


		// バリデーション
		$rules = [
			'agent.agent_cd' => ['required', 'regex:/^[a-zA-Z0-9_-]+$/',],
			'agent.name' => ['required',],
			'agent.postCode' => ['required','regex:/^[a-zA-Z0-9]{7}$/',], 
			'agent.address' => ['required',], 
			'agent.tel' => ['required','regex:/^[a-zA-Z0-9]{10,11}$/'], 
		];
		$messages = [
			'agent.agent_cd.required' => '代理店コードは必須です',
			'agent.agent_cd.regex' => '代理店コードは英数字、-、_のいずれかです',
			'agent.name.required' => '代理店名は必須です',
			'agent.postCode.required' => '郵便番号は必須です',
			'agent.postCode.regex' => '郵便番号は数値7桁',
			'agent.address.required' => '住所は必須です',
			'agent.tel.required' => '電話番号は必須です',
			'agent.tel.regex' => '電話番号は数値10または11桁',
		];
		$validated = $request->validate(
			$rules,
			$messages
		);

		if ( empty($agent_cd) || empty($distributor_cd) ) {
			// 必須コードがないのでエラー
        	return view('manage/agentUsageStatus/edit', [
					"distributor_cd" => $distributor_cd,
					"agent_cd" => $agent_cd,
					"agent" => $params,
				]
			);
		}
				
		$request->session()->put('agent', $params);
        // 確認画面表示
        return view('manage/agentUsageStatus/confirm', [
				"distributor_cd" => $distributor_cd,
				"agent_cd" => $agent_cd,
				"agent" => $params,
			]
		);
    }

	/**
 	*
 	*  代理店更新処理
 	*/ 
    public function update(Request $request) {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);

		$params = $request->session()->get('agent');
        $this->actionLog(__CLASS__, __FUNCTION__ . " params(agent):[". var_export($params, true). "]");
		// 販売店コード
		$distributor_cd = $request->session()->get('distributor_cd');
		/**
 		 * 仮に設定する
 		**/
		$distributor_cd = "agent01";

		// 代理店コードを取得する
		$agent_cd = $params['agent_cd'];
        $this->actionLog(__CLASS__, __FUNCTION__ . " distributor_cd:[".$distributor_cd . "] agent_cd[" .$agent_cd ."]");
		if ( empty($agent_cd) || empty($distributor_cd) ) {
			// 必須コードがないのでエラー
        	return view('manage/agentUsageStatus/edit', [
					"distributor_cd" => $distributor_cd,
					"agent_cd" => $agent_cd,
					"agent" => $params,
				]
			);
		}

		// 対象データがあるか
		$agent = MAgent::where('agent_cd', $agent_cd)->where('distributor_cd', $distributor_cd)->get()->first();
		if ( $agent == null ) {
			// 新規
			$agent = new MAgent();
			$agent->distributor_cd = $distributor_cd;
			$agent->agent_cd = $agent_cd;
			$agent->level = 1;
			$agent->status = 0;
			$agent->name = $params['name'];
			$agent->postCode= $params['postCode'];
			$agent->address= $params['address'];
			$agent->tel = $params['tel'];
			$agent->mailCompanyName = $params['mailCompanyName'];
			$agent->homePageUrl = $params['homePageUrl'];
			
		} else {
			// 更新
			$agent->name = $params['name'];
			$agent->postCode= $params['postCode'];
			$agent->address= $params['address'];
			$agent->tel = $params['tel'];
			$agent->mailCompanyName = $params['mailCompanyName'];
			$agent->homePageUrl = $params['homePageUrl'];
		}
		$agent->save();
		
				
		// 	manage/agentSearch/index
        return redirect()->route('manageAgentSearch');

    }


    /**
     * 検索アクション
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request): RedirectResponse
    {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);
        $request->session()->put(__CLASS__ . 'pageNo', '');
        $request->session()->put('agentNo', $cond['agentNo']);

        return redirect()->route('manageAgentUsageStatus');
    }

    /**
     * 詳細
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function detail(Request $request, $editId): View|Factory|Application
    {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['agentNo'] = 1;
            $cond['targetMonth'] = now()->format('Y-m');
            $cond['contractPlan'] = '';
            $cond['chargeName'] = '';
            $cond['dispType'] = 2;
        }

        // 会社ID復号
        // $companyId = Crypt::decrypt($editId);
        $companyId = $editId;

        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId, $cond['agentNo']);

        $model = new AgentUsageStatus();
        $detail = $model->getDetailData($companyId, $cond['targetMonth'], $cond['agentNo']);

        $assignAry = [
            'companyId' => $companyId,
            'companyName' => $companyName,
            'targetmonth' => $cond['targetMonth'],
            'detail' => $detail,
            'userDetailList' => $userCompany->getAgent($companyId, $cond['agentNo']),
            'pageNo' => $request->session()->get(__CLASS__ . 'pageNo'),
        ];

        return view('manage/agentUsageStatus/detail',$assignAry);
    }

    /**
     * CSV出力(利用状況一覧)
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function listCsv(Request $request): BinaryFileResponse
    {
        //代理店閲覧権限があるユーザーか確認
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        $this->actionLog(__CLASS__, __FUNCTION__);
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['agentNo'] = 1;
            $cond['targetMonth'] = now()->format('Y-m');
            $cond['contractPlan'] = '';
            $cond['dispType'] = 2;
        }

        //ページ行数保持
        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new CsvAgentUsageStatus();

        $csvInfo = $model->makeCsv($cond, $pageNum);
        $headers = [['Content-Type' => 'text/css']];

        return response()->download($csvInfo['filePath'], $csvInfo['fileName'], $headers)->deleteFileAfterSend(true);

    }

	/**
 	 * 販売店代理店のリストを取得
 	 *　@param 販売店コード（ディフォルト "" のときは指定せず全部)
 	 *  @return 販売店、代理店の配列
 	 *
 	 */
	private function agentlist(string $agentid = "")
	{


		// 販売店リスト
		$agentresult = [];
		if ($agentid !== "") 
		{
			// 販売店コードが指定されている
			$agentresult[0] = $this->agent_list[$agentid];
		}
		else
		{
			// 販売店コードが指定されていないのですべて
			for ($i = 0; $i < count($this->agent_list); $i++ ) {
				if ($this->agent_list[$i]['level'] === 1) {
					$agentresult[] = $this->agent_list[$i];
				}
			}
		}
		// 代理店リスト
		$agentresult2 = [];
		if ($agentid !== "") 
		{
			for ($i = 0; $i < count($this->agent_list); $i++ ) {
				if ($this->agent_list[$i]['agentid'] === $agentid && $this->agent_list[$i]['level'] === 2) {
					$agentresult2[] = $this->agent_list[$i];
				}
			}
		}
		else
		{
			for ($i = 0; $i < count($this->agent_list); $i++ ) {
				if ($this->agent_list[$i]['level'] === 2) {
					$agentresult2[] = $this->agent_list[$i];
				}
			}
		}


		return [
			'agent1' => $agentresult,
			'agent2' => $agentresult2,
		];
	}

	/**
 	 * 代理店のリストを取得
 	 *　@param 販売店コー、販売店コード（ディフォルト "" のときは指定せず全部)
 	 *  @return 代理店の配列
 	 *
 	 */
	private function agentlist_sub(string $agentid1, string $agentid2)
	{


		// 代理店リスト
		$agentresult= [];
		if ($agentid2 !== "0") 
		{
			for ($i = 0; $i < count($this->agent_list); $i++ ) {
				if ($this->agent_list[$i]['agentid2'] === $agentid2) {
					$agentresult[] = $this->agent_list[$i];
				}
			}
		}
		else
		{
			if ($agentid1 !== "0" ) {
				for ($i = 0; $i < count($this->agent_list); $i++ ) {
					if ($this->agent_list[$i]['agentid'] === $agentid1 && $this->agent_list[$i]['level'] == 2) {
						$agentresult[] = $this->agent_list[$i];
					}
				}
			} else {
				for ($i = 0; $i < count($this->agent_list); $i++ ) {
					if ($this->agent_list[$i]['level'] === 2) {
						$agentresult[] = $this->agent_list[$i];
					}
				}
			}
		}


		return $agentresult;
	}
}
