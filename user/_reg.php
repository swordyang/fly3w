<?php
require_once '../lib/config.php';
$email = $_POST['email'];
$email = strtolower($email);
$passwd = $_POST['passwd'];
$name = $_POST['name'];
$repasswd = $_POST['repasswd'];
// $agree = $_POST['agree'];
$code = $user_require_invite == 0? null : $_POST['code'];

$c = new \Ss\User\UserCheck();
$code = $user_require_invite == 0? null : new \Ss\User\InviteCode($code);
if($code != null && !$code->IsCodeOk()){
    $a['msg'] = "邀请码无效";
}elseif(!$c->IsEmailLegal($email)){
    $a['msg'] = "邮箱无效";
}elseif($c->IsEmailUsed($email)){
    $a['msg'] = "邮箱已被使用";
}elseif($repasswd != $passwd){
    $a['msg'] = "两次密码输入不符";
}elseif(strlen($passwd)<6){
    $a['msg'] = "密码太短";
}elseif(strlen($name)<3){
    $a['msg'] = "用户名太短";
}elseif($c->IsUsernameUsed($name)){
    $a['msg'] = "用户名已经被使用";
}else{
    // get value
    $ref_by = $code == null? '0' : $code->GetCodeUser();
    $passwd = \Ss\User\Comm::SsPW($passwd);
    $plan = "A";
    $transfer = $a_transfer;
    $invite_num = rand($user_invite_min,$user_invite_max);
    //do reg
    $reg = new \Ss\User\Reg();
    $reg->Reg($name,$email,$passwd,$plan,$transfer,$invite_num,$ref_by);
    if($code != null){
        $code->Del();
    }
    $a['ok'] = '1';
    $a['msg'] = "注册成功";
}
echo json_encode($a);
