<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Ee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $appid = $request->appid;
        $key = $request->key;
        $res = Ee::where(['appid'=>$appid,'key'=>$key])->first();
        if(!$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该参数无效！'
            ];
            die(json_encode($arr));
        }elseif(time()-$res['a_guoqi']>3600){
            $arr = [
                'err'=>5002,
                'msg'=>'该token已经过期！'
            ];
            die(json_encode($arr));
        }

        //查询次数
        $uid = Auth::user()->id;
        $str = substr(md5($_SERVER['REQUEST_URI']),5,15);
        $k = $str.":num".$uid;
        $k1 = $str.":time".$uid;
        $a = Redis::get($k);
        $a1 = Redis::get($k1);
        if(!$a && !$a1){
            Redis::incr($k);
            Redis::set($k1,time());
        }else{
            if(time()-$a1<60){//一分钟之内
                if($a>=20){
                    $arr = [
                        'err'=>5003,
                        'msg'=>'每分钟请求次数已经达到限制！'
                    ];
                    die(json_encode($arr));
                }else{
                    Redis::incr($k);
                }
            }else{//超过一分钟
                Redis::set($k,1);
                Redis::set($k1,time());
            }
        }
        return $next($request);
    }
}
