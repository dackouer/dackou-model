<?php
	namespace dackou\model\Activity;

	use support\Request;
    use support\Db;
    use dackou\Server;

	class ActivityModel extends \dackou\Model{
		protected $table = 'Activity';
        protected $title = '活动';
        protected $page = true;
        protected $action = 'action';
        protected $status_value = [
            ['label'=>'拒绝','value'=>-1,'color'=>'#909399'],
            ['label'=>'待审核','value'=>0,'val'=>100,'color'=>'#F56C6C'],
            ['label'=>'正常','value'=>1,'color'=>'#409EFF']
        ];
        protected $map_exclude = ['UserID'];
        protected $is_auto_check = true;
        protected $is_qrcode = true;
        protected $qrcode_host = 'https://account.heilife.com/#/detail';
        protected $qrcode_router = 'sign';
        protected $default_seat = '嘉宾号';

        protected function getPageList(Request $request){
            try{
                $field = $this->getList($request,'field',true);
                array_push($field,"activity_cate.Title as cate_title");
                $activity_user = $this->prefix . 'activity_user';
                // array_push($field,Db::raw("(SELECT COUNT(ID) FROM {$activity_user} WHERE IsSign = 1 AND IsDel = 0 AND Status = 1 AND ActivityID = ".$this->tab.".ID) as signin"));
                // array_push($field,Db::raw("(SELECT COUNT(ID) FROM {$activity_user} WHERE IsDel = 0 AND Status = 1 AND ActivityID = ".$this->tab.".ID) as signs"));
                array_push($field,Db::raw("(SELECT CONCAT(COUNT(ID),'/',(SELECT COUNT(ID) FROM {$activity_user} WHERE IsDel = 0 AND ActivityID = ".$this->tab.".ID)) FROM {$activity_user} WHERE IsSign = 1 AND IsDel = 0 AND ActivityID = ".$this->tab.".ID) as signin"));

                $where = $this->getWhere($request);
                $cate_id = $request->input('cate_id',0);
                if($cate_id){
                    array_push($where,[$this->table.'.CateID','=',$cate_id]);
                }

                $time = $request->input('time',[]);
                if($time && is_array($time)){
                    $start_time = isset($time[0]) ? $time[0] : '';
                    $end_time = isset($time[1]) ? $time[1] : '';

                    if($start_time){
                        array_push($where,[$this->table.'.StartTime','>=',$start_time]);
                    }

                    if($end_time){
                        array_push($where,[$this->table.'.EndTime','<=',$end_time]);
                    }
                }

                $status = $request->input('status','');
                if(is_numeric($status) && in_array($status, [1,2])){
                    array_push($where,[$this->table.'.Status','=',$status===2 ? 0 : $status]);
                }

                $keyword = trim($request->input('keyword',''));
                if($keyword){
                    array_push($where,[$this->table.'.Title','like','%'.$keyword.'%']);
                }
                // var_dump($where);
                $rows = Db::table($this->table)
                            ->join('activity_cate','CateID','=','activity_cate.ID')
                            ->where($where)
                            ->count();
                $limit = $this->getLimit($request);

                $object = Db::table($this->table)
                            ->join('activity_cate','CateID','=','activity_cate.ID')
                            ->select(...$field)
                            ->where($where)
                            ->orderBy($this->table.".ID",'desc')
                            ->offset($limit[0])
                            ->limit($limit[1])
                            ->get();
                if($object){
                    foreach($object as $k => $v){
                        $object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
                    }
                }

                return ['rows' => $rows,'data' => $object];
            }catch(\Exception $e){
                // var_dump($e);
                return $this->getExceptionError($e);
            }
        }

        protected function getListById(Request $request,$id = 0){
            if(!$id || !is_numeric($id) || $id < 0){
                return 100007;
            }

            $field = $this->getList($request,'field',true);
            array_push($field,"activity_cate.Title as cate_title");
            array_push($field,'Content as content');
            $where = $this->getWhere($request);
            array_push($where,[$this->table.'.ID','=',$id]);

            $object = Db::table($this->table)
                        ->join('activity_cate','CateID','=','activity_cate.ID')
                        ->select(...$field)
                        ->where($where)
                        ->first();
            if($object){
                $object->picture = $this->getDecodeData($object->picture);
                $object->state = $object->status === 1 ? 1 : -1;    // 1: 活动进行中,-1:活动不存在
                if(time() < strtotime($object->start_time)){
                    $object->state = 0;  // 未到开始时间
                }elseif(time() > strtotime($object->end_time)){
                    $object->state = 2;  // 活动已结束
                }
                $object->create_time = $this->getDateTime($object->create_time);

                $uid = $request->input('user_id',0);
                if($uid){
                    $object->user = null;
                    $service = new ActivityUserModel();
                    $user = $service->getList($request,'user',$uid,$id);
                    var_dump('aaa user: ',$user);
                    if($user && is_object($user) && property_exists($user,'seat') && !$user->seat){
                        $user->seat = $this->default_seat;
                    }
                    $object->user = $user;
                }
            }
            return $object;
        }

        protected function getQueryList(Request $request): array
        {
            $keyword = trim($request->input('keyword',''));
            $date = $request->input('date',[]);
            $status = $request->input('status','');

            $status_value = [
                ['label'=>'-All-','value'=>''],
                ['label'=>'待审核','value'=>2],
                ['label'=>'已审核','value'=>1],
            ];
            
            $action = [
                ['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$keyword,'width'=>250,'clearable'=>true,'placeholder'=>'关键词...'],
                ['type'=>'date','label'=>'时间','prop'=>'date','value'=>$date,'attrs'=>['type'=>'daterange','range-separator'=>'To','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间','value-format'=>'YYYY-MM-DD']],
                // ['type'=>'select','label'=>'状态','prop'=>'status','value'=>$status ? (int)$status : '','width'=>100,'children'=>$status_value]
            ];

            return $action;
        }

        /**
         * 会员签到
         * @param Request $request [description]
         * @param integer $aid     [description]
         */
        public function setSign(Request $request,$aid = 0){
            try{
                $aid = $aid ?: $request->input('aid',0);
                if(!$aid || !is_numeric($aid) || !$aid < 0){
                    return '无效的活动参数';
                }
                $mobile = trim($request->post('mobile',''));
                if(!$mobile){
                    return '请输入手机号码';
                }
                if(!Server::isMobile($mobile)){
                    return '手机号码格式有误,请重新输入';
                }
                $smscode = trim($request->post('smscode',''));
                if(!$smscode){
                    return '请填写短信验证码';
                }
                if(!$this->checkValidateCode($request,$smscode,'smscode')){
                    // return '验证码错误';
                }

                $_class_name = $this->getClassName('User');
                $service = new $_class_name();
                $user = $service->getList($request,'mobile',$mobile);
                // var_dump('mobile user: ',$user);
                if(!$user){
                    if(is_array($user) && isset($user['code'])){
                        return $user;
                    }
                    $user = $service->register($request,['mobile'=>$mobile]);
                    if(!$user || !is_object($user)){
                        return $user;
                    }
                }else{
                    $token = \dackou\service\Token\TokenService::generateToken($request,$user);
                    if($token){
                        $user->expire_time = $token['expire_time'];
                        $user->token = $token['token'];
                    }
                }
                // var_dump('user: ',$user);
                $service = new ActivityUserModel();
                $res = $service->getList($request,'mobile',$mobile,$aid);
                var_dump('get activity_user: ',$res);
                if(!$res){
                    $service->insertData($request,[
                        'ActivityID' => $aid,
                        'Mobile' => $mobile,
                        'IsSign' => 1,
                        'SignTime' => time(),
                        'SignIP' => $request->getRealIp($safe_mode = true)
                    ]);
                }else{
                    $service->updateData($request,[
                        'IsSign' => 1,
                        'SignTime' => time(),
                        'SignIP' => $request->getRealIp($safe_mode = true)
                    ],$res->id);
                }

                return [
                    'uid' => $user->uid,
                    'nickname' => $user->nickname,
                    'face' => $user->face,
                    'seat' => $res&&$res->seat ? $res->seat : $this->default_seat,
                    'expire_time' => $user->expire_time,
                    'token' => $user->token
                ];
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * [validate description]
         * @param  Request $request [description]
         * @param  boolean $flag    [description]
         * @return [type]           [description]
         */
        protected function validate(Request $request,$id = 0,$obj = null){
            $user_id = $request->post('user_id',62001000);
            $cate_id = $request->post('cate_id');
            $title = trim($request->post('title',''));
            $keyword = trim($request->post('keyword',''));
            $desc = trim($request->post('desc',''));
            $pic = $request->post('pic','');
            $picture = $request->post('picture',[]);
            $time = $request->post('time',[]);
            $username = trim($request->post('username',''));
            $telphone = trim($request->post('telphone',''));
            $is_comment = $request->post('is_comment',0);
            $content = trim($request->post('content',''));

            if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
                return '无效的创建者';
            }

            $_user_class = $this->getClassName('User');
            $service = new $_user_class();
            $user = $service->getList($request,$user_id);
            if(!$user || !is_object($user)){
                return '无效的创建者';
            }

            if(empty($cate_id)){
                return 100903;
            }

            if(!is_numeric($cate_id) || !$cate_id){
                return 100910;
            }

            $service = new ActivityCateModel();
            $cate = $service->getList($request,$cate_id);
            if(!$cate || !is_object($cate)){
                return 100910;
            }

            if(empty($title) || !$title){
                return 100904;
            }

            if(strlen($title) < 10){
                return $this->title.'标题长度不能小于10';
            }

            if($this->checkExists(['Title' => $title],$id)){
                return 100905;
            }

            if(!$desc){
                // return $this->title.'描述不能为空';
            }

            if(!$picture){
                // return '请上传图片';
            }

            if(!$time || !is_array($time) || count($time) < 2){
            	return '请选择活动时间';
            }

            if(strtotime($time[1]) <= strtotime($time[0])){
                return '结束时间必须大于开始时间';
            }

            if(!$username){
            	return '活动联系人不能为空';
            }

            if(!$telphone){
            	return '联系方式不能为空';
            }

            if(!$content){
                // return $this->title.'内容不能为空';
            }

            $data['cate_id'] = $cate_id;
            $data['title'] = $title;
            // $data['keyword'] = $keyword;
            $data['desc'] = $desc;
            $data['pic'] = $pic ? $pic : (isset($picture[0]) ? $picture[0] : '');
            $data['picture'] = $picture;
            $data['start_time'] = $time[0];
            $data['end_time'] = $time[1];
            $data['username'] = $username;
            $data['telphone'] = $telphone;
            $data['is_comment'] = $is_comment;
            $data['content'] = $content;
            if(!$id && $this->is_auto_check){
                $data['status'] = 1;
            }

            return $data;
        }

        protected function getActionList(Request $request,$id = 0): array
        {
            $action = $request->input('action','');
            if($action === 'export'){
                return $this->getExportAction($request,$id);
            }

            if($id){
                $data = $this->getList($request,$id);
            }

            $service = new ActivityCateModel();
            $cate = $service->getList($request,'option');

            return [
                ['type'=>'select','label'=>'类别','prop'=>'cate_id','value'=>$data->cate_id ?? '','children'=>$cate,'required'=>true],
                ['type'=>'input','label'=>'标题','prop'=>'title','value'=>$data->title ?? '','required'=>true],
                ['type'=>'input','label'=>'描述','prop'=>'desc','value'=>$data->desc ?? '','attrs'=>['type'=>'textarea'],'required'=>true],
                ['type'=>'upload','label'=>'图片','prop'=>'picture','value'=>$data->picture ?? [],'required'=>true,'attrs'=>$this->getUploadOptions('card',5)],
                ['type'=>'date','label'=>'活动时间','prop'=>'time','value'=>$id ? [$data->start_time,$data->end_time] : [],'required'=>true,'attrs'=>['type'=>'datetimerange','range-separator'=>'To','format'=>'YYYY-MM-DD HH:mm','value-format'=>'YYYY-MM-DD HH:mm','style'=>['max-width'=>'600px']]],
                ['type'=>'input','label'=>'联系人','prop'=>'username','value'=>$data->username ?? ''],
                ['type'=>'input','label'=>'联系方式','prop'=>'telphone','value'=>$data->telphone ?? ''],
                ['type'=>'editor','label'=>'活动详情','prop'=>'content','value'=>$data->content ?? '','attrs'=>$this->getEditorOptions('wang')]
            ];
        }

        protected function getMapList(Request $request): array
        {
            return [
                ['type'=>'varchar','label'=>'ID','prop'=>'id'],
                ['type'=>'varchar','label'=>'类别','prop'=>'cate_title'],
                ['type'=>'varchar','label'=>'活动标题','prop'=>'title','sortable'=>true,'width'=>250,'callback'=>'title'],
                ['type'=>'img','label'=>'图片','prop'=>'pic'],
                ['type'=>'editor','label'=>'联系人','prop'=>'username'],
                ['type'=>'editor','label'=>'联系方式','prop'=>'telphone'],
                ['type'=>'concat','label'=>'活动时间','prop'=>'start_time','delimiter'=>' - ','fields'=>['end_time'],'width'=>300],
                ['type'=>'varchar','label'=>'签到','prop'=>'signin','color'=>'#409EFF','callback'=>'sign'],
                ['type'=>'qrcode','label'=>'二维码','prop'=>'qrcode'],
                ['type'=>'varchar','label'=>'查看','prop'=>'hits','suffix'=>'次'],
                ['type'=>'varchar','label'=>'排序','prop'=>'sort'],
                ['type'=>'check','label'=>'状态','prop'=>'status','data'=>$this->status_value],
                ['type'=>'date','label'=>'创建时间','prop'=>'create_time','format'=>'YYYY-MM-DD HH:ii:ss','width'=>180],
            ];
        }
    }
?>