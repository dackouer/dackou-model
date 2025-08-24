<?php
	namespace dackou\model\Article;

	use support\Request;
    use support\Db;

	class ArticleModel extends \dackou\Model{
		protected $table = 'Article';
        protected $title = '文章';
        protected $action = 'action';
        protected $original_value = [1=>'原创',0=>'引用'];
        protected $status_value = [-1=>'被拒绝',0=>'待审核',1=>'正常'];

        protected function getPageList(Request $request){
            try{
                $field = $this->getList($request,'field',true);
                array_push($field,"article_cate.Title as cate_title");

                $where = $this->getWhere($request);
                // var_dump($where);
                $rows = Db::table($this->table)
                            ->join('article_cate','CateID','=','article_cate.ID')
                            ->where($where)
                            ->count();
                $limit = $this->getLimit($request);

                $object = Db::table($this->table)
                            ->join('article_cate','CateID','=','article_cate.ID')
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
            $is_original = $request->post('is_original',0);
            $source_url = trim($request->post('source_url',''));
            $author = trim($request->post('author',''));
            $is_comment = $request->post('is_comment',0);
            $content = trim($request->post('content',''));

            if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
                return '无效的创作者';
            }

            $_user_class = $this->getClassName('User');
            $service = new $_user_class();
            $user = $service->getList($request,$user_id);
            if(!$user || !is_object($user)){
                return '无效的创作者';
            }

            if(empty($cate_id)){
                return 100903;
            }

            if(!is_numeric($cate_id) || !$cate_id){
                return 100910;
            }

            $service = new ArticleCateModel();
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

            if(!$is_original){
                if(empty($source_url)){
                    return 100913;
                }
                if(empty($author)){
                    return '原创作者不能为空';
                }
            }else{
                $author = $user->realname;
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
            $data['is_original'] = $is_original;
            $data['source_url'] = $source_url;
            $data['author'] = $author;
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

            $service = new ArticleCateModel();
            $cate = $service->getList($request,'option');

            $original = [];
            foreach($this->original_value as $k => $v){
                array_push($original,['label'=>$v,'value'=>(int)$k]);
            }

            $action = [
                ['type'=>'select','label'=>$this->title.'类别','prop'=>'cate_id','value'=>$data->cate_id ?? '','children'=>$cate,'rules'=>['required'=>true,'message'=>'请选择'.$this->title.'类别']],
                ['type'=>'input','label'=>$this->title.'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>$this->title.'标题不能为空']],
                ['type'=>'input','label'=>$this->title.'描述','prop'=>'desc','value'=>$data->desc ?? '','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>$this->title.'描述不能为空']],
                ['type'=>'upload','label'=>'图片','prop'=>'picture','value'=>$data->picture ?? [],'uploadAttrs'=>[
                    'type'=>'card','limit'=>5,'size'=>'default','action'=>$this->host['api'].'upload'
                ],'rules'=>['required'=>false,'message'=>'请上传图片']],
                ['type'=>'editor','label'=>$this->title.'内容','prop'=>'content','value'=>$data->content ?? '','editorOptions'=>[
                    'type'=>'wang','image'=>['server'=>$this->host['api'].'upload/editor'],'video'=>['server'=>$this->host['api'].'upload/video']
                ],'rules'=>['required'=>true,'message'=>$this->title.'内容不能为空']],
                ['type'=>'radio-group','label'=>'是否原创','prop'=>'is_original','value'=>$data->is_original ?? 1,'children'=>$original],
                ['type'=>'input','label'=>'引用地址','prop'=>'source_url','value'=>$data->source_url ?? '','hidden'=>true],
                ['type'=>'input','label'=>'原创作者','prop'=>'author','value'=>$data->author ?? '','hidden'=>true],
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
                ['type'=>'varchar','label'=>'作者','prop'=>'author'],
                ['type'=>'switch','label'=>'原创','prop'=>'is_original'],
                ['type'=>'switch','label'=>'开启评论','prop'=>'is_comment'],
                ['type'=>'varchar','label'=>'评论数','prop'=>'comments'],
                ['type'=>'varchar','label'=>'排序','prop'=>'sort'],
                ['type'=>'map','label'=>'状态','prop'=>'status','data'=>[['label'=>'拒绝','value'=>-1,'color'=>'#b00101','callback'=>'resubmit'],['label'=>'待审核','value'=>0,'color'=>'#F56C6C','callback'=>'check'],['label'=>'正常','value'=>1,'color'=>'blue']]],
                ['type'=>'varchar','label'=>'创建时间','prop'=>'create_time','width'=>180],
            ];
        }
    }
?>