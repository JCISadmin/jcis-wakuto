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

    const COL_TYPE = 'type';
    const TYPE_MANAGE = 1;
    const TYPE_USER = 0;

    private $dbModel;

    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
        dd('retrieveById');
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

        $type = self::TYPE_USER;
        $this->dbModel = null;
        if (isset($credentials[self::COL_TYPE])) {
            if ($credentials[self::COL_TYPE] == self::TYPE_MANAGE) {
                $this->dbModel = new MAdminUser();
                $type = self::TYPE_MANAGE;
            }
        }

        if (is_null($this->dbModel)) {
            // TODO ユーザー画面向け認証
        }

        $user = $this->dbModel->getUserCredentials($credentials['userId'], $credentials['password']);
        return $this->getGenericUser($user, $type);

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

}
