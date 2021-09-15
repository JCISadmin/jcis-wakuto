<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use App\Models\MPrefecture;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\User\Search\SearchRequest;
use App\Models\SearchEngine;

/**
 * WEB検索画面
 */
class SearchController extends Controller
{

    /**
     * 初期画面
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $model = new MPrefecture();

        $request->session()->flash(__CLASS__ . 'searchData');

        $assignAry = [
            'selectList' => [
                'prefecture' => $model->getSelectList(),
            ],
        ];

        return view('user/search/edit', $assignAry);
    }

    /**
     * WEB検索
     *
     * @param SearchRequest $request
     */
    public function search(SearchRequest $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        /** @var AuthUser $user */
        $user = auth()->user();
        $model = new SearchEngine();

        $data = $request->input();

        $prefCity = '';
        if (is_null($data['prefecture']) === false) {
            $prefCity = $data['prefecture'];
            if (is_null($data['city']) === false) {
                $prefCity .= $data['city'];
            }
        }

        $age = '';
        if (is_null($data['age']) === false) {
            $age = $data['age'];
        }

        $isFussy = false;
        if (isset($data['fuzzyFlg'])) {
            $isFussy = true;
        }

        $result = [];
        foreach($data['companyName'] as $item) {
            if ($item !== '') {
                $result[$item]['keyword'] = $item;
                $result[$item]['result'] = $model->searchCompany($user->companyId, $user->contractPlanId, $user->userId, $item, $prefCity, $isFussy);
            }
        }

        foreach($data['parsonName'] as $item) {
            if ($item !== '') {
                $result[$item]['keyword'] = $item;
                $result[$item]['result'] = $model->searchPerson($user->companyId, $user->contractPlanId, $user->userId, $item, $age, $prefCity, $isFussy, '');
            }
        }

        $request->session()->put(__CLASS__ . 'searchData', $result);

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->Text(100, 100, '萩畗𪀚髙原');
        $pdf->Output('test.pdf', 'D');


    }


}
