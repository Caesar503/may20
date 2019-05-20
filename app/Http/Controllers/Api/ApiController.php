<?php

namespace App\Http\Controllers\Api;

use App\Model\Ee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class ApiController extends Controller
{
    public function regist()
    {
        return view('regist');
    }
    //注册执行
    public function registDo()
    {
//        print_r($_POST);
//        dd($_FILES['zhizhao']);
        $photo = request()->file('zhizhao');
        //文件后缀
        $extension = $photo->getClientOriginalExtension();
        //文件名称
        $path = time() . 'test' . Str::random(8) . '.' . $extension;
        //存储
        $store_result = $photo->storeAs('zhizhao', $path);


        $_POST['zhizhao']='zhizhao/'.$path;
        unset($_POST['_token']);
        $data = $_POST;

        $id = Ee::insertGetId($data);
        if($id)
        {
            return view('Test.test',['id'=>$id]);
        }else
        {
            die('您的注册出现失误！');
        }
    }
    //生成APPID
    function getAppid($a,$b)
    {
        return strtoupper(substr(md5(time().$a.Str::random(10)).$b,8,20));
    }
    //生成KEY
    function getKey($appid)
    {
        return substr(md5($appid),6,20);
    }
    //获取access_tokern
    public function get_access($id = 0)
    {
        $res = Ee::where('id',$id)->first();
        if(!$id || !$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该用户不存在！'
            ];
            die(json_encode($arr));
        }

        if(time()-$res['a_time']<60){
            if($res['a_num']>=20){
                $arr = [
                    'err'=>5002,
                    'msg'=>'每分钟请求超过限制（20次）！！'
                ];
            }else{
                $appid = $res['appid'];
                $key = $res['key'];
                $access_token = $this->get_access_token($appid,$key);
                $r = Ee::where('id',$id)->update(['a_token'=>$access_token,'a_num'=>$res['a_num']+1,'a_guoqi'=>time()]);
                if($r){
                    $arr = [
                        'err'=>1,
                        'msg'=>'生成token成功！',
                        'data'=>[
                            'token'=>$access_token
                        ]
                    ];
                }
            }
            die(json_encode($arr));
        }else if(time()-$res['a_time']>60){
            $appid = $res['appid'];
            $key = $res['key'];
            $access_token = $this->get_access_token($appid,$key);
            $r = Ee::where('id',$id)->update(['a_token'=>$access_token,'a_time'=>time(),'a_num'=>1,'a_guoqi'=>time()]);
            if($r){
                $arr = [
                    'err'=>1,
                    'msg'=>'生成token成功！',
                    'data'=>[
                        'token'=>$access_token
                    ]
                ];
            }
            die(json_encode($arr));
        }
    }
    //获取IP
    public function get_kip($id = 0)
    {
        $res = Ee::where('id',$id)->first();
        if(!$id || !$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该用户不存在！'
            ];
            die(json_encode($arr));
        }elseif(time()-$res['a_guoqi']>3600){
            $arr = [
                'err'=>5002,
                'msg'=>'该token已经过期，请重新获取！'
            ];
            die(json_encode($arr));
        }

        //查询次数
        $k = 'ip:num:'.$id;
        $k1 = 'ip:time:'.$id;
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
        $arr = [
            'err'=>1,
            'msg'=>'获取客户端的ip成功！',
            'data'=>[
                'ip'=>$_SERVER['SERVER_ADDR']
            ]
        ];
//        $ip = $_SERVER['SERVER_ADDR'];
        echo json_encode($arr);
    }
    //获取UA
    public function get_kua($id = 0)
    {
        $res = Ee::where('id',$id)->first();
        if(!$id || !$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该用户不存在！'
            ];
            die(json_encode($arr));
        }elseif(time()-$res['a_guoqi']>3600){
            $arr = [
                'err'=>5002,
                'msg'=>'该token已经过期，请重新获取！'
            ];
            die(json_encode($arr));
        }

        //查询次数
        $k = 'ip:num:'.$id;
        $k1 = 'ip:time:'.$id;
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
//        dd($_SERVER['HTTP_USER_AGENT']);
        $arr = [
            'err'=>1,
            'msg'=>'获取客户端的UA成功！',
            'data'=>[
                'ip'=>$_SERVER['HTTP_USER_AGENT']
            ]
        ];
//        $ip = $_SERVER['SERVER_ADDR'];
        echo json_encode($arr);
    }
    //生成access_token
    function get_access_token($appid,$key)
    {
        return substr(time().$appid.Str::random(3).$key,8,20);
    }
    //查看状态
    public function get_status($id)
    {
        $status = Ee::where('id',$id)->first();
        if($status->a_status ==1){
            $arr = [
                'err'=>1,
                'msg'=>'您的注册正在审核中，请稍等'
            ];
        }elseif($status->a_status ==2){
            $arr = [
                'err'=>2,
                'msg'=>'您的注册审核已经通过！'
            ];;
        }elseif($status->a_status ==3){
//            header("Refresh:2;url=/regist");
            $arr = [
                'err'=>3,
                'msg'=>'您的注册审核未通过，请重新注册'
            ];
        }
        echo json_encode($arr);
    }
    //获取用户的信息
    public  function get_userinfo($id){
        $res = Ee::where('id',$id)->first()->toArray();
        if(!$id || !$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该用户不存在！'
            ];
        }elseif(time()-$res['a_guoqi']>3600){
            $arr = [
                'err'=>5002,
                'msg'=>'该token已经过期，请重新获取！'
            ];
        }else{
            //查询次数
            $k = 'info:num:'.$id;
            $k1 = 'info:time:'.$id;
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
            $arr = [
                'err'=>1,
                'msg'=>$res
            ];
        }
        die(json_encode($arr));
    }
    //生成access_token
    public function access_token($id)
    {
        $res = Ee::where('id',$id)->first()->toArray();
        //生成APPID
        $appid = $this->getAppid($res['user'],$res['code']);
        //生成KEY
        $key = $this->getKey($appid);
        //修改 该用户的appid和key
        $r = Ee::where('id',$id)->update(['appid'=>$appid,'key'=>$key]);
        if($r){
            return view('access',['id'=>$id,'appid'=>$appid,'key'=>$key]);
        }
    }
}
