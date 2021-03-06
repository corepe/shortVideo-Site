<?php
namespace App\Http\Controllers;

use App\Factory\UserFactory;
use App\Helper\DuoShuoClient;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author  jiangxianli
     * @created_at 2016-10-27 18:36:28
     */
    public function getLogin(Request $request)
    {
        //多说用户登录
        $response = DuoShuoClient::getAccessToken($request->get('code'));
        //查询用户信息
        $user = UserFactory::userModel()->where(['duo_shuo_id' => $response['user_id']])->first();
        if (!$user) {
            //获取多说用户信息
            $user_info = DuoShuoClient::getUserInfo($response['user_id']);
            $arr       = [
                'nick_name'     => $user_info['name'],
                'image_url'     => $user_info['avatar_url'],
                'duo_shuo_id'   => $user_info['user_id'],
                'duo_shuo_info' => json_encode($user_info),
            ];
            //创建新用户
            $user = UserFactory::createUser($arr);
        }
        //用户登录
        \Auth::login($user);

        return redirect('/');
    }

    /**
     * 退出登录
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author  jiangxianli
     * @created_at 2016-10-27 18:38:50
     */
    public function getLogout(Request $request)
    {
        if (\Auth::check()) {
            \Auth::logout();
        }

        return redirect('/');
    }
}