<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

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
     * @return string|void
     */
    public function getAuthIdentifierName()
    {
        return 'userId';
    }

    /**
     * ユーザー 認証ID値の取得
     * @return mixed|void
     */
    public function getAuthIdentifier()
    {
        return $this->attributes;
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
        dd('getAuthPassword');
    }

    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
        dd('getRememberToken');
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
