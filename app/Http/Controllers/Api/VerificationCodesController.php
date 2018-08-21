<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Providers\EasySmsServiceProvider;
use Composer\Cache;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if(!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if(!hash_equals($request->captcha_code, $captchaData['code'])) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaData['phone'];

        if(!app()->environment('production')) {
            $code = '1234';
        } else {
            // $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            $code = random_int(1111, 9999);

            try {
                $result = $easySms->send($phone, [
                    'content' => "【赵庆昌】您的验证码是{$code}。如非本人操作，请忽略本短信",
                ]);
            } catch (NoGatewayAvailableException $e) {
                $message = $e->getException('yunpian')->getMessage();
                return $this->response->errorInternal($message ?? '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinute(10);

        \Cache::put($key, ['phone' => $phone, 'code'=>$code], $expiredAt);
        \Cache::forget($request->captcha_key);

        return $this->response->array([
            'key' => $key,
            'expiredAt' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);

    }
}