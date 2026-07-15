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
            $title = trim($request->post('title',''));
            $symbol = trim($request->post('symbol',''));

            if(empty($title) || !$title){
                return 100721;
            }

            if($this->checkExists(['Title' => $title],$id)){
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
	}
?>