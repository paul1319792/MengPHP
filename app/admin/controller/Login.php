<?php
/**
 * +------------------------------------------------------
 * | Copyright (c) 2016-2018 http://www.majiameng.com
 * +------------------------------------------------------
 * | MengPHP后台框架[基于ThinkPHP5开发]
 * +------------------------------------------------------
 * | Author: 马佳萌 <666@majiameng.com>,QQ:879042886
 * +------------------------------------------------------
 * | DateTime: 2017/1/26 12:14
 * +------------------------------------------------------
 */
namespace app\admin\controller;
use org\Verify;
use app\common\controller\Common;

/**
 * 后台登陆控制器
 * @package app\admin\controller
 */
class Login extends Common
{
    /**
     * 登陆页面
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     */
    public function index()
    {
        $model = model('AdminUser');

        if ($this->request->isPost()) {
            $username = input('post.username/s');
            $password = input('post.password/s');
            $code = input('post.code/s');

            //验证码核验
//            $verify = new Verify();
//            if (!$verify->check($code)) {
//                return json(['code' => -4, 'data' => '', 'msg' => '验证码错误']);
//            }

            if (!$model->login($username, $password)) {
                return $this->error($model->getError(), url('index'));
            }
            return $this->success('登陆成功，页面跳转中...', url('index/index'));
        }

        if ($model->isLogin()) {
            $this->redirect(url('index/index', '', true, true));
        }

        return $this->fetch('login/index');
    }

    /**
     * 验证码图片生成
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     */
    public function checkverify(){
        $verify = new Verify();
        $verify->imageH = 32;
        $verify->imageW = 100;
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 14;
        return $verify->entry();
    }


    /**
     * 首页 页面
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     * @return mixed
     */
    public function index_page()
    {
        return $this->fetch('index/index_page');
    }



    /**
     * 退出登陆
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     */
    public function logout(){
        model('AdminUser')->logout();
        $this->redirect(ROOT_DIR);
    }


    /**
     * 图标选择
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     */
    public function icon() {
        return $this->fetch();
    }

    /**
     * 解锁屏幕
     * @author 马佳萌 <666@majiameng.com>
     * @return mixed
     */
    public function unlocked()
    {
        $_pwd = input('post.password');
        $model = model('AdminUser');
        $login = $model->isLogin();
        if (!$login) {
            return $this->error('登录信息失效，请重新登录！');
        }
        $password = $model->where('id', $login['uid'])->value('password');
        if (!$password) {
            return $this->error('登录异常，请重新登录！');
        }
        if (!password_verify($_pwd, $password)) {
            return $this->error('密码错误，请重新输入！');
        }
        return $this->success('解锁成功');
    }
}
