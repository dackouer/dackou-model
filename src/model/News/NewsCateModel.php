<?php
	namespace dackou\model\News;

	use support\Request;

	class NewsCateModel extends \dackou\Model{
		protected $table = 'NewsCate';
        protected $title = '新闻类别';
        protected $page = false;

        /**
         * [validate description]
         * @param  Request $request [description]
         * @param  boolean $flag    [description]
         * @return [type]           [description]
         */
        protected function validate(Request $request,$id = 0,$obj = null){
            $title = trim($request->post('title',''));
            $pic = trim($request->post('pic',''));
            if(is_array($pic)){
                $pic = isset($pic[0]) ? $pic[0] : '';
            }
            $sort = $request->post('sort',1);

            if(empty($title) || !$title){
                return 100711;
            }

            if($this->checkExists(['Title' => $title],$id)){
                return 100712;
            }

            // if(empty($symbol) || !$symbol){
            //     return 100713;
            // }

            // if($this->checkExists(['Symbol' => $symbol],$id)){
            //     return 100714;
            // }

            $level = $request->post('level',1);
            if(empty($level)){
                $level = 1;
            }
            $pid = $request->post('pid');
            if(empty($pid)){
                $pid = 0;
            }

            $data['title'] = $title;
            $data['pic'] = $pic;
            $data['level'] = $level;
            $data['pid'] = $pid;
            $data['sort'] = $sort;
            
            return $data;
        }

        protected function getActionList(Request $request,$id = 0): array
        {
            try{
                if($id){
                    $data = $this->getList($request,$id);
                }

                $pid = $request->input('pid',0);
                $level = $request->input('level',1);
                $ilevel = [];
                for($i=1;$i<=$this->layer;$i++){
                    array_push($ilevel,['type'=>'option','label'=>$i.'级','value'=>$i]);
                }
                $ipid = [];
                if($this->layer == 2){
                    $ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['PID'=>0]);
                }elseif($this->layer == 3){
                    $ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['Level'=>2]);
                }
                $sort = $this->getMaxSort($pid);

                $action = [
                    ['type'=>'input','label'=>'类别名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'类别名称不能为空']],
                    ['type'=>'upload','label'=>'图标','prop'=>'pic','value'=>$id ? $data->pic : '','attrs'=>[
                        'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
                    ]],
                    ['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$ilevel],
                    ['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$ipid],
                    ['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort],
                ];

                return $action;
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        protected function getMapList(Request $request): array
        {
            return [
                ['type'=>'id','label'=>'ID','prop'=>'id'],
                ['type'=>'varchar','label'=>'类别名称','prop'=>'title'],
                ['type'=>'img','label'=>'图片','prop'=>'pic'],
                ['type'=>'varchar','label'=>'排序','prop'=>'sort'],
                ['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
            ];
        }
	}
?>