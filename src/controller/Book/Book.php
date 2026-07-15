<?php
	namespace dackou\controller\Book;

	use support\Request;
	use dackou\Json;

	class Book extends \dackou\Controller{
		protected $table = 'Book';

		public function home(Request $request){
			$data = [
				['id'=>1,'title'=>'A','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'Amy Chen','face'=>'/static/avatar.jpg','msg'=>'有空一起做护理'],
					]
				],
				['id'=>2,'title'=>'B','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'Brian Lee','face'=>'/static/avatar.jpg','msg'=>'周末再约'],
					]
				],
				['id'=>3,'title'=>'C','children'=>[
						['cate_id'=>3,'cate_name'=>'技师','uid'=>11077991,'nickname'=>'陈可可','face'=>'/static/avatar.jpg','msg'=>'好用的服务都会记下来'],
					]
				],
				['id'=>4,'title'=>'D','children'=>[
						['cate_id'=>3,'cate_name'=>'技师','uid'=>11077991,'nickname'=>'大辅','face'=>'/static/avatar.jpg','msg'=>'有需要直接喊我'],
					]
				],
				['id'=>5,'title'=>'E','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'KSDf','face'=>'/static/avatar.jpg','msg'=>'下次约到再发你'],
					]
				],
				['id'=>6,'title'=>'F','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'Fiona Wang','face'=>'/static/avatar.jpg','msg'=>'有新店告诉我'],
					]
				],
				['id'=>7,'title'=>'G','children'=>[
						['cate_id'=>2,'cate_name'=>'店铺','uid'=>11077991,'nickname'=>'最近预约门市','face'=>'/static/avatar.jpg','msg'=>'营业中，讯息一般5分钟内回复'],
					]
				],
				['id'=>8,'title'=>'H','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'今日预约担当','face'=>'/static/avatar.jpg','msg'=>'有空档会第一时间回复'],
					]
				],
				['id'=>9,'title'=>'I','children'=>[
						['cate_id'=>5,'cate_name'=>'服务号','uid'=>11077991,'nickname'=>'NeeDo客服','face'=>'/static/avatar.jpg','msg'=>'售后和平台协助都可以直接说'],
					]
				],
				['id'=>10,'title'=>'J','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>11,'title'=>'K','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>12,'title'=>'L','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>13,'title'=>'M','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>14,'title'=>'N','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>15,'title'=>'O','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>16,'title'=>'P','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>17,'title'=>'Q','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>18,'title'=>'R','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>19,'title'=>'S','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>20,'title'=>'T','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>21,'title'=>'U','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>22,'title'=>'V','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>23,'title'=>'W','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>24,'title'=>'X','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>25,'title'=>'Y','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
				['id'=>26,'title'=>'Z','children'=>[
						['cate_id'=>1,'cate_name'=>'','uid'=>11077991,'nickname'=>'售后顾问','face'=>'/static/avatar.jpg','msg'=>'有问题可以直接回我'],
					]
				],
			];

			return Json::show($data);
		}
	}
?>