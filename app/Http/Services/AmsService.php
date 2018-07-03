<?php
/**
 * Created by PhpStorm.
 * User: Hainan
 * Date: 2018/4/19
 * Time: 11:25
 */

namespace App\Http\Services;
use DianHun\Ams\Ams;
use DianHun\Ams\User;
use DianHun\Util\Http\Client;
use App\Models\User as AdminUser;


class AmsService
{
    private $appId = 1006;
    private $sessionKey = 'ams';

    public function __construct()
    {
        $this->ams = new Ams($this->appId);
    }

    /**
     * @param $key
     * @return array
     */
    public function callback($key)
    {
        $option = [
            'timeOut' => 10,
            'connectTimeOut' => 30
        ];
        $client = new Client($option);
        $lib = new User($this->ams, $key, $client);
        $data = $lib->login();
        $data['user'] = '';
        if ($data['code'] == User::SUCCESS_CODE) {
            $this->keepLoginState($data['data']);
            $user = $this->update_userInfo($data['data']);
            $data['user'] = $user;
        }
        return $data;
    }

    /*
     * @return
     * */

    private function update_userInfo($data){
        $user =  AdminUser::where('user',$data['user'])->first();
        $ip = request()->ip();
        if(!empty($user)){
            $user->last_ip = $ip;
            $user->last_login_time = date('Y-m-d H:i:s');
            $user->save();
        }else{
            $user = new AdminUser();
            $user->last_ip = $ip;
            $user->last_login_time = date('Y-m-d H:i:s');
            $user->name = $data['name'];
            $user->user = $data['user'];
            $user->save();
        }
        return $user;
     }

    /**
     * @param $callbackUrl
     * @return string
     */
    public function login($callbackUrl)
    {
        $lib = new Ams($this->appId);
        $url = $lib->getLoginUrl($callbackUrl);
        return $url;
    }

    /**
     * @param $callbackUrl
     * @return string
     */
    public function logout($callbackUrl)
    {
        $url = $this->ams->getLogoutUrl($callbackUrl);
        return $url;
    }

    /**
     * @return array
     */
    public function getUser()
    {
        if (!session($this->sessionKey)) {
            return [];
        }
        return session([$this->sessionKey]);
    }

    /**
     * @param $data
     */
    private function keepLoginState($data)
    {
        session([$this->sessionKey => $data]);
    }



}