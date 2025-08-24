<?php
	namespace dackou\model\City;

	use support\Request;
    use support\Db;

	class CityModel extends \dackou\Model{
		protected $table = 'City';
        protected $title = '城市';
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
                return ['rows' => $rows,'data' => $object];
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