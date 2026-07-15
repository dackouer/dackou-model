<?php
	namespace dackou\model\Service;

	use support\Request;

	class ServiceCateModel extends \dackou\Model{
		protected $table = 'ServiceCate';
        protected $title = '服务类别';

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
	}
?>