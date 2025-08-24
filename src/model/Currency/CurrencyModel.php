<?php
	namespace dackou\model\Currency;

	use support\Request;

	class CurrencyModel extends \dackou\Model{
		protected $table = 'Currency';

        /**
         * [validate description]
         * @param  Request $request [description]
         * @param  boolean $flag    [description]
         * @return [type]           [description]
         */
        protected function validate(Request $request,$id = 0,$obj = null){
            $title = trim($request->post('currency_name',''));
            $symbol = trim($request->post('symbol',''));

            if(empty($title) || !$title){
                return 100721;
            }

            if($this->checkExists(['CurrencyName' => $title],$id)){
                return 100722;
            }

            if(empty($symbol) || !$symbol){
                return 100723;
            }

            if($this->checkExists(['Symbol' => $symbol],$id)){
                return 100724;
            }

            $data['title'] = $title;
            $data['symbol'] = $symbol;

            return $data;
        }

        /**
         * [getActionList description]
         * @param  Request $request [description]
         * @return [type]           [description]
         */
        protected function getActionList(Request $request,$id = 0): array
        {
            return [
                [ "type"=>"input", "label"=>"币种名称", "value"=>"", "prop"=>"currency_name", "rules"=>[ "required"=>true, "message"=>"币种名称不能为空" ]],
                [ "type"=>"input", "label"=>"标识符", "value"=>"", "prop"=>"symbol", "rules"=>[ "required"=>true, "message"=>"标识符不能为空" ]],
                [ "type"=>"switch", "label"=>"设为默认", "value"=>"0", "prop"=>"is_default"]
            ];
        }
	}
?>