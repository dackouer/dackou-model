<?php
	namespace dackou\model\Market;

	use support\Request;
	use support\Db;
	use zjkal\TimeHelper;

	class MarketPeriodModel extends \dackou\Model{
		protected $table = 'MarketPeriod';
		protected $title = '营销时段';
		

		protected function getValidList(Request $request){
			try{
				$field = ["ID as id","StartTime as start_time","EndTime as end_time"];
				$where = [['IsDel','=',0],['Status','=',1]];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				if($object){
					$data = [];
					foreach($object as $item){
						$desc = $this->getDescByTime($item->start_time,$item->end_time);
						$status = $this->getDescByTime($item->start_time,$item->end_time,true);
						$timer = $this->getRemainder($item->end_time,true);
						array_push($data,['id'=>$item->id,'title'=>$item->start_time,'desc'=>$desc,'status'=> $status,'timer'=>$timer]);
					}
					// var_dump($data);
					return $data;
				}
			}catch(\Exception $e){
				return [];
			}
		}

		private function getDescByTime($start_time,$end_time,$flag = false){
			$time = time();
			$day = date('Y-m-d',$time);
			$start_time = TimeHelper::toTimestamp($day . ' ' .$start_time);
			$end_time = TimeHelper::toTimestamp($day . ' ' .$end_time);

			if($time < $start_time){
				return $flag ? 2 : '未开始';
			}
			if($time > $end_time){
				return $flag ? 0 : '已结束';
			}

			return $flag ? 1 : '抢购中';
		}

		private function getRemainder($end_time,$flag = false){
			$time = time();
			$day = date('Y-m-d',$time);

			if($flag){
				return $day . ' ' .$end_time . ':00';
			}

			$end_time = TimeHelper::toTimestamp($day . ' ' .$end_time);
			if($end_time > $time){
				return $end_time - $time;
			}
			return 0;
		}

		protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
		{
			try{
				$field = ["ID as id","StartTime as start_time","EndTime as end_time"];
				$where = [['IsDel','=',0],['Status','=',1]];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				if($object){
					$data = [];
					foreach($object as $item){
						array_push($data,['type'=>'option','label'=>"{$item->start_time} - {$item->end_time}",'value'=>$item->id]);
					}
					return $data;
				}

				return [];
			}catch(\Exception $e){
				return [];
			}
		}
	}
?>