<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;

use App\Models\MAdminUser;
use App\Models\AuthUser;

/**
 * 認証プロパイダ
 */
class AuthUserProvider implements UserProvider
{

    private $dbModel;

    /**
     * ユーザー情報の取得
     *
     * @param mixed $identifier
     * @return AuthUser|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {

        $this->setModel($identifier);

        $user = $this->dbModel->getUserCredentials($identifier['userId'], $identifier['password']);
        return $this->getGenericUser($user, $identifier['type']);

    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
        dd('retrieveByToken');
    }

    public function updateRememberToken(\Illuminate\Contracts\Auth\Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
        dd('updateRememberToken');
    }

    /**
     * ユーザー認証
     *
     * @param array $credentials
     * @return AuthUser|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {

        $this->setModel($credentials);

        $user = $this->dbModel->getUserCredentials($credentials['userId'], $credentials['password']);
        //return $this->getGenericUser($user, $credentials['type']);
        return $this->getGenericUser($user, 0);

    }

    /**
     * 認証リトライ回数チェック
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials)
    {
        return true;
    }


    /**
     * 認証ユーザーモデルの生成
     *
     * @param $user
     * @param $type
     * @return AuthUser|null
     */
    protected function getGenericUser($user, $type)
    {
        if (! is_null($user)) {
            $aryUser = (array) $user;
            $aryUser['type'] = $type;
            return new AuthUser($aryUser);
        }

        return null;

    }

    /**
     * ユーザー種別毎モデルの設定
     *
     * @param array $credentials
     */
    private function setModel(array $credentials) {
        $this->dbModel = null;
        if (isset($credentials[AuthUser::COL_TYPE])) {
            if ($credentials[AuthUser::COL_TYPE] == AuthUser::TYPE_MANAGE) {
                $this->dbModel = new MAdminUser();
            }
        }

        if (is_null($this->dbModel)) {
            // TODO ユーザー画面向け認証
            $this->dbModel = new MAdminUser();
        }
    }


}
