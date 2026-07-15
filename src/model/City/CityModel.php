<?php
	namespace dackou\model\City;

	use support\Request;
    use support\Db;

	class CityModel extends \dackou\Model{
		protected $table = 'City';
        protected $title = '城市';
        protected $show_method = 'page';
        public $layer = 3;

        protected function getPageList(Request $request){
            try{
                $field = $this->getList($request,'field');
                $where = $this->getWhere($request);

                $pid = $request->input('pid',0);
                array_push($where,['PID','=',$pid]);

                $rows = Db::table($this->table)
                            ->where($where)
                            ->count();
                [$offset,$limit] = $this->getLimit($request);
                $result = [];

                $object = Db::table($this->table)
                            ->select(...$field)
                            ->where($where)
                            ->offset($offset)
                            ->limit($limit)
                            ->get();
                return ['rows' => $rows,'data' => $object,'layer' => 1];
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * 获取省份数据
         * @param  Request $request [description]
         * @return [type]           [description]
         */
        protected function getProvinceList(Request $request){
            $type = trim($request->input('type',''));

            if($type == 'option'){
                $label_name = $request->input('label',$this->option_label);
                $value_name = $request->input('value',$this->option_value);
                $field = ['Title as '.$label_name,'ID as '.$value_name];
            }else{
                $field = $this->getList($request,'field');
            }

            $where = [['IsDel','=',0],['PID','=',0]];

            $object = Db::table($this->table)
                        ->select(...$field)
                        ->where($where)
                        ->get();
            return $object;
        }

        /**
         * 获取某个省份的城市列表
         * @param  Request $request [description]
         * @param  integer $pid     [description]
         * @return [type]           [description]
         */
        protected function getCityList(Request $request,$pid = 0){
            $type = trim($request->input('type',''));
            if($type == 'option'){
                $label_name = $request->input('label',$this->option_label);
                $value_name = $request->input('value',$this->option_value);
                $field = ['Title as '.$label_name,'ID as '.$value_name];
            }else{
                $field = $this->getList($request,'field');
            }

            $where = [['IsDel','=',0],['PID','=',$pid]];

            $object = Db::table($this->table)
                        ->select(...$field)
                        ->where($where)
                        ->get();
            return $object;
        }

        /**
         * 获取某个城市的区县列表
         * @param  Request $request [description]
         * @param  integer $pid     [description]
         * @return [type]           [description]
         */
        protected function getDistrictList(Request $request,$pid = 0){
            $type = trim($request->input('type',''));
            if($type == 'option'){
                $label_name = $request->input('label',$this->option_label);
                $value_name = $request->input('value',$this->option_value);
                $field = ['Title as '.$label_name,'ID as '.$value_name];
            }else{
                $field = $this->getList($request,'field');
            }

            $where = [['IsDel','=',0],['PID','=',$pid]];

            $object = Db::table($this->table)
                        ->select(...$field)
                        ->where($where)
                        ->get();
            return $object;
        }

        /**
         * 获取某个区县的街道列表
         * @param  Request $request [description]
         * @param  integer $pid     [description]
         * @return [type]           [description]
         */
        protected function getStreetList(Request $request,$pid = 0){
            $type = trim($request->input('type',''));
            if($type == 'option'){
                $label_name = $request->input('label',$this->option_label);
                $value_name = $request->input('value',$this->option_value);
                $field = ['Title as '.$label_name,'ID as '.$value_name];
            }else{
                $field = $this->getList($request,'field');
            }

            $where = [['IsDel','=',0],['PID','=',$pid]];

            $object = Db::table($this->table)
                        ->select(...$field)
                        ->where($where)
                        ->get();
            return $object;
        }

        protected function getApiList(Request $request){
            $label_name = $request->input('label',$this->option_label);
            $value_name = $request->input('value',$this->option_value);
            $pid = $request->input('pid',0);

            $field = [
                'Title as '.$label_name,
                'ID as '.$value_name,
            ];
            $where = $this->getWhere($request);
            array_push($where,['PID','=',$pid]);

            $object = Db::table($this->table)
                        ->select(...$field)
                        ->where($where)
                        ->orderBy('Sort','asc')
                        ->get();
            return $object;

            /*
            $field = [
                'ID as id',
                'Title as title',
                'Level as level',
                'PID as pid',
                'Number as number',
                'Sort as sort',
                'Title as '.$label_name,
                'ID as '.$value_name,

            ];
            $where = $this->getWhere($request);

            $object = Db::table($this->table)
                    ->select(...$field)
                    ->where($where)
                    ->orderBy('Level','asc')
                    ->orderBy('Sort','asc')
                    ->get();
            if($object){
                $object = $this->child($object);
                $data = [];
                foreach($object as $obj){
                    $options[$label_name] = $obj->$label_name;
                    $options[$value_name] = "'".$obj->$value_name."'";
                    $options['children'] = [];
                    
                    $children = [];
                    if($obj->number){
                        $option = [];
                        foreach($obj->children as $items){
                            $option[$label_name] = $items->$label_name;
                            $option[$value_name] = "'{$items->$value_name}'";
                            $option['children'] = [];

                            $child = [];
                            if($items->children){
                                $opt = [];
                                foreach($items->children as $item){
                                    $opt[$label_name] = $item->$label_name;
                                    $opt[$value_name] = "'{$item->$value_name}'";
                                    array_push($child,$opt);
                                }
                            }

                            $option['children'] = $child;
                            array_push($children,$option);
                        }
                    }
                    $options['children'] = $children;

                    array_push($data,$options);
                }

                var_dump($data[3]);

                return $data;
            }
            return []; */

            /*
            $object = $this->getList($request,'province');

            foreach($object as $obj){

                $province = "\{{$label_name}: '{$obj->$label_name}',{$value_name}: '{$obj->$value_name}',children: [\n";

                $city = $this->getList($request,'city',$obj->$value_name);
                $children = "";
                foreach($city as $ct){
                    $children .= "\{{$label_name}: '{$ct->$label_name}',{$value_name}: '{$ct->$value_name}',children: [\n";

                    $area = $this->getList($request,'district',$ct->$value_name);
                    $child = "";
                    foreach($area as $ar){
                        $child .= "\{{$label_name}: '{$ar->$label_name}',{$value_name}: '{$ar->$value_name}'\},\n";
                    }

                    $children .= $child . "]\},";
                }
                $province .= $children . "]\},";
                var_dump($province);
            }

            return $object;
            */
        }

        protected function getCitynameList(Request $request,$pid = 0,$cid = 0,$did = 0,$sid = 0,$address = ''){
            $pid = $pid ? $pid : $request->input('pid',0);
            $cid = $cid ? $cid : $request->input('cid',0);
            $did = $did ? $did : $request->input('did',0);
            $sid = $sid ? $sid : $request->input('sid',0);
            $address = $address ? $address : $request->input('address','');

            $cityname = $this->getCityName($pid,$cid,$did,$sid);
            if($cityname){
                return $cityname . $address;
            }

            return '';
        }

        /**
         * [validate description]
         * @param  Request $request [description]
         * @param  boolean $flag    [description]
         * @return [type]           [description]
         */
        protected function validate(Request $request,$id = 0,$obj = null){

            $title = trim($request->post('title',''));
            $symbol = trim($request->post('symbol',''));

            if(empty($title) || !$title){
                return 100711;
            }

            if($this->checkExists(['Title' => $title],$id)){
                return 100712;
            }

            if(empty($symbol) || !$symbol){
                return 100713;
            }

            if($this->checkExists(['Symbol' => $symbol],$id)){
                return 100714;
            }

            $data['title'] = $title;
            $data['symbol'] = $symbol;

            return $data;
        }

        protected function getMapList(Request $request): array
        {
            $mode = $request->input('mode','province');
            switch($mode){
                case '':
                case 'province':
                    return [
                        ['type'=>'id','label'=>'ID','prop'=>'id'],
                        ['type'=>'varchar','label'=>'省份名称','prop'=>'title','callback'=>$mode],
                        ['type'=>'varchar','label'=>'编码','prop'=>'code'],
                        ['type'=>'varchar','label'=>'区号','prop'=>'tel_code'],
                        ['type'=>'varchar','label'=>'邮政编码','prop'=>'zip_code'],
                        ['type'=>'varchar','label'=>'拼音','prop'=>'spell'],
                        ['type'=>'varchar','label'=>'英文名','prop'=>'en_name'],
                        ['type'=>'varchar','label'=>'经度','prop'=>'longitude'],
                        ['type'=>'varchar','label'=>'纬度','prop'=>'latitude'],
                    ];
                case 'city':
                    return [
                        ['type'=>'id','label'=>'ID','prop'=>'id'],
                        ['type'=>'varchar','label'=>'省份ID','prop'=>'pid'],
                        ['type'=>'varchar','label'=>'城市名称','prop'=>'title','callback'=>$mode],
                        ['type'=>'varchar','label'=>'编码','prop'=>'code'],
                        ['type'=>'varchar','label'=>'区号','prop'=>'tel_code'],
                        ['type'=>'varchar','label'=>'邮政编码','prop'=>'zip_code'],
                        ['type'=>'varchar','label'=>'拼音','prop'=>'spell'],
                        ['type'=>'varchar','label'=>'英文名','prop'=>'en_name'],
                        ['type'=>'varchar','label'=>'经度','prop'=>'longitude'],
                        ['type'=>'varchar','label'=>'纬度','prop'=>'latitude'],
                    ];
                case 'district':
                    return [
                        ['type'=>'id','label'=>'ID','prop'=>'id'],
                        ['type'=>'varchar','label'=>'城市ID','prop'=>'pid'],
                        ['type'=>'varchar','label'=>'城市名称','prop'=>'title','callback'=>$mode],
                        ['type'=>'varchar','label'=>'编码','prop'=>'code'],
                        ['type'=>'varchar','label'=>'区号','prop'=>'tel_code'],
                        ['type'=>'varchar','label'=>'邮政编码','prop'=>'zip_code'],
                        ['type'=>'varchar','label'=>'拼音','prop'=>'spell'],
                        ['type'=>'varchar','label'=>'英文名','prop'=>'en_name'],
                        ['type'=>'varchar','label'=>'经度','prop'=>'longitude'],
                        ['type'=>'varchar','label'=>'纬度','prop'=>'latitude'],
                    ];
                default: 
                    return [
                        ['type'=>'id','label'=>'ID','prop'=>'id'],
                        ['type'=>'varchar','label'=>'城市名称','prop'=>'title','callback'=>$mode],
                        ['type'=>'varchar','label'=>'编码','prop'=>'code'],
                        ['type'=>'varchar','label'=>'区号','prop'=>'tel_code'],
                        ['type'=>'varchar','label'=>'邮政编码','prop'=>'zip_code'],
                        ['type'=>'varchar','label'=>'拼音','prop'=>'spell'],
                        ['type'=>'varchar','label'=>'英文名','prop'=>'en_name'],
                        ['type'=>'varchar','label'=>'经度','prop'=>'longitude'],
                        ['type'=>'varchar','label'=>'纬度','prop'=>'latitude'],
                    ];
            }
        }
	}
?>