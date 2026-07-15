<?php
	namespace dackou\model\Article;

	use support\Request;
    use support\Db;

	class ArticleModel extends \dackou\Model{
		protected $table = 'Article';
        protected $title = '文章';
        protected $action = 'action';
        protected $map_exclude = ['UserID'];
        protected $original_value = [1=>'原创',0=>'引用'];
        protected $status_value = [-1=>'被拒绝',0=>'待审核',1=>'正常'];

        protected function getPageList(Request $request){
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

            if(strlen($title) < 10){
                return $this->title.'标题长度不能小于10';
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
    }
?>