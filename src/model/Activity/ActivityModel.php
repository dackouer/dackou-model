<?php
	namespace dackou\model\Activity;

	use support\Request;
    use support\Db;

	class ActivityModel extends \dackou\Model{
		protected $table = 'Activity';
        protected $title = '活动';
        protected $page = true;
        protected $action = 'action';
        protected $status_value = [
            ['label'=>'拒绝','value'=>-1,'color'=>'#b00101','callback'=>'resubmit'],
            ['label'=>'待审核','value'=>0,'color'=>'#F56C6C','callback'=>'check'],
            ['label'=>'正常','value'=>1,'color'=>'blue']
        ];

        protected function getPageList(Request $request){
            try{
                $field = $this->getList($request,'field',true);
                array_push($field,"activity_cate.Title as cate_title");

                $where = $this->getWhere($request);
                $cate_id = $request->input('cate_id',0);
                if($cate_id){
                    array_push($where,[$this->table.'.CateID','=',$cate_id]);
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

        /**
         * [validate description]
         * @param  Request $request [description]
         * @param  boolean $flag    [description]
         * @return [type]           [description]
         */
        protected function validate(Request $request,$id = 0,$obj = null){
            $user_id = $request->post('user_id');
            $cate_id = $request->post('cate_id');
            $title = trim($request->post('title',''));
            $keyword = trim($request->post('keyword',''));
            $desc = trim($request->post('desc',''));
            $picture = $request->post('picture',[]);
            $dtime = $request->post('dtime',[]);
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

            if(strlen($title) < 10 || strlen($title) > 40){
                return $this->title.'标题长度不能小于10或大于40';
            }

            if($this->checkExists(['Title' => $title],$id)){
                return 100905;
            }

            if(!$desc){
                return $this->title.'描述不能为空';
            }

            if(!$picture){
                // return '请上传图片';
            }

            if(!$dtime || !is_array($dtime) || count($dtime) < 2){
            	return '请选择活动时间';
            }

            if(!$username){
            	return '活动联系人不能为空';
            }

            if(!$telphone){
            	return '联系方式不能为空';
            }

            if(!$content){
                return $this->title.'内容不能为空';
            }

            $data['cate_id'] = $cate_id;
            $data['title'] = $title;
            // $data['keyword'] = $keyword;
            $data['desc'] = $desc;
            $data['pic'] = isset($picture[0]) ? $picture[0] : '';
            $data['picture'] = $picture;
            $data['start_time'] = $dtime[0];
            $data['end_time'] = $dtime[1];
            $data['username'] = $username;
            $data['telphone'] = $telphone;
            $data['is_comment'] = $is_comment;
            $data['content'] = $content;
            if(!$id){
                $data['status'] = 1;
            }

            return $data;
        }

        protected function getActionList(Request $request,$id = 0): array
        {
            if($id){
                $data = $this->getList($request,$id);
            }

            $service = new ActivityCateModel();
            $cate = $service->getList($request,'option');

            $action = [
                ['type'=>'select','label'=>$this->title.'类别','prop'=>'cate_id','value'=>$data->cate_id ?? '','children'=>$cate,'rules'=>['required'=>true,'message'=>'请选择'.$this->title.'类别']],
                ['type'=>'input','label'=>$this->title.'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>$this->title.'标题不能为空']],
                ['type'=>'input','label'=>$this->title.'描述','prop'=>'desc','value'=>$data->desc ?? '','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>$this->title.'描述不能为空']],
                ['type'=>'upload','label'=>'图片','prop'=>'picture','value'=>$data->picture ?? [],'uploadAttrs'=>[
                    'type'=>'card','limit'=>5,'size'=>'default','action'=>$this->host['api'].'upload'
                ],'rules'=>['required'=>false,'message'=>'请上传图片']],
                ['type'=>'date-picker','label'=>'活动时间','prop'=>'dtime','value'=>$id ? [$data->start_time,$data->end_time] : [],'attrs'=>['type'=>'daterange','range-separator'=>'To','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间'],'rules'=>['required'=>true,'message'=>'请选择活动时间']],
                ['type'=>'input','label'=>'活动联系人','prop'=>'username','value'=>$data->username ?? '','rules'=>['required'=>true,'message'=>'活动联系人不能为空']],
                ['type'=>'input','label'=>'联系方式','prop'=>'telphone','value'=>$data->telphone ?? '','rules'=>['required'=>true,'message'=>'活动联系方式不能为空']],
                ['type'=>'editor','label'=>$this->title.'内容','prop'=>'content','value'=>$data->content ?? '','editorOptions'=>[
                    'type'=>'emo','is_ai'=>true,'action'=>$this->host['api'].'upload/umo','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/umo'],'video'=>['server'=>$this->host['api'].'upload/video']
                ],'rules'=>['required'=>true,'message'=>$this->title.'内容不能为空']],
                ['type'=>'switch','label'=>'开启评论','prop'=>'is_comment','value'=>$data->is_comment ?? 0]
            ];

            return $action;
        }

        protected function getMapList(Request $request): array
        {
            return [
                ['type'=>'id','label'=>'ID','prop'=>'id'],
                ['type'=>'varchar','label'=>'类别','prop'=>'cate_title'],
                ['type'=>'varchar','label'=>'标题','prop'=>'title','width'=>200],
                ['type'=>'img','label'=>'图片','prop'=>'pic'],
                ['type'=>'varchar','label'=>'查看次数','prop'=>'hits'],
                ['type'=>'switch','label'=>'开启评论','prop'=>'is_comment'],
                ['type'=>'varchar','label'=>'评论数','prop'=>'comments'],
                ['type'=>'varchar','label'=>'排序','prop'=>'sort'],
                ['type'=>'map','label'=>'状态','prop'=>'status','data'=>$this->status_value],
                ['type'=>'varchar','label'=>'创建时间','prop'=>'create_time','width'=>180],
            ];
        }
    }
?>