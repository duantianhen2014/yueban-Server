<?php

namespace App\Api;

use App\User;
use Dingo\Api\Routing\Helpers;
use Toplan\Sms\Facades\SmsManager;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Http\Request;
use Illuminate\Routing\Controller;


class AuthController extends Controller
{

    use Helpers;


    public function sendCheckCode(Request $request)
    {
        // 添加用户请求的标识
        $request->request->add([
            'access_token' => $request->mobile
        ]);
        $validate = SmsManager::validateFields();
        if ($validate["success"] == false){
            return $this->response()->error('请求错误,请确认手机号是否有误',400);
        }
        $result = SmsManager::requestVerifySms();

        if ($result["success"] == true){
            return $this->response()->created();
        }

        return $this->response()->errorInternal('验证码发送失败，请重试');

    }

    public function register(Request $request){

        // 添加用户请求的标识
        $request->request->add([
            'access_token' => $request->mobile
        ]);
        //验证数据
        $validator = Validator::make($request->all(), [
            'mobile'     => 'required|confirm_mobile_not_change|confirm_rule:mobile_required|unique:users',
            'verifyCode' => 'required|verify_code',
            'password' => 'required|min:6'
        ],[
            'verifyCode.verify_code' => '验证码不匹配',
            'mobile.confirm_rule' => '确认手机号输入是否有误',
            'mobile.confirm_mobile_not_change' => '手机号或验证码不正确'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            SmsManager::forgetState();

            return $this->response()->error($errors,400);
        }
        User::create([
            'mobile' => $request->mobile,
            'password' => bcrypt($request->password)
        ]);
        return $this->loginUser($request);

    }


    public function loginUser(Request $request){

        //验证数据
        $validator = Validator::make($request->all(), [
            'mobile'     => 'required|zh_mobile',
            'password' => 'required|min:6'
        ],[
            'mobile.zh_mobile' => '确认手机号输入是否有误'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->response()->error($errors);
        }
        $credentials = $request->only('mobile', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response()->error('用户名或密码错误',404);
            }
        } catch (JWTException $e) {
            return $this->response()->error('服务器错误，不能创建token',500);
        }
        return compact('token');

    }

}