<?php

namespace App\Http\Controllers\Api;

use App\Model\Ee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        $_POST['uid']= Auth::id();
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
//    //生成APPID
//    function getAppid($a,$b)
//    {
//        return strtoupper(substr(md5(time().$a.Str::random(10)).$b,8,20));
//    }
//    //生成KEY
//    function getKey($appid)
//    {
//        return substr(md5($appid),6,20);
//    }
    //获取access_tokern
    public function get_access(Request $request)
    {
        $appid = $request->appid;
        $key = $request->key;
        $res = Ee::where(['appid'=>$appid,'key'=>$key])->first();
        if(!$res)
        {
            $arr = [
                'err'=>5001,
                'msg'=>'该用户注册的企业号不存在！'
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
                $r = Ee::where(['appid'=>$appid,'key'=>$key])->update(['a_token'=>$access_token,'a_num'=>$res['a_num']+1,'a_guoqi'=>time()]);
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
            $r = Ee::where(['appid'=>$appid,'key'=>$key])->update(['a_token'=>$access_token,'a_time'=>time(),'a_num'=>1,'a_guoqi'=>time()]);
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
    public function get_kip()
    {
        $arr = [
            'err'=>1,
            'msg'=>'获取客户端的ip成功！',
            'data'=>[
                'ip'=>$_SERVER['SERVER_ADDR']
            ]
        ];
        echo json_encode($arr);
    }
    //获取UA
    public function get_kua()
    {
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
    public  function get_userinfo(){
        $aa = Auth::user()->id;
        $res = Ee::where(['uid'=>$aa])->first()->toArray();
        $arr = [
            'err'=>1,
            'msg'=>$res
        ];
        die(json_encode($arr));
    }
    //生成access_token
    public function access_token($id)
    {
        $uid = Auth::user()->id;
        $res = Ee::where(['id'=>$id,'uid'=>$uid])->first();
        if($res){
            return view('access',['id'=>$id,'appid'=>$res['appid'],'key'=>$res['key']]);
        }else{
            echo "<h3>当前用户注册的企业号为".$id."的不存在！！</h3>";
        }
    }
}
