<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAgentDb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('manage/agentUsageStatus*')) {

            $agentNo = $request->session()->get('agentNo', 1);

            if ($agentNo) {
                $dbConnection = $this->getDbConnectionByAgentNo($agentNo);
                if ($dbConnection) {
                    config(['database.default' => $dbConnection]);
                }
            }
        }

        return $next($request);
    }

    /**
     * データベース接続を取得
     *
     * @param  string  $agentNo
     * @return string|null
     */
    protected function getDbConnectionByAgentNo($agentNo)
    {
        $dbConnectionName = '';

        $agentList = config('agent.agentList');
        if (array_key_exists($agentNo, $agentList)) {
            $dbConnectionName = $agentList[$agentNo]['dbConnection'];
        }

        return $dbConnectionName;
    }
}
