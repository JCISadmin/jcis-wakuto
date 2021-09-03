<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * 認証ユーザーモデル
 *
 * @property string userId
 * @property mixed $name
 * @property mixed $mail
 * @property mixed $companyId
 * @property mixed $departmentJob
 * @property string $loginDatetime
 */
class AuthUser implements Authenticatable
{

    const COL_TYPE = 'type';
    const TYPE_MANAGE = 1;
    const TYPE_USER = 0;

    protected array $attributes;

    /**
     * コンストラクタ
     *
     * @param array $attributes
     */
    public function __construct(array $attributes) {
        $this->attributes = $attributes;
    }


    /**
     * ユーザー ユニークID
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'userId';
    }

    /**
     * ユーザー 認証ID値の取得
     * @return array|callable|null
     */
    public function getAuthIdentifier(): callable|array|null
    {
        return $this->attributes;
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
        dd('getAuthPassword');
    }

    /**
     * ログイン維持用のtoken
     *   未使用なのでNULL応答
     *
     * @return null
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
        dd('setRememberToken');
    }

    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
        dd('getRememberTokenName');
    }

    /**
     * getter
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * setter
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * isset
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * unset
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

}
