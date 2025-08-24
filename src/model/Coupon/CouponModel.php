<?php
	namespace dackou\model\Coupon;

	use support\Request;
	use zjkal\TimeHelper;

	class CouponModel extends \dackou\Model{
		protected $table = 'Coupon';
		protected $title = '优惠券';
		
		protected function validate(Request $request,$id = 0,$obj = null){
			$title = trim($request->post('title',''));
			if(!$title){
				return '优惠券名称不能为空';
			}

			if($this->checkExists(['Title'=>$title],$id)){
				return '优惠券已存在';
			}

			$denomination = $request->post('denomination',0);
			if(empty($denomination) || !is_numeric($denomination)){
				return '优惠券面额只能填写数字';
			}
			if($denomination <= 0){
				return '优惠券面额必须大于零';
			}

			$coupon_time = $request->post('coupon_time');
			if(!$coupon_time || !is_array($coupon_time) || count($coupon_time) < 2){
				return '请选择发放时间';
			}
			$start_time = $coupon_time[0];
			$end_time = $coupon_time[1];

			$total = $request->post('total',0);
			if(empty($total) || !is_numeric($total)){
				return '发放总量只能填写数字';
			}
			if($total <= 0){
				return '发放总量必须大于零';
			}

			$full_amount = $request->post('full_amount',0);
			if(!is_numeric($full_amount)){
				return '使用门槛只能填写数字';
			}

			$use_type = $request->post('use_type',0);
			if(!in_array($use_type,[0,1,2])){
				return '请选择用券时间';
			}

			if(!$use_type){
				$datetime = $request->post('datetime');
				if(!$datetime || !is_array($datetime)){
					return '请选择固定时间';
				}

				$data['use_start_time'] = TimeHelper::toTimestamp($datetime[0]);
				$data['use_end_time'] = TimeHelper::toTimestamp($datetime[1]);
			}else{
				$use_end_time = $request->post('use_end_time');
				if(empty($use_end_time) || !is_numeric($use_end_time)){
					return '领券使用天数只能填写数字';
				}
				if($use_end_time <= 0){
					return '领券使用天数必须大于零';
				}

				$data['use_start_time'] = 0;
				$data['use_end_time'] = $use_end_time;
			}

			$receive_type = $request->post('receive_type',0);
			if(!in_array($receive_type,[0,1])){
				return '请选择领取方式';
			}

			$receive_limit = $request->post('receive_limit',0);
			if(!in_array($receive_limit,[0,1,2])){
				return '请选择领取次数限制';
			}

			if($receive_limit){
				$receive_number = $request->post('receive_number',0);
				if(empty($receive_number) || !is_numeric($receive_number)){
					return '领取次数只能填写数字';
				}
				if($receive_number <= 0){
					return '领取次数必须大于零';
				}

				$data['receive_number'] = $receive_number;
			}else{
				$data['receive_number'] = 0;
			}

			$coupon_type = $request->post('coupon_type',0);
			if(!in_array($coupon_type,[0,1,2])){
				return '请选择领适用商品';
			}

			if($coupon_type){
				$goods = trim($request->post('goods',''));
				if(!$goods){
					return $coupon_type === 2 ? '请选择不可用商品' : '请选择可用商品';
				}

				$data['goods'] = $goods;
			}else{
				$data['goods'] = '';
			}

			$data['title'] = $title;
			$data['denomination'] = $denomination;
			$data['start_time'] = TimeHelper::toTimestamp($start_time);
			$data['end_time'] = TimeHelper::toTimestamp($end_time);
			$data['total'] = $total;
			$data['full_amount'] = $full_amount;
			$data['use_type'] = $use_type;
			$data['receive_type'] = $receive_type;
			$data['receive_limit'] = $receive_limit;
			$data['coupon_type'] = $coupon_type;

			return isset($data) ? $data : true;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			$time = [];
			$ctime = [];
			if($id){
				$data = $this->getList($request,$id);
				$ctime = [$this->getDateTime($data->start_time),$this->getDateTime($data->end_time)];
				$time = [$this->getDateTime($data->use_start_time),$this->getDateTime($data->use_end_time)];
			}

			$action = [];

			$title = ['type'=>'input','label'=>'优惠券名称','prop'=>'title','value'=>$id?$data->title:'','placeholder'=>'优惠券名称','rules'=>['required'=>true,'message'=>'优惠券名称不能为空']];
			$denomination = ['type'=>'input','label'=>'优惠券面额','prop'=>'denomination','value'=>$id?$data->denomination:'','placeholder'=>'优惠券面额','slot'=>['suffix'=>['value'=>'元']],'rules'=>['required'=>true,'message'=>'优惠券面额不能为空']];
			$coupon_time = ['type'=>'date-picker','label'=>'发放时间','prop'=>'coupon_time','value'=>$ctime,'attrs'=>['style'=>['width'=>'490px'],'type'=>'datetimerange','range-separator'=>'至','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间'],'rules'=>['required'=>true,'message'=>'发放时间不能为空']];
			$total = ['type'=>'input','label'=>'发放总量','prop'=>'total','value'=>$id?$data->total:'','placeholder'=>'发放总量','slot'=>['suffix'=>['value'=>'张']],'rules'=>['required'=>true,'message'=>'发放总量不能为空']];
			$full_amount = ['type'=>'input','label'=>'使用门槛','prop'=>'full_amount','value'=>$id?$data->full_amount:'','placeholder'=>'','slot'=>['prefix'=>['value'=>'订单满'],'suffix'=>['value'=>'元可用']],'rules'=>['required'=>true,'message'=>'使用门槛不能为空']];
			$use_type = ['type'=>'radio-group','label'=>'用券时间','prop'=>'use_type','value'=>$id?$data->use_type:0,'children'=>[
				['type'=>'radio','label'=>'固定时间','value'=>0],
				['type'=>'radio','label'=>'领券当日起','value'=>1],
				['type'=>'radio','label'=>'领券次日起','value'=>2],
			]];			
			$use_time = ['type'=>'date-picker','label'=>'固定时间','prop'=>'datetime','value'=>$time,'attrs'=>['style'=>['width'=>'490px'],'type'=>'datetimerange','range-separator'=>'至','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间']];
			$use_day = ['type'=>'input','label'=>'领券当日起','prop'=>'use_end_time','value'=>$id?$data->use_end_time:'','placeholder'=>'天数','hidden'=>true,'slot'=>['suffix'=>['value'=>'天内可用']]];
			$receive_type = ['type'=>'radio-group','label'=>'领取方式','prop'=>'receive_type','value'=>$id?$data->receive_type:0,'children'=>[
				['type'=>'radio','label'=>'会员手动领取','value'=>0],
				['type'=>'radio','label'=>'指定会员发放','value'=>1]
			]];
			$receive_limit = ['type'=>'radio-group','label'=>'领取次数限制','prop'=>'receive_limit','value'=>$id?$data->receive_limit:0,'children'=>[
				['type'=>'radio','label'=>'不限制','value'=>0],
				['type'=>'radio','label'=>'限制总次数','value'=>1],
				['type'=>'radio','label'=>'每日限制次数','value'=>2]
			]];
			$receive_number = ['type'=>'input','label'=>'限制总次数','prop'=>'receive_number','value'=>$id?$data->receive_number:'','placeholder'=>'领取次数','hidden'=>true,'slot'=>['suffix'=>['value'=>'次']]];
			$coupon_type = ['type'=>'radio-group','label'=>'适用商品','prop'=>'coupon_type','value'=>$id?$data->coupon_type:0,'children'=>[
				['type'=>'radio','label'=>'全部商品可用','value'=>0],
				['type'=>'radio','label'=>'指定商品可用','value'=>1],
				['type'=>'radio','label'=>'指定商品不可用','value'=>2]
			]];
			$goods = ['type'=>'input','label'=>'商品列表','prop'=>'goods','value'=>$id?$data->goods:'','placeholder'=>'商品列表'];

			array_push($action,$title,$denomination,$coupon_time,$total,$full_amount,$use_type,$use_time,$use_day,$receive_type,$receive_limit,$receive_number,$coupon_type,$goods);

			return $action;
		}
	}
?>