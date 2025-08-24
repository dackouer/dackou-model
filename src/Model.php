<?php
	/**
	 * This file is part of webman.
	 *
	 * Licensed under The MIT License
	 * For full copyright and license information, please see the MIT-LICENSE.txt
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @author    walkor<walkor@workerman.net>
	 * @copyright walkor<walkor@workerman.net>
	 * @link      http://www.workerman.net/
	 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
	 */

	namespace dackou;

	use Closure;
	use Illuminate\Contracts\Pagination\CursorPaginator;
	use Illuminate\Contracts\Pagination\LengthAwarePaginator;
	use Illuminate\Contracts\Pagination\Paginator;
	use Illuminate\Database\Eloquent\Model as BaseModel;
	use Illuminate\Database\Query\Builder;
	use Illuminate\Database\Query\Expression;
	use Illuminate\Database\Query\Grammars\Grammar;
	use Illuminate\Database\Query\Processors\Processor;
	use Illuminate\Support\Collection;
	use Illuminate\Support\LazyCollection;

	use support\Request;
	use support\Db;

	/**
	 * save
	 * @method static BaseModel make($attributes = [])
	 * @method static \Illuminate\Database\Eloquent\Builder|static withGlobalScope($identifier, $scope)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withoutGlobalScope($scope)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withoutGlobalScopes($scopes = null)
	 * @method static array removedScopes()
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereKey($id)
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereKeyNot($id)
	 * @method static \Illuminate\Database\Eloquent\Builder|static where($column, $operator = null, $value = null, $boolean = 'and')
	 * @method static BaseModel|null firstWhere($column, $operator = null, $value = null, $boolean = 'and')
	 * @method static \Illuminate\Database\Eloquent\Builder|static orWhere($column, $operator = null, $value = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static latest($column = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static oldest($column = null)
	 * @method static \Illuminate\Database\Eloquent\Collection|static hydrate($items)
	 * @method static \Illuminate\Database\Eloquent\Collection|static fromQuery($query, $bindings = [])
	 * @method static BaseModel|\Illuminate\Database\Eloquent\Collection|static[]|static|null find($id, $columns = [])
	 * @method static \Illuminate\Database\Eloquent\Collection|static findMany($ids, $columns = [])
	 * @method static BaseModel|\Illuminate\Database\Eloquent\Collection|static|static[] findOrFail($id, $columns = [])
	 * @method static BaseModel|static findOrNew($id, $columns = [])
	 * @method static BaseModel|static firstOrNew($attributes = [], $values = [])
	 * @method static BaseModel|static firstOrCreate($attributes = [], $values = [])
	 * @method static BaseModel|static updateOrCreate($attributes, $values = [])
	 * @method static BaseModel|static firstOrFail($columns = [])
	 * @method static BaseModel|static|mixed firstOr($columns = [], $callback = null)
	 * @method static BaseModel sole($columns = [])
	 * @method static mixed value($column)
	 * @method static \Illuminate\Database\Eloquent\Collection[]|static[] get($columns = [])
	 * @method static BaseModel[]|static[] getModels($columns = [])
	 * @method static array eagerLoadRelations($models)
	 * @method static LazyCollection cursor()
	 * @method static Collection pluck($column, $key = null)
	 * @method static LengthAwarePaginator paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
	 * @method static Paginator simplePaginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
	 * @method static CursorPaginator cursorPaginate($perPage = null, $columns = [], $cursorName = 'cursor', $cursor = null)
	 * @method static BaseModel|$this create($attributes = [])
	 * @method static BaseModel|$this forceCreate($attributes)
	 * @method static int upsert($values, $uniqueBy, $update = null)
	 * @method static void onDelete($callback)
	 * @method static static|mixed scopes($scopes)
	 * @method static static applyScopes()
	 * @method static \Illuminate\Database\Eloquent\Builder|static without($relations)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withOnly($relations)
	 * @method static BaseModel newModelInstance($attributes = [])
	 * @method static \Illuminate\Database\Eloquent\Builder|static withCasts($casts)
	 * @method static Builder getQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|static setQuery($query)
	 * @method static Builder toBase()
	 * @method static array getEagerLoads()
	 * @method static \Illuminate\Database\Eloquent\Builder|static setEagerLoads($eagerLoad)
	 * @method static BaseModel getModel()
	 * @method static \Illuminate\Database\Eloquent\Builder|static setModel($model)
	 * @method static Closure getMacro($name)
	 * @method static bool hasMacro($name)
	 * @method static Closure getGlobalMacro($name)
	 * @method static bool hasGlobalMacro($name)
	 * @method static static clone ()
	 * @method static \Illuminate\Database\Eloquent\Builder|static has($relation, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orHas($relation, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static doesntHave($relation, $boolean = 'and', $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orDoesntHave($relation)
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereHas($relation, $callback = null, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orWhereHas($relation, $callback = null, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereDoesntHave($relation, $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orWhereDoesntHave($relation, $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static hasMorph($relation, $types, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orHasMorph($relation, $types, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static doesntHaveMorph($relation, $types, $boolean = 'and', $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orDoesntHaveMorph($relation, $types)
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereHasMorph($relation, $types, $callback = null, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orWhereHasMorph($relation, $types, $callback = null, $operator = '>=', $count = 1)
	 * @method static \Illuminate\Database\Eloquent\Builder|static whereDoesntHaveMorph($relation, $types, $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static orWhereDoesntHaveMorph($relation, $types, $callback = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withAggregate($relations, $column, $function = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withCount($relations)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withMax($relation, $column)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withMin($relation, $column)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withSum($relation, $column)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withAvg($relation, $column)
	 * @method static \Illuminate\Database\Eloquent\Builder|static withExists($relation)
	 * @method static \Illuminate\Database\Eloquent\Builder|static mergeConstraintsFrom($from)
	 * @method static Collection explain()
	 * @method static bool chunk($count, $callback)
	 * @method static Collection chunkMap($callback, $count = 1000)
	 * @method static bool each($callback, $count = 1000)
	 * @method static bool chunkById($count, $callback, $column = null, $alias = null)
	 * @method static bool eachById($callback, $count = 1000, $column = null, $alias = null)
	 * @method static LazyCollection lazy($chunkSize = 1000)
	 * @method static LazyCollection lazyById($chunkSize = 1000, $column = null, $alias = null)
	 * @method static BaseModel|object|static|null first($columns = [])
	 * @method static BaseModel|object|null baseSole($columns = [])
	 * @method static \Illuminate\Database\Eloquent\Builder|static tap($callback)
	 * @method static mixed when($value, $callback, $default = null)
	 * @method static mixed unless($value, $callback, $default = null)
	 * @method static Builder select($columns = [])
	 * @method static Builder selectSub($query, $as)
	 * @method static Builder selectRaw($expression, $bindings = [])
	 * @method static Builder fromSub($query, $as)
	 * @method static Builder fromRaw($expression, $bindings = [])
	 * @method static Builder addSelect($column)
	 * @method static Builder distinct()
	 * @method static Builder from($table, $as = null)
	 * @method static Builder join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
	 * @method static Builder joinWhere($table, $first, $operator, $second, $type = 'inner')
	 * @method static Builder joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
	 * @method static Builder leftJoin($table, $first, $operator = null, $second = null)
	 * @method static Builder leftJoinWhere($table, $first, $operator, $second)
	 * @method static Builder leftJoinSub($query, $as, $first, $operator = null, $second = null)
	 * @method static Builder rightJoin($table, $first, $operator = null, $second = null)
	 * @method static Builder rightJoinWhere($table, $first, $operator, $second)
	 * @method static Builder rightJoinSub($query, $as, $first, $operator = null, $second = null)
	 * @method static Builder crossJoin($table, $first = null, $operator = null, $second = null)
	 * @method static Builder crossJoinSub($query, $as)
	 * @method static void mergeWheres($wheres, $bindings)
	 * @method static array prepareValueAndOperator($value, $operator, $useDefault = false)
	 * @method static Builder whereColumn($first, $operator = null, $second = null, $boolean = 'and')
	 * @method static Builder orWhereColumn($first, $operator = null, $second = null)
	 * @method static Builder whereRaw($sql, $bindings = [], $boolean = 'and')
	 * @method static Builder orWhereRaw($sql, $bindings = [])
	 * @method static Builder whereIn($column, $values, $boolean = 'and', $not = false)
	 * @method static Builder orWhereIn($column, $values)
	 * @method static Builder whereNotIn($column, $values, $boolean = 'and')
	 * @method static Builder orWhereNotIn($column, $values)
	 * @method static Builder whereIntegerInRaw($column, $values, $boolean = 'and', $not = false)
	 * @method static Builder orWhereIntegerInRaw($column, $values)
	 * @method static Builder whereIntegerNotInRaw($column, $values, $boolean = 'and')
	 * @method static Builder orWhereIntegerNotInRaw($column, $values)
	 * @method static Builder whereNull($columns, $boolean = 'and', $not = false)
	 * @method static Builder orWhereNull($column)
	 * @method static Builder whereNotNull($columns, $boolean = 'and')
	 * @method static Builder whereBetween($column, $values, $boolean = 'and', $not = false)
	 * @method static Builder whereBetweenColumns($column, $values, $boolean = 'and', $not = false)
	 * @method static Builder orWhereBetween($column, $values)
	 * @method static Builder orWhereBetweenColumns($column, $values)
	 * @method static Builder whereNotBetween($column, $values, $boolean = 'and')
	 * @method static Builder whereNotBetweenColumns($column, $values, $boolean = 'and')
	 * @method static Builder orWhereNotBetween($column, $values)
	 * @method static Builder orWhereNotBetweenColumns($column, $values)
	 * @method static Builder orWhereNotNull($column)
	 * @method static Builder whereDate($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereDate($column, $operator, $value = null)
	 * @method static Builder whereTime($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereTime($column, $operator, $value = null)
	 * @method static Builder whereDay($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereDay($column, $operator, $value = null)
	 * @method static Builder whereMonth($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereMonth($column, $operator, $value = null)
	 * @method static Builder whereYear($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereYear($column, $operator, $value = null)
	 * @method static Builder whereNested($callback, $boolean = 'and')
	 * @method static Builder forNestedWhere()
	 * @method static Builder addNestedWhereQuery($query, $boolean = 'and')
	 * @method static Builder whereExists($callback, $boolean = 'and', $not = false)
	 * @method static Builder orWhereExists($callback, $not = false)
	 * @method static Builder whereNotExists($callback, $boolean = 'and')
	 * @method static Builder orWhereNotExists($callback)
	 * @method static Builder addWhereExistsQuery($query, $boolean = 'and', $not = false)
	 * @method static Builder whereRowValues($columns, $operator, $values, $boolean = 'and')
	 * @method static Builder orWhereRowValues($columns, $operator, $values)
	 * @method static Builder whereJsonContains($column, $value, $boolean = 'and', $not = false)
	 * @method static Builder orWhereJsonContains($column, $value)
	 * @method static Builder whereJsonDoesntContain($column, $value, $boolean = 'and')
	 * @method static Builder orWhereJsonDoesntContain($column, $value)
	 * @method static Builder whereJsonLength($column, $operator, $value = null, $boolean = 'and')
	 * @method static Builder orWhereJsonLength($column, $operator, $value = null)
	 * @method static Builder dynamicWhere($method, $parameters)
	 * @method static Builder groupBy(...$groups)
	 * @method static Builder groupByRaw($sql, $bindings = [])
	 * @method static Builder having($column, $operator = null, $value = null, $boolean = 'and')
	 * @method static Builder orHaving($column, $operator = null, $value = null)
	 * @method static Builder havingBetween($column, $values, $boolean = 'and', $not = false)
	 * @method static Builder havingRaw($sql, $bindings = [], $boolean = 'and')
	 * @method static Builder orHavingRaw($sql, $bindings = [])
	 * @method static Builder orderBy($column, $direction = 'asc')
	 * @method static Builder orderByDesc($column)
	 * @method static Builder inRandomOrder($seed = '')
	 * @method static Builder orderByRaw($sql, $bindings = [])
	 * @method static Builder skip($value)
	 * @method static Builder offset($value)
	 * @method static Builder take($value)
	 * @method static Builder limit($value)
	 * @method static Builder forPage($page, $perPage = 15)
	 * @method static Builder forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
	 * @method static Builder forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
	 * @method static Builder reorder($column = null, $direction = 'asc')
	 * @method static Builder union($query, $all = false)
	 * @method static Builder unionAll($query)
	 * @method static Builder lock($value = true)
	 * @method static Builder lockForUpdate()
	 * @method static Builder sharedLock()
	 * @method static Builder beforeQuery($callback)
	 * @method static void applyBeforeQueryCallbacks()
	 * @method static string toSql()
	 * @method static int getCountForPagination($columns = [])
	 * @method static string implode($column, $glue = '')
	 * @method static bool exists()
	 * @method static bool doesntExist()
	 * @method static mixed existsOr($callback)
	 * @method static mixed doesntExistOr($callback)
	 * @method static int count($columns = '*')
	 * @method static mixed min($column)
	 * @method static mixed max($column)
	 * @method static mixed sum($column)
	 * @method static mixed avg($column)
	 * @method static mixed average($column)
	 * @method static mixed aggregate($function, $columns = [])
	 * @method static float|int numericAggregate($function, $columns = [])
	 * @method static bool insert($values)
	 * @method static int insertOrIgnore($values)
	 * @method static int insertGetId($values, $sequence = null)
	 * @method static int insertUsing($columns, $query)
	 * @method static bool updateOrInsert($attributes, $values = [])
	 * @method static void truncate()
	 * @method static Expression raw($value)
	 * @method static array getBindings()
	 * @method static array getRawBindings()
	 * @method static Builder setBindings($bindings, $type = 'where')
	 * @method static Builder addBinding($value, $type = 'where')
	 * @method static Builder mergeBindings($query)
	 * @method static array cleanBindings($bindings)
	 * @method static Processor getProcessor()
	 * @method static Grammar getGrammar()
	 * @method static Builder useWritePdo()
	 * @method static static cloneWithout($properties)
	 * @method static static cloneWithoutBindings($except)
	 * @method static Builder dump()
	 * @method static void dd()
	 * @method static void macro($name, $macro)
	 * @method static void mixin($mixin, $replace = true)
	 * @method static mixed macroCall($method, $parameters)
	 */
	class Model extends BaseModel
	{
		const CREATED_AT = 'CreateTime';
    	const UPDATED_AT = 'UpdateTime';
    	protected $dateFormat = 'U';
    	public $timestamps = true;
    	protected $tabdata = [];
    	protected $dbname = 'ibtgs';
    	protected $prefix = 'ibt_';
    	protected $tab = '';
    	protected $title = '';
    	protected $is_schema = false;
    	protected $page = false;
    	protected $orderBy = 'asc';
    	protected $delete = true;
    	protected $truncate = true;
    	public $layer = 1;
    	public $digit = 1;
    	protected $pagesize = 10;
    	protected $is_size = false;
    	protected $is_total = true;
    	protected $is_jumper = false;
    	protected $align = 'center';
    	protected $option_key = '';
    	protected $show_method = 'page';
    	protected $code_length = 8;
    	protected $idzero = false;
    	protected $editor = 'wang';
    	protected $is_ai = true;
    	protected $field = [];
    	protected $fields = [];
    	protected $system = [];
    	protected $config = [];
    	protected $is_cate = false;
    	protected $cate_table = '';
    	protected $config_table = '';
    	protected $import_file = '';
    	protected $init_data = [];
    	protected $action_value = [];
    	protected $import_field = [];
    	protected $color = ['primary'=>'#409EFF','success'=>'#67C23A','warning'=>'#E6A23C','danger'=>'#F56C6C','info'=>'#909399'];
    	protected $status_value = [0 => '关闭',1 => '正常'];
    	protected $gender_value = [0=>'未知',1=>'男',2=>'女'];
    	protected $host = ['api' => 'http://127.0.0.1:8787/'];
    	protected $filter_field = ['UpdateTime','DeleteTime','IsDel','Uuid','Token','Desc','Description','Authentication','Password','PayPassword','SecurityPassword','Hobby','LastLoginTime','LastLoginIP','LockedReason','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'];

    	public function __construct(){
    		if(!empty($this->table)){
    			$this->table = $this->convert($this->table,false);
    			if($this->prefix){
	    			$this->tab = $this->prefix . $this->table;
	    		}

    			$this->setConfig();
    		}
    	}

    	private function setConfig(){
    		$config = config('database') ?? [];
    		if(isset($config['connections']['mysql']['prefix'])){
    			$this->dbname = $config['connections']['mysql']['database'];
    			$this->prefix = $config['connections']['mysql']['prefix'];
    			$this->tab 	  = $this->prefix . $this->table;
    		}
    		if(isset($config['host']) && is_array($config['host']) && count($config['host'])){
    			$this->host = $config['host'];
    		}

    		$this->system = $this->getOptionData('system');
    		$this->tabdata = $this->getTableOption();
    		if($this->tabdata && is_object($this->tabdata) && property_exists($this->tabdata,'id')){
    			if($this->tabdata->title){
    				$this->title = $this->tabdata->title;
    			}
    			if($this->tabdata->table_name){
    				$this->table = $this->tabdata->table_name;
    			}
    			if($this->tabdata->primary_key){
    				$this->primaryKey = $this->tabdata->primary_key;
    			}
    			if($this->tabdata->layer && is_numeric($this->tabdata->layer) && $this->tabdata->layer > 0){
    				$this->layer = (int)$this->tabdata->layer;
    			}
    			if($this->tabdata->show_method){
    				$this->show_method = $this->tabdata->show_method;
    			}
    			if($this->tabdata->is_page){
    				$this->page = $this->tabdata->is_page ? true : false;
    			}
    			if($this->tabdata->cate_table){
    				$this->cate_table = $this->tabdata->cate_table;
    			}
    			if($this->tabdata->config_table){
    				$this->config_table = $this->tabdata->config_table;
    			}
    			if($this->tabdata->option_key){
    				$this->option_key = $this->tabdata->option_key;
    			}
    			if($this->tabdata->status_value){
    				$this->status_value = $this->getDecodeData($this->tabdata->status_value);
    			}
    			if($this->tabdata->order_by){
    				$this->order_by = $this->tabdata->order_by;
    			}
    			if($this->tabdata->is_qrcode){
    				$this->is_qrcode = $this->tabdata->is_qrcode ? true : false;
    			}
    		}

    		if(!empty($this->config_table)){

    		}

    		if($this->option_key){
    			$this->config = $this->getOptionData($this->option_key);
    		}

    		$this->fields = $this->getList(\request(),'fields');

			if(method_exists($this,'_init')){
				$this->_init();
			}
    	}

		// 设置表参数
		private function getTableOption(): mixed
		{
			try{
				$field = [
					"ID as id",
					"Title as title",
					"TableName as table_name",
					"PrimaryKey as primary_key",
					"Layer as layer",
					"ShowMethod as show_method",
					"IsPage as is_page",
					"CateTable as cate_table",
					"ConfigTable as config_table",
					"OptionKey option_key",
					"StatusValue status_value",
					"OrderBy order_by",
					"IsQrcode as is_qrcode",
					"IsCreateTime as is_create_time",
					"IsUpdateTime as is_update_time"
				];
				$data = Db::table('table')->select(...$field)->where('TableName',$this->table)->first();
				// if($data){
				// 	$field = ["ID","TableID","Comment","FieldName","MapName","IsPrimaryKey","IsKey","IsUnique","IsMust","IsShow","IsAdd","IsMod","IsSearch","ShowType","FormType","Width","Align","DefaultValue","Rules","Prefix","Suffix","Prompt","CallbackField","CallbackKey","CallbackTitle","After","Status"];
				// 	$field = $this->setFields($field);
				// 	$data->fields = Db::table('field')->select(...$field)->where([['TableID','=',$data->id],['Status','=',1]])->get();
				// }
				// var_dump($data);
				return $data ? $data : false;
			}catch(\Exception $e){
				return false;
			}
		}

		// 获取参数信息
    	protected function getOptionData($key = ''): array
    	{
    		if(!$key || empty($key) || is_null($key)){
    			return [];
    		}

    		if(!is_array($key)){
    			$key = \explode(',',$key);
    		}
    		try{
                $field = [
                    "option.ID as id",
                    "option.Title as title",
                    "option.Key as key",
                    "option.ValueType as value_type",
                    "option.DefaultValue as default_value",
                    "option.Value as value"
                ];             
                $object = Db::table("option")
                                ->select(...$field)
                                ->where([['Level','=',2],['FormType','<>','divider']])
                                ->whereIn('Tag',$key)
                                ->get(); 
                // var_dump($object);
                $result = [];
                if($object){
                    foreach($object as $item){
                    	$value = $item->value;

                    	switch($item->value_type){
                    		case 2:
                    		case 3:
                    			$value = $value ? (int)$value : 0;
                    			break;
                    		case 4:
                    			$value = $value ? \round($value,1) : 0;
                    			break;
                    		case 5:
                    			$value = $value ? \round($value,2) : 0;
                    			break;
                    		case 6:
                    			$value = $value ? \round($value,3) : 0;
                    			break;
                    		case 7:
                    			$value = $value ? \round($value,4) : 0;
                    			break;
                    		case 8:
                    			$value = $value ? ($value ? 1 : 0) : 0;
                    			break;
                    		case 9:
                    			$value = $value ? ($value ? true : false) : false;
                    			break;
                    		case 10:
                    			$value = $value ? $this->getDecodeData($value) : [];
                    			break;
                    		case 11:
                    			$value = $value ? $this->getDecodeData($value,true) : [];
                    			break;
                    		default:
                    	}
                        $result[$item->key] = $value;
                    }
                }

                return $result;
            }catch(\Exception $e){
                return [];
            }
    	}

    	protected function getSchemaList(Request $request,$table = ''){
    		try{
    			$table = $table ? $table : $this->table;
    			if(!$this->table){
    				return [];
    			}
    			$field = [
    				'field.ID as id',
    				'TableID as table_id',
    				'table.TableName as table',
    				'Comment as comment',
    				'FieldName as field_name',
    				'MapName as map_name',
    				'FieldType as field_type',
    				'Length as length',
    				'IsPrimaryKey as is_primary_key',
    				'IsKey as is_key',
    				'IsUnique as is_unique',
    				'IsMust as is_must',
    				'IsShow as is_show',
    				'IsAdd as is_add',
    				'IsMod as is_mod',
    				'IsSearch as is_search',
    				'ShowType as show_type',
    				'FormType as form_type',
    				'Width as width',
    				'field.Align as align',
    				'DefaultValue as default_value',
    				'Rules as rules',
    				'Prefix as prefix',
    				'Suffix as suffix',
    				'Prompt as prompt',
    				'CallbackField as callback_field',
    				'CallbackKey as callback_key',
    				'CallbackTitle as callback_title',
    				'After as after',
    				'field.Status as status',
    			];

    			$where = [
    				['field.IsDel','=',0],
    				['field.Status','=',1],
    				['table.TableName','=',$this->convert($this->table)]
    			];

    			$object = Db::table('field')
    						->join('table','TableID','=','table.ID')
    						->select(...$field)
    						->where($where)
    						->orderBy('field.Sort','asc')
    						->get();
    			if($object){
    				foreach($object as $key => $val){
    					$object[$key]->rules = $this->getDecodeData($object[$key]->rules);
    				}
    			}
    			return $object ? $object->toArray() : $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

        /**
         * [fetch description]
         * @param  [type] $id [description]
         * @return [type]     [description]
         */
        public function fetch($table = '',$id = 1){
            try{
                $table = !empty($table) ? $table : $this->table;
                $res = Db::select("SHOW FULL FIELDS FROM `".$this->prefix.$table."`");
                if(!$res){
                	return [];
                }
                $field = [];
                foreach($res as $item){
                	array_push($field,$item->Field . ' as ' . $this->convert($item->Field,false));
                }
                // var_dump($field);
                $result = Db::table($table)
                            ->select(...$field)
                            ->where('ID',$id)
                            ->first();
                
                return $result;
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

    	/**
    	 * 统一查询
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	public function getList(Request $request): mixed
    	{
    		$args = \func_get_args();
            \array_shift($args);

            try{
	            if(!$args || !count($args)){
	                return $this->getAllList($request);
	            }

	            if(is_numeric($args[0])){
	                return $this->getListById($request,...$args);
	            }

	            if(is_array($args[0]) || is_object($args[0])){
	                return $this->getAllList($request,...$args);
	            }

	            if(is_string($args[0])){
	                $sign = \ucfirst(strtolower($args[0]));
	                $class_name = 'get'.$sign.'List';

	                if(\method_exists($this,$class_name)){
	                	\array_shift($args);
	                    return $this->$class_name($request,...$args);
	                }
	                return 100005;
	            }

	            return $this->getAllList($request,...$args);
	        }catch(\Exception $e){
	        	return $this->getExceptionError($e);
	        }
    	}

    	/**
    	 * 统一新增
    	 * @param Request $request [description]
    	 */
    	public function add(Request $request): mixed
    	{
    		try{
	    		if(strtolower($request->method()) !== 'post'){
					return 100000;
				}

				$data = $request->post();
				if(method_exists($this,'setRequest')){
					$data = \array_merge($data,$this->setRequest($request));
				}
				if(!$data || !is_array($data)){
					return $data;
				}
				
				if(method_exists($this,'validate')){
					$res_valid = $this->validate($request);
					if($res_valid && is_array($res_valid) && count($res_valid)){
						$data = \array_merge($data,$res_valid);
					}elseif($res_valid !== true){
						return $res_valid;
					}
				}
				if(!$data || !is_array($data) || !count($data)){
	                return 100000;  //
	            }

	            if(isset($data['callback']) && \method_exists($this,$data['callback'])){
	            	$_callback = $data['callback'];
	            	return $this->$_callback($request,$data);
	            }
	    		[$keys,$vals] = $this->getList($request,'field','key');
	    		// var_dump($keys,$vals);
	    		if(!$keys || !is_array($keys) || !count($keys)){
	    			return '无效的表字段';
	    		}

	    		$param = [];
	    		foreach($data as $key => $val){
	    			if(in_array($key,$vals)){
	    				$param[$this->convert($key)] = is_array($val) ? $this->getJsonData($val) : $val;
	    			}
	    		}

	    		if(!count($param)){
	    			return 100000;
	    		}

	    		if($this->layer > 1 && $this->digit > 1 && isset($param['PID'])){
	    			$maxid = $this->getMaxid($param['PID'],$this->digit);
	    			if($maxid){
		    			$param[$this->convert($this->primaryKey)] = $maxid;
		    		}
	    		}

	    		if($this->fieldExists('CreateIP')){
	    			$param['CreateIP'] = $request->getRealIp($safe_mode = true);
	    		}
	    		
	    		foreach($param as $k => $v){
	    			$this->$k = $v;
	    		}

	    		$result = $this->save();
	    		// var_dump('add result: '.$result);
	    		if($result){
	    			$data = $this->getConvertData($data);
	    			$data[$this->primaryKey] = Db::getPdo()->lastInsertId();
	    			if($this->layer > 1){
                		Db::table($this->table)->where('ID',$data['pid'])->increment('Number');
                	}
                	if(isset($data['sort']) && $this->fieldExists('Sort')){
                		$pid = isset($data['pid']) ? $data['pid'] : -1;
                		$this->adjustSort($request,$data['sort'],$data[$this->primaryKey],$pid);
                	}
	    			if(method_exists($this,'setExcute')){
	    				$result = $this->setExcute($request,$data);
	    				if($result !== false){
	    					return $data;
	    				}
	    				return '数据插入成功但执行回调失败';
	    			}

	    			return $data;
	    		}

	    		return '数据新增失败';
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	/**
    	 * 修改数据
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	public function mod(Request $request,$id = 0): mixed
    	{
    		try{
	    		if(strtolower($request->method()) !== 'post'){
					return 100000;
				}

	    		if(!$id){
	    			return 100007;
	    		}
	    		
	    		$data = $this->getList($request,$id);
	    		if(!$data || !is_object($data) || !property_exists($data,$this->primaryKey)){
	    			return '数据不存在或已被删除';
	    		}
	    		$post = $request->post();
				if(method_exists($this,'setRequest')){
		    		$post = \array_merge($post,$this->setRequest($request,$id));
		    	}
	    		if(!$post || !is_array($post)){
	    			return '无效的数据';
	    		}
	    		if(method_exists($this,'validate')){
		    		$res_valid = $this->validate($request,$id,$data);
		    		if(is_array($res_valid) || $res_valid === true){
		    			if(is_array($res_valid)){
		    				$post = \array_merge($post,$res_valid);
		    			}
		    		}else{
		    			return $res_valid;
		    		}
		    		
		    	}
				if(!$post || !is_array($post)){
	    			return '空数据';
	    		}

	            if(isset($post['callback']) && \method_exists($this,$post['callback'])){
	            	$_callback = $post['callback'];
	            	return $this->$_callback($request,$post,$id);
	            }

	    		[$keys,$vals] = $this->getList($request,'field','key');
	    		if(!$keys || !is_array($keys) || !count($keys)){
	    			return '无效的表字段';
	    		}

	    		$param = [];
    			foreach($post as $key => $val){
	    			$key = $this->convert($key);
	    			if(in_array($key,$keys) && !in_array($key,[$this->convert($this->primaryKey)])){
	    				$param[$key] = is_array($val) ? $this->getJsonData($val) : $val;
	    			}
	    		}
	    		if(!$param || !count($param)){
	    			return '无效的数据参数';
	    		}

	    		if($this->fieldExists('UpdateIP')){
	    			$param['UpdateIP'] = $request->getRealIp($safe_mode = true);
	    		}
	    		$result = Db::table($this->table)->where($this->convert($this->primaryKey),$id)->update($param);
	    		// var_dump('result: '.$result);
	    		if($result !== false){
	    			$data = $this->getConvertData($data);
                	if(isset($param['Sort']) && $this->fieldExists('Sort')){
                		$pid = isset($param['PID']) ? $param['PID'] : -1;
                		$this->adjustSort($request,$param['Sort'],$id,$pid);
                	}

	    			if(method_exists($this,'setExcute')){
	    				$action = isset($post['action']) ? $post['action'] : true;
	    				return $this->setExcute($request,$data,$action);
	    			}
	    			return $data;
	    		}

	    		return '数据更新失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	// 当排序有冲突时自动调整排序
    	protected function adjustSort(Request $request,$sort,$id,$pid = -1){
    		// var_dump('id: '.$id.' pid: '.$pid.' sort: '.$sort);
    		$where = [[$this->table.'.Sort','=',$sort],[$this->table.'.ID','<>',$id]];
    		if($pid >= 0){
    			array_push($where,[$this->table.'.PID','=',$pid]);
    		}
    		$object = Db::table($this->table)
    					->select('Sort as sort')
    					->where($where)
    					->first();
    		// var_dump($object);
    		if($object){
    			if($pid >= 0){
    				$sql = "UPDATE `".$this->tab."` SET Sort = Sort + 1 WHERE PID = ? AND Sort >= ? AND ID <> ?";
    				$result = Db::select($sql,[$pid,$sort,$id]);
    			}else{
    				$sql = "UPDATE `".$this->tab."` SET Sort = Sort + 1 WHERE Sort >= ? AND ID <> ?";
    				$result = Db::select($sql,[$sort,$id]);
    			}

    			return $result !== false ? true : false;

    		}
    		return true;
    	}

    	protected function validate(Request $request,$id = 0,$obj = null){
    		if(!$this->fields){
    			$this->fields = $this->getList($request,'fields');
    		}

    		if(!$this->fields){
    			return true;
    		}
    		$data = [];
    		foreach($this->fields as $field){
    			if($field['is_must']){
    				$field_name = $field['field_name'];
    				$prop = $field['map_name'];
    				$comment = $field['comment'];

    				$value = $request->post($prop);
    				if(is_string($value)){
    					$value = trim($value);
    				}
    				if(!$value){
    					return $comment.'不能为空';
    				}

    				if($this->checkExists([$field_name=>$value],$id)){
    					return $comment.'已存在,请重新输入';
    				}

    				$data[$prop] = $value;
    			}
    		}

    		return $data;
    	}

    	/**
    	 * 初始化数据
    	 * @param Request $request [description]
    	 * @param array   $data    [description]
    	 */
    	public function setInitialize(Request $request,$data = []){
    		try{
    			Db::table($this->table)->truncate();
    			if(\method_exists($this,'setTruncate')){
    				$this->setTruncate($request);
    			}

    			$data = $data&&count($data) ? $data : $this->getDefaultData($request);
    			if(!$data || !is_array($data) || !count($data) || !is_array($data[0])){
    				return '无效的初始化数据';
    			}

    			$payload = [];
    			foreach($data as $key => $val){
    				foreach($val as $k => $v){
    					$k = $this->convert($k);
    					$payload[$key][$k] = $v;

						if(!isset($val['create_time']) && !isset($val['CreateTime'])){
							$payload[$key]['CreateTime'] = $this->timestamps ? time() : \date('Y-m-d H:i:s',time());
						}
						if(!isset($val['create_ip']) && !isset($val['CreateIP'])){
							$payload[$key]['CreateIP'] = $request->getRealIp($safe_mode = true);
						}
    				}
    			}
    			// var_dump($payload);
    			$result = Db::table($this->table)->insert($payload);
    			return $result ? count($payload) : 0;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getExportList(Request $request){
    		try{
    			$field = $this->getList($request,'field',true);
    			$where = $this->getWhere($request);

    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->get();
    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getImportfileList(Request $request){
    		try{
    			$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
	    		if($res){
	    			$fields = ['ID','Picture','Idcode','Description','Content','UserID','Status','State','CreateTime','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'];
	    			$head = [];
	    			foreach($res as $field){
	    				if(!in_array($field->Field,$fields)){
	    					array_push($head,$field->Comment);
	    				}
	    			}
	    			// var_dump($head);
	    			if($head){
	    				\dackou\service\Office\OfficeService::exported($request,$head,[]);
	    			}
	    			return $head;
	    		}
    		}catch(\Exception $e){
    			return '';
    		}
    	}

    	protected function getImportList(Request $request){
    		if($this->import_field){
    			return $this->import_field;
    		}

    		$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
    		if($res){
    			$fields = ['ID','Picture','Idcode','Description','Content','UserID','Status','State','CreateTime','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'];
    			$result = [];
    			foreach($res as $field){
    				if(!in_array($field->Field,$fields)){
    					array_push($result,$field->Field);
    				}
    			}
    			// var_dump($result);
    			return $result;
    		}
    		return [];
    	}

    	public function setImport(Request $request){
    		try{
    			$fields = $this->getList($request,'import');
    			if(!$fields || !is_array($fields) || !count($fields)){
    				return '无效的导入字段';
    			}

    			$file = $request->post('file');
    			// var_dump('file: ',$file);
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            $file = (is_array($file) && isset($file['save_path'])) ? $file['save_path'] : $file;
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            
	            $data = \dackou\service\Office\OfficeService::imported($file);
	            // var_dump($data);
	            if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }
		        \array_shift($data);
		        if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }

		        if(count($data[0]) !== count($fields)){
		        	return '导入数据字段不匹配';
		        }

		        if(\method_exists($this,'validate')){
		        	
		        }

		        $success_number = 0;
		        $failed_number = 0;
		        $keys = [];
		        $value = "";
		        $param = [];
		        $ip = $request->getRealIp($safe_mode = true);
		        $is_idcode = $this->fieldExists('Idcode');
		        $is_create_time = $this->fieldExists('CreateTime');
		        $is_create_ip = $this->fieldExists('CreateIP');

		        foreach($fields as $field){
		        	array_push($keys,"`".$this->convert($field)."`");
		        }
		        if($is_idcode){
		        	array_push($keys,"`Idcode`");
		        }
		        if($is_create_time){
		        	array_push($keys,"`CreateTime`");
		        }
		        if($is_create_ip){
		        	array_push($keys,"`CreateIP`");
		        }
		        for($i=0;$i<count($data);$i++){
		        	$value .= "(";
		        	foreach($data[$i] as $k => $v){
		        		$value .= "?,";
		        		array_push($param,$v);
		        	}
		        	if($is_idcode){
			        	$value .= "?,";
			        	array_push($param,$this->createUniqueCode('Idcode',$this->code_length));
			        }
		        	if($is_create_time){
			        	$value .= "?,";
			        	array_push($param,time());
			        }
			        if($is_create_ip){
			        	$value .= "?,";
			        	array_push($param,$ip);
			        }
		        	$value = rtrim($value,",") . "),";
		        }
		        $value = trim($value,",");
		        if(!$keys || !$value || !$param){
		        	return '无效的数据格式';
		        }

		        $sql = "INSERT INTO `".$this->tab."` (".\implode(',',$keys).") VALUES $value";
		        // var_dump('import sql: '.$sql);
		        // var_dump($param);

		        $result = Db::insert($sql,$param);
		        if($result !== false){
		        	return ['success' => count($data),'fail' => 0];
		        }
		        return '导入失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getDefaultData(Request $request){
    		return $this->init_data;
    	}

    	/**
    	 * 内部操作新增数据
    	 * @param  Request $request [description]
    	 * @param  array   $data    [description]
    	 * @return [type]           [description]
    	 */
    	public function insertData(Request $request,array $data = []): mixed
    	{
    		if(!$data || !is_array($data)){
    			return false;
    		}
    		// var_dump('批量插入数据：',$data);
    		try{
    			if(count($data) === count($data, COUNT_RECURSIVE)){
    				$temp = [];
		    		foreach($data as $k => $v){
		    			$k = $this->convert($k);
		    			$temp[$k] = $v;

		    			if(!isset($data['create_time']) && !isset($data['CreateTime'])){
		    				$temp['CreateTime'] = $this->timestamps ? time() : \date('Y-m-d H:i:s',time());
		    			}
		    			if(!isset($data['create_ip']) && !isset($data['CreateIP'])){
		    				if($this->table != 'user'){
		    					$temp['CreateIP'] = $request->getRealIp($safe_mode = true);
		    				}
		    			}
		    		}
		    		$keys = \implode(',',\array_keys($temp));
		    		$param = \array_values($temp);
		    		$vals = [];
		    		foreach($param as $k => $v){
		    			array_push($vals,'?');
		    		}
		    		$vals = \implode(',',$vals);

		    		$sql = "INSERT INTO `".$this->tab."` ($keys) VALUES ($vals)";
		    		// var_dump('insert data sql: '.$sql);
		    		$result = Db::insert($sql,$param);
		    		if($result){
		    			$data = $this->getConvertData($data);
		    			$data[$this->primaryKey] = Db::getPdo()->lastInsertId();
		    			if($this->layer > 1){
	                		Db::table($this->table)->where('ID',$data['pid'])->increment('Number');
	                	}
		    			if(method_exists($this,'setExcute')){
		    				return $this->setExcute($request,$data);
		    			}

		    			return $data;
		    		}

		    		return false;
    			}else{
    				foreach($data as $key => $val){
    					if(!isset($val['create_time']) && !isset($val['CreateTime'])){
							$data[$key]['CreateTime'] = $this->timestamps ? time() : \date('Y-m-d H:i:s',time());
						}
						if(!isset($val['create_ip']) && !isset($val['CreateIP'])){
							if($this->table == 'user'){
								$data[$key]['CreateIP'] = $request->getRealIp($safe_mode = true);
							}
						}
    				}

    				$keys = [];
    				$vals = [];
    				$param = [];
    				for($i=0;$i<count($data);$i++){
    					$str = str_repeat('?,', count($data[$i]));
            			$str = rtrim($str,',');
    					array_push($vals,"({$str})");
    					foreach($data[$i] as $k => $v){
    						if($i === 0){
    							array_push($keys,"`{$k}`");
    						}
    						$v = is_array($v) ? $this->getDecodeData($v) : $v;
    						array_push($param,$v);
    					}
    				}
    				if($keys && $vals && $param){
    					$keys = \implode(',',$keys);
    					$vals = \implode(',',$vals);
    					$sql = "INSERT INTO `".$this->tab."` ({$keys}) VALUES $vals";
    					// var_dump('insert data sql: '.$sql);
    					$result = Db::insert($sql,$param);
    					// var_dump('insert data result: '.$result);
    					return $result !== false ? true : false;
    				}

    				return false;
    			}
    			
    		}catch(\Exception $e){
    			// var_dump('errot: '.$e->getMessage());
    			return false;
    		}
    	}

    	/**
    	 * 内容操作更新或新增数据
    	 * @param  Request $request [description]
    	 * @param  array   $arr     [description]
    	 * @return [type]           [description]
    	 */
    	public function appendData(Request $request,array $arr = []): mixed
    	{
    		if(!$data || !count($data)){
    			return false;
    		}

    		try{
    			if(count($array) === count($array, COUNT_RECURSIVE)){
		    		foreach($data as $k => $v){
		    			$k = $this->convert($k);
		    			$this->$k = $v;
		    		}

		    		$result = $this->save();
		    		// var_dump('insert result: '.$result);
		    		if($result){
		    			$data = $this->getConvertData($data);
		    			$data[$this->primaryKey] = Db::getPdo()->lastInsertId();
		    			if($this->layer > 1){
	                		Db::table($this->table)->where('ID',$data['pid'])->increment('Number');
	                	}
		    			if(method_exists($this,'setExcute')){
		    				return $this->setExcute($request,$data);
		    			}

		    			return $data;
		    		}

		    		return '数据新增失败';
    			}else{
    				$param = [];
    				$keys = [];
    				$vals = [];
    				for($i=0;$i<count($data);$i++){
						foreach($data as $k => $v){
    						if($i == 0){
    							array_push($keys,"`".$this->convert($k)."`");
    						}
    						array_push($param,$v);
    						array_push($vals,'?');
    					}
    				}

    				if(!$param || !count($param) || !$keys || !count($keys) || !$vals || !count($vals)){
    					return '无效的数据';
    				}

    				$keys = implode(',',$keys);
    				$vals = implode(',',$vals);
    				$sql = "INSERT INTO `".$this->tab."` ({$keys}) VALUES ({$vals})";

    				$result = Db::insert($sql,$param);
    				if($result !== false){
    					return true;
    				}
    				return false;
    			}
    			
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	/**
    	 * 内部修改数据
    	 * @param  Request       $request [description]
    	 * @param  array         $arr     [description]
    	 * @param  mixed|integer $id      [description]
    	 * @return [type]                 [description]
    	 */
    	public function updateData(Request $request,array $arr = [],mixed $id = 0): mixed
    	{
    		try{
    			if(!$id){
    				return false;
    			}
    			// $data = $this->getListById($request,$id);
    			// if(!$data || !is_object($data)){
    			// 	return false;
    			// }
    			// var_dump('update data:',$data);
    			$keys = "";
    			$param = [];
    			foreach($arr as $key => $val){
    				$keys .= "`".$this->convert($key)."` = ?,";
    				array_push($param,$val);
    			}
    			$keys = rtrim($keys,",");
    			array_push($param,$id);
    			$sql = "UPDATE `".$this->tab."` SET {$keys} WHERE `".$this->convert($this->primaryKey)."` = ?";
    			// var_dump('update data sql: '.$sql);
    			// var_dump($param);
    			$result = Db::update($sql,$param);
    			// var_dump('update data result: '.$result);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			// var_dump($e->getMessage());
    			return false;
    		}
    	}

    	protected function getResultData($data,$flag = false){
    		if($flag){
    			foreach($data as $key => $val){
    				if(\property_exists($data[$key],'create_time')){
    					$data[$key]->create_time = $this->getDateTime($data[$key]->create_time);
    				}
    				if(\property_exists($data[$key],'update_time')){
    					$data[$key]->update_time = $this->getDateTime($data[$key]->update_time);
    				}
    				if(\property_exists($data[$key],'delete_time')){
    					$data[$key]->delete_time = $this->getDateTime($data[$key]->delete_time);
    				}
    				if(\property_exists($data[$key],'picture')){
    					$data[$key]->picture = $this->getDecodeData($data[$key]->picture);
    				}
    			}
    		}else{
    			if(\property_exists($data,'create_time')){
    				$data->create_time = $this->getDateTime($data->create_time);
    			}
    			if(\property_exists($data,'update_time')){
    				$data->update_time = $this->getDateTime($data->update_time);
    			}
    			if(\property_exists($data,'delete_time')){
    				$data->delete_time = $this->getDateTime($data->delete_time);
    			}
    			if(\property_exists($data,'picture')){
    				$data->picture = $this->getDecodeData($data->picture);
    			}
    		}
    		return $data;
    	}

    	/**
    	 * 删除数据
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	public function del(Request $request,$id = 0){
    		try{
	    		if(!$id){
	    			return 100007;
	    		}
	    		$data = $this->getList($request,$id);
	    		if(!$data || !is_object($data)){
	    			return '数据不存在或已被删除';
	    		}

	    		// if(class_exists('\app\model\Role\RoleModel')){
		    	// 	$service = new \app\model\Role\RoleModel();
		    	// 	$role = $service->getSignList($request);
		    	// 	if($role && is_object($role) && !$role->is_admin){
		    	// 		return '无操作权限';
		    	// 	}
		    	// }

	    		if(property_exists($data,'IsDel')){
	    			$result = $this->updateData($request,['IsDel'=>1],$id);
	    			// var_dump('result: '.$result);
	    			if($result !== false){
	    				if(\method_exists($this,'setExcute')){
	    					return $this->setExcute($request,$data,'del');
	    				}
	    				return true;
	    			}
	    			return false;
	    		}else{
	    			$sql = "DELETE FROM `".$this->tab."` WHERE `".$this->convert($this->primaryKey)."` = ?";
	    			$result = Db::select($sql,[$id]);
	    			if($result !== false){
	    				if(\method_exists($this,'setExcute')){
	    					return $this->setExcute($request,$data,'delete');
	    				}
	    				return true;
	    			}
	    			return false;
	    		}
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	public function clear(Request $request){
    		try{
    			// var_dump('table: '.$this->table);
    			if(!$this->truncate){
    				return '该表数据不允许清空';
    			}
    			$result = Db::table($this->table)->truncate();
                if(method_exists($this,'setTruncate')){
                    return $this->setTruncate($request);
                }
                return true;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 获取某条记录
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getListById(Request $request,$id = 0){
    		if(!$id){
    			return '数据不存在或已被删除';
    		}

    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);
    			array_push($where,[$this->convert($this->primaryKey),'=',$id]);
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->first();
    			return $object ? $this->getResultData($object) : '数据不存在或已被删除';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 获取显示列表
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getShowList(Request $request){
    		$title = $this->getTableTitle();
    		// var_dump('method: '.$this->show_method);
    		$data = $this->getList($request,$this->show_method);
    		if(!is_array($data) || (isset($data['code']) && $data['code'])){
    			return $data;
    		}
    		$rows = isset($data['rows']) ? (int)$data['rows'] : count($data);
    		$layer = isset($data['layer']) ? (int)$data['layer'] : (int)$this->layer;

    		$thead = $this->getList($request,'map');
    		// var_dump($thead);
    		foreach($thead as $k => $v){
    			if(!isset($thead[$k]['align']) && $this->align){
    				$thead[$k]['align'] = $this->align;
    			}
    		}
            $grant = $this->getList($request,'grants');
            if(is_array($grant) && isset($grant['code']) && $grant['code']){
            	return $grant;
            }
            $query = $this->getList($request,'query');

            $tabs = [];
            if($this->is_cate){
            	if($this->cate_value){
            		$tabs = [['label'=>'全部'.$title,'value'=>'all']];
            		foreach($this->cate_value as $k => $v){
            			array_push($tabs,['label'=>$v,'value'=>(int)$k]);
            		}
            	}
            }

            if($this->action_value && count($this->action_value)){
            	if($data['data'] && count($data['data'])){
	            	for($i=0;$i<count($data['data']);$i++){
	            		$data['data'][$i]->action = $this->action_value;
	            	}
	            }
                array_push($thead,['type' => 'action','prop' => 'action','label' => '操作','align' => 'center','width' => 80]);
            }
            
            // if($this->table == 'user' || $this->table == 'order' || $this->table == 'device'){
            //     array_push($thead,['type' => 'hand','prop' => 'hand','label' => '操作','align' => 'center','width' => 80]);
            // }

            if(in_array('is_modify',$grant)){
                array_push($thead,['type' => 'mod','prop' => 'mod','label' => '编辑','align' => 'center','width' => 80]);
            }
            if(in_array('is_del',$grant)){
                array_push($thead,['type' => 'del','prop' => 'del','label' => '删除','align' => 'center','width' => 80]);
            }
            $result =  [
                'title'         => $title,
                'table'         => $this->table,
                'primaryKey'    => $this->primaryKey,
                'layer'         => $layer,
                'thead'         => $thead,
                'query'         => $query,
                'grant'         => $grant,
                'tabs'			=> $tabs,
                'action'        => '',
                'page'			=> (int)$request->input('page',1),
                'pagesize'		=> (int)$this->pagesize,
                'prefix'		=> $this->prefix,
                'api'			=> $this->host['api'],
                'importFile'    => $this->import_file,
                'rows'          => $rows,
                'data'          => isset($data['data']) ? $data['data'] : $data,
                'is_size'		=> $this->is_size,
                'is_total'		=> 0,
                'is_jumper'		=> $this->is_jumper,
            ];

            foreach($data as $k => $v){
            	if(!in_array($k,['rows','data','layer'])){
            		$result[$k] = $v;
            	}
            }

            return $result;
    	}

    	/**
    	 * 获取所有数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getAllList(Request $request){
    		if($this->page){
    			return $this->getPageList($request);
    		}

    		try{
	    		$field = $this->getList($request,'field');
	    		$where = $this->getWhere($request);

	    		$args = \func_get_args();
				\array_shift($args);

				if($args && isset($args[0]) && is_array($args[0])){
					foreach($args[0] as $k => $v){
						$k = $this->convert($k);
    					if($this->fieldExists($k)){
    						array_push($where,[$k,'=',$v]);
    					}
					}
				}

				$post = $request->post();
				if($post){
					foreach($post as $k => $v){
						$k = $this->convert($k);
    					if($this->fieldExists($k)){
    						array_push($where,[$k,'=',$v]);
    					}
					}
				}

	    		$object = Db::table($this->table)
	    						->select(...$field)
	    						->where($where)
	    						->orderBy($this->convert($this->primaryKey),$this->orderBy)
	    						->get();

	    		return $object ? $this->getResultData($object,true) : [];
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	/**
    	 * 获取分页数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getPageList(Request $request){
    		try{
	    		$field = $this->getList($request,'field');
	    		$where = $this->getWhere($request);
	    		if($this->idzero){
	    			array_push($where,[$this->table.'.ID','>',0]);
	    		}

	    		$rows = self::where($where)->count();
	    		$limit = $this->getLimit($request,$this->pagesize);
	    		$object = Db::table($this->table)
	    						->select(...$field)
	    						->where($where)
	    						->orderBy($this->convert($this->primaryKey),$this->orderBy)
	    						->offset($limit[0])
	    						->limit($limit[1])
	    						->get();

	    		return ['rows' => $rows,'data' => $this->getResultData($object,true)];                                                                                   ;
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	/**
    	 * 获取搜索数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getSearchList(Request $request){
    		try{
	    		$field = $this->getList($request,'field');
	    		$where = $this->getWhere($request);

	    		$post = $request->post();
	    		if($post){
	    			foreach($post as $k => $v){
	    				$k = $this->convert($k);
	    				if($this->fieldExists($k)){
	    					array_push($where,[$k,'=',$v]);
	    				}
	    			}
	    		}

	    		$rows = self::where($where)->count();
	    		$limit = $this->getLimit($request,$this->pagesize);
	    		$object = Db::table($this->table)
	    						->select(...$field)
	    						->where($where)
	    						->orderBy($this->convert($this->primaryKey),$this->orderBy)
	    						->offset($limit[0])
	    						->limit($limit[1])
	    						->get();

	    		return ['rows' => $rows,'data' => $this->getResultData($object,true)];                                                                                   ;
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	public function getCodeData(Request $request){
    		$len = $request->input('len',($request->input('length',$this->code_length)));
    		$field = $request->input('field','');
    		if(!$field){
    			$field = $this->convert($this->table).'Code';
    		}else{
    			$field = $this->convert($field);
    		}
    		$type = $request->input('type',1);

    		return $this->createUniqueCode($field,$len,$type);
    	}

    	protected function getUserList(Request $request,$user_id = 0)
    	{
    		try{
    			$user_id = $user_id ? $user_id : $request->input('user_id',0);
    			if(!$user_id){
    				return [];
    			}

    			$field = $this->getList($request,'field');
    			// var_dump($field);
    			if(!$this->fieldExists('user_id')){
    				return [];
    			}
	    		$where = $this->getWhere($request);
    			array_push($where,[$this->table.'.UserID','=',$user_id]);

    			$rows = Db::table($this->table)
    						->where($where)
    						->count();
    			[$offset,$limit] = $this->getLimit($request);
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->get();
    			return ['rows' => $rows,'data' => $object];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 获取指定字段数据
    	 * @param  Request $request [description]
    	 * @param  array   $field   [description]
    	 * @return [type]           [description]
    	 */
    	protected function getAssignList(Request $request,array $fields = [],mixed $id = 0): mixed
    	{
    		try{
    			if(!$fields){
    				return [];
    			}
    			$field = [];
    			foreach($fields as $k => $v){
    				if(is_string($k)){
    					array_push($field,"{$k} as {$v}");
    				}else{
    					array_push($field,"{$v} as ".$this->convert($v,false));
    				}
    			}
    			// var_dump('get assign field: ',$field);
    			$where = [['IsDel','=',0]];
    			if($id){
    				array_push($where,[$this->convert($this->primaryKey),'=',$id]);
    			}
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->get();
    			return $object ? ($id?$object[0]:$object) : [];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * [getTreeList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getTreeList(Request $request): array
    	{
    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);
    			$object = Db::table($this->table)
    							->select(...$field)
    							->orderBy('Level','asc')
    							->orderBy('Sort','asc')
    							->where($where)
    							->get();
    			if($object){
    				$this->pagesize = (int)count($object);
    				$object = $this->getResultData($object,true);
    				return $this->tree($object);
    			}

    			return [];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getChildList(Request $request): array
    	{
    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);
    			$object = Db::table($this->table)
    							->select(...$field)
    							->where($where)
    							->get();
    			$object = $this->getResultData($object,true);

    			return $this->child($object);
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getParentList(Request $request,$pid = 0){
    		try{
    			if(!is_numeric($pid) || is_null($pid) || $pid < 0){
    				return [];
    			}
    			$field = $this->getList($request,'field');
    			if(!$this->fieldExists('pid')){
    				return [];
    			}
    			$where = $this->getWhere($request);
    			if(!is_array($pid)){
    				$pid = \explode(',',$pid);
    			}
    			$object = Db::table($this->table)
    						->select(...$field);
    			if($this->fieldExists('sort')){
    				$object = $object->orderBy('Sort','asc');
    			}else{
    				$object = $object->orderBy($this->convert($this->primaryKey),'asc');
    			}
    			$object = $object->where($where)
    						->whereIn('PID',$pid)
    						->get();
    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

		protected function getDefaultList(Request $request){
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				if($this->fieldExists('IsDefault')){
					array_push($where,[$this->table.'.IsDefault','=',1]);
				}else{
					return [];
				}

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

    	protected function getTopList(Request $request,$num = 1){
    		try{
    			$num = $num ? $num : $request->input('num',1);
    			if(!is_numeric($num) || $num < 1){
    				return [];
    			}

    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);

	    		$args = \func_get_args();
				\array_shift($args);
				\array_shift($args);

				if($args && is_array($args)){
					foreach($args as $k => $v){
						$k = $this->convert($k);
    					if($this->fieldExists($k)){
    						array_push($where,[$k,'=',$v]);
    					}
					}
				}

				$post = $request->post();
				if($post){
					foreach($post as $k => $v){
						$k = $this->convert($k);
    					if($this->fieldExists($k)){
    						array_push($where,[$k,'=',$v]);
    					}
					}
				}
				
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->orderBy($this->convert($this->primaryKey),'desc')
    						->limit($num);
    			if($num > 1){
    				$object = $object->get();

    				return $this->getResultData($object,true);
    			}else{
    				$object = $object->first();
    				return $this->getResultData($object);
    			}

    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

        /**
         * 生成H5二维码
         * @param  [type] $url [description]
         * @return [type]      [description]
         */
        protected function createH5Qrcode($request,$data){
            $service = new \dackou\service\Qrcode\QrcodeService();
            $result = $service->create($request,$data);
            return $result->getDataUri();
        }

        /**
         * 生成小程序二维码
         * @param  [type] $url [description]
         * @return [type]      [description]
         */
        protected function createMiniQrcode($request,$data){
            $service = new \dackou\service\Wechat\WechatService();
            // var_dump('mini qrcode data:');
            // var_dump($data);
            $result = $service->createMiniCode($request,$data);
            if(isset($result['code']) && $result['code'] === 0){
                return $result['data'];
            }
            return $result;
        }

    	protected function getQrcodeList(Request $request,$id = 0){
    		if(!$id){
    			return 100007;
    		}

    		$h5Data = [
    			'content' => 'content',
    			'text' 	  => 'H5二维码',
    			'size'	  => 1024,
    			'margin'  => 10,
    			'logo' 	  => ''
    		];
    		$miniData = [
    			'path'	=> '/pages/index/index',
    			'width' => 1024
    		];

    		$qrcodeH5 = $this->createH5Qrcode($request,$h5Data);
    		$qrcodeMini = $this->createMiniQrcode($request,$miniData);

    		try{
    			return [
    				'title' => $this->getTableTitle().'二维码',
    				'h5'	=> ['path'=>'user/info?uid='.$id,'qrcode'=>$qrcodeH5],
    				'mini'	=> ['path'=>'/pages/index/index','qrcode'=>$qrcodeMini]
    			];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getLevelList(Request $request,$level = 1){
    		try{
    			if(empty($level) || is_null($level) || !is_numeric($level) || $level <= 0){
    				return [];
    			}
    			$field = $this->getList($request,'field');
    			if(!$this->fieldExists('level')){
    				return [];
    			}
    			$where = $this->getWhere($request);
    			if(!is_array($level)){
    				$level = \explode(',',$level);
    			}
    			$object = Db::table($this->table)
    						->select(...$field);
    			if($this->fieldExists('sort')){
    				$object = $object->orderBy('Sort','asc');
    			}else{
    				$object = $object->orderBy($this->convert($this->primaryKey),'asc');
    			}
    			$object = $object->where($where)
    						->whereIn('Level',$level)
    						->get();
    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * [getKeyList description]
    	 * @param  Request $request [description]
    	 * @param  string  $key     [description]
    	 * @return [type]           [description]
    	 */
    	protected function getKeyList(Request $request,$key = ''){
    		try{
    			if((is_string($key) && empty($key)) || (is_array($key) && !count($key))){
    				return [];
    			}

    			if(is_string($key)){
    				$key = explode(',',$key);
    			}

    			$field = $this->getList($request,'field');
    			if(!$this->fieldExists('key')){
    				return [];
    			}

    			$where = $this->getWhere($request);

    			$object = Db::table($this->table)
    							->select(...$field)
    							->where($where)
    							->whereIn('Key',$key)
    							->get();
    			return $this->getResultData($object,true);
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getFormtypeList(Request $request): array
    	{
    		return [
				['type'=>'option','label'=>'文本','value'=>'1','children'=>[
					['label'=>'普通文本','value'=>'input'],
					['label'=>'加密文本','value'=>'password'],
					['label'=>'文本域','value'=>'textarea'],
				]],
				['type'=>'option','label'=>'上传','value'=>'2','children'=>[
					['label'=>'上传图片','value'=>'upload_img'],
					['label'=>'上传Logo','value'=>'upload_logo'],
					['label'=>'上传头像','value'=>'upload_face'],
					['label'=>'上传轮播图','value'=>'upload_picture'],
					['label'=>'上传文件','value'=>'upload_file'],
					['label'=>'上传证件','value'=>'upload_cert'],
					['label'=>'上传音频','value'=>'upload_voice'],
					['label'=>'上传视频','value'=>'upload_video'],
				]],
				['type'=>'option','label'=>'选择','value'=>'3','children'=>[
					['label'=>'下拉单选','value'=>'select'],
					['label'=>'下拉多选','value'=>'select_milut'],
					['label'=>'级联选择','value'=>'cascader'],
					['label'=>'复选框','value'=>'checkbox'],
					['label'=>'单选框','value'=>'radio'],
				]],
				['type'=>'option','label'=>'日期时间','value'=>'4','children'=>[
					['label'=>'年月日','value'=>'datetime'],
					['label'=>'年月日时','value'=>'datetimes'],
					['label'=>'年','value'=>'year'],
					['label'=>'月','value'=>'month'],
					['label'=>'日','value'=>'day'],
					['label'=>'时','value'=>'hour'],
				]],
				['type'=>'option','label'=>'编辑器','value'=>'5','children'=>[
					['label'=>'富文本','value'=>'editor'],
				]],
				['type'=>'option','label'=>'商品','value'=>'6','children'=>[
					['label'=>'商品属性','value'=>'goods_attr'],
					['label'=>'商品规格','value'=>'goods_spec'],
				]],
				['type'=>'option','label'=>'城市','value'=>'7','children'=>[
					['label'=>'省市区','value'=>'city'],
					['label'=>'省市区街道','value'=>'district'],
					['label'=>'省市','value'=>'province_city'],
					['label'=>'省','value'=>'province'],
				]],
				['type'=>'option','label'=>'其它','value'=>'8','children'=>[
					['label'=>'图标','value'=>'icon'],
					['label'=>'开关','value'=>'switch'],
					['label'=>'颜色','value'=>'color-picker'],
					['label'=>'排序','value'=>'sort'],
					['label'=>'多值表单','value'=>'milut_form'],
					['label'=>'分割线','value'=>'divider'],
				]]
    		];
    	}

    	protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
    	{
    		try{
    			if(!$field || count($field)){
    				if($this->field && count($this->field)){
    					$field = $this->field;
    				}else{
    					$field = $this->getList($request,'field');
    				}
    			}else{
    				for($i=0;$i<count($field);$i++){
    					$field[$i] = $field[$i] . ' as ' . $this->convert($field[$i],false);
    				}
    			}
    			if(!$field || !count($field)){
    				return [];
    			}

    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($this->table.'.IsDel',0);
    			if($data && is_array($data) && count(array_keys($data)) == count(array_values($data))){
    				foreach($data as $key => $val){
    					$object = $object->where($this->convert($key),$val);
    				}
    			}
    			$object = $object->get();
    			// var_dump($object);
    			if($object){
    				if($this->layer > 1){
    					$object = $this->child($object);
    				}
    				$options = [];
    				foreach($object as $item){
    					if(property_exists($item,'title')){
    						$label = $item->title;
    					}elseif(property_exists($item,$this->table.'_name')){
    						$key = $this->table.'_name';
    						$label = $item->$key;
    					}elseif(property_exists($item,$this->table.'_title')){
    						$key = $this->table.'_title';
    						$label = $item->$key;
    					}elseif(property_exists($item,$this->table.'name')){
    						$key = $this->table.'name';
    						$label = $item->$key;
    					}else{
    						$label = '';
    					}
    					if($label){
    						$key = $this->primaryKey;
    						$value = $item->$key;
							$option = ['type'=>'option','label'=>$label,'value'=>$value];
    						if($this->layer > 1){
    							$children = [];
    							if(property_exists($item,'children') && is_array($item->children) && count($item->children)){
    								foreach($item->children as $child){
    									if(property_exists($child,'title')){
    										$label = $child->title;
    									}elseif(property_exists($child,$this->table.'_name')){
    										$key = $this->table.'_name';
    										$label = $child->$key;
    									}elseif(property_exists($child,$this->table.'_title')){
    										$key = $this->table.'_title';
    										$label = $child->$key;
    									}elseif(property_exists($child,$this->table.'name')){
    										$key = $this->table.'name';
    										$label = $child->$key;
    									}else{
    										$label = '';
    									}
    									if($label){
    										$value = (int)$child->$key;
    										array_push($children,['type'=>'option','label'=>$label,'value'=>$value]);
    									}
    								}
    							}
    							if(count($children)){
    								$option['children'] = $children;
    							}
    						}
							array_push($options,$option);
    					}
    				}

    				return $options;
    			}

    			return [];
    		}catch(\Exception $e){
    			return [];
    		}
    	}

    	/**
    	 * 获取查询表单
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getQueryList(Request $request): array
    	{
    		if(!$this->field || !count($this->field)){
    			return [];
    		}
    		$form = [];
    		if($this->fieldExists('title') || $this->fieldExists($this->table.'_name') || $this->fieldExists($this->table.'name') || $this->fieldExists($this->table.'_title') || $this->fieldExists($this->table.'_code')){
    			array_push($form,['type'=>'input','label'=>'关键词','prop'=>'keyword']);
    		}

    		return $form;
    	}

    	/**
    	 * 获取表单
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getActionList(Request $request,$id = 0): array
    	{
    		try{
    			$action_value = $request->input('action','');
    			if($action_value == 'export'){
    				$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
    				if(!$res){
    					return [];
    				}

    				$field = [];
    				foreach($res as $item){
    					array_push($field,['type'=>'option','label'=>$item->Comment,'value'=>$item->Field]);
    				}

    				$action = [
    					['type'=>'radio-group','label'=>'导出数据类型','prop'=>'cate_id','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'原始数据','value'=>1],
    						['type'=>'radio','label'=>'关联数据','value'=>2],
    					]],
    					['type'=>'radio-group','label'=>'导出数据字段','prop'=>'is_all','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'全部字段','value'=>1],
    						['type'=>'radio','label'=>'部分字段','value'=>2],
    					]],
    					['type'=>'checkbox-group','label'=>'选择字段','prop'=>'field','value'=>[],'hidden'=>true,'children'=>$field],
    					['type'=>'radio-group','label'=>'导出数量','prop'=>'count','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'全部数据','value'=>1],
    						['type'=>'radio','label'=>'分段数据','value'=>2],
    					]],
    					['type'=>'form-group','label'=>'数据从','prop'=>'part','value'=>'','hidden'=>true,'delimiter'=>'-','children'=>[
    						['type'=>'input','label'=>'开始条数','prop'=>'start','value'=>1,'attrs'=>['style'=>['width'=>'150px']],'slot'=>['prefix'=>['value'=>'第'],'suffix'=>['value'=>'条']]],
    						['type'=>'input','label'=>'结束条数','prop'=>'end','value'=>10,'attrs'=>['style'=>['width'=>'150px']],'slot'=>['prefix'=>['value'=>'第'],'suffix'=>['value'=>'条']]],
    					]],
    				];

    				return $action;
    			}


	    		$fields = $this->fields ? $this->fields : $this->getList($request,'fields');
	    		if(!$fields || !count($fields)){
	    			return [];
	    		}

	    		$action = [];
	    		if($id){
	    			$data = $this->getList($request,$id);
	    		}
	    		$sort = $this->getMaxSort(0);
	    		if($this->layer > 1){
		    		$level = $request->input('level',1);
		    		if(empty($level) || !is_numeric($level) || $level < 1){
		    			$level = 1;
		    		}
		    		$levelData = [];
		    		for($i=1;$i<=$this->layer;$i++){
		    			array_push($levelData,['type'=>'option','label'=>$i.'级','value'=>(int)$i]);
		    		}
		    		$pid = $request->input('pid',1);
		    		if(empty($pid) || !is_numeric($pid) || $pid < 0){
		    			$pid = 0;
		    		}
		    		$pidData = [];
		    		$res_pid = $this->getList($request,'parent');
		    		if($res_pid){
		    			foreach($res_pid as $item){
		    				array_push($pidData,['type'=>'option','label'=>$item->title,'value'=>(int)$item->id]);
		    			}
		    		}
	    			$sort = $this->getMaxSort($pid);
		    	}

	    		$filter_field = $this->filter_field;
	    		array_push($filter_field,'CreateTime');
	    		// var_dump($fields);
	    		foreach($fields as $field){
	    			if(strtolower($field['map_name']) != 'id' && !in_array($field['field_name'],$filter_field)){
		    			$type = 'input';
		    			$prop = $field['map_name'];
		    			$label = $field['comment'] ? $field['comment'] : $prop;
		    			$value = $id ? (property_exists($data,$prop) ? $data->$prop : '') : $field['default_value'];
		    			$hidden = false;
		    			$attrs = [];
		    			$rules = [];
		    			$children = [];
		    			$uploadAttrs = [];
		    			$editorOptions = [];
		    			$slot = [];

		    			if(strtolower($field['field_type']) == 'varchar'){
		    				$type = 'input';
		    			}
		    			if(strtolower($field['field_type']) == 'tinyint' && $field['length'] == 1 && substr(strtolower($field['map_name']),0,2) == 'is'){
		    				$type = 'switch';
		    			}

		    			switch($field['map_name']){
		    				case 'title':
		    				case $this->table.'_title':
		    				case $this->table.'_name':
		    					$type = 'input';
		    					$rules = ['required'=>true,'message'=>$label.'不能为空'];
		    					break;
		    				case 'level':
		    					if($this->layer > 1){
		    						$type = 'select';
		    						$children = $levelData;
		    						$value = $id ? $data->level : $level;
		    						$hidden = true;
		    					}
		    					break;
		    				case 'pid':
		    					if($this->layer > 1){
		    						$type = 'select';
		    						$children = $pidData;
		    						$value = $id ? $data->pid : $pid;
		    						$hidden = true;
		    					}
		    					break;
		    				case 'sort':
		    					$type = 'input';
		    					$value = $id ? $data->sort : $sort;
		    					break;
		    				case 'pic':
		    					$type = 'upload';
		    					$uploadAttrs = ['type'=>'pic','limit'=>1,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				case 'img':
		    					$type = 'upload';
		    					$uploadAttrs = ['type'=>'img','limit'=>1,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				case 'icon':
		    					$type = 'upload';
		    					$uploadAttrs = ['type'=>'icon','limit'=>1,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				case 'face':
		    					$type = 'upload';
		    					$uploadAttrs = ['type'=>'avatar','limit'=>1,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				case 'picture':
		    					$type = 'upload';
		    					$uploadAttrs = ['type'=>'card','limit'=>5,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				case 'desc':
		    				case 'description':
		    				case 'remark':
		    					$type = 'input';
		    					$attrs = ['type'=>'textarea'];
		    					break;
		    				case 'content':
		    					$type = 'editor';
		    					$editorOptions = ['type'=>'editor','limit'=>5,'size'=>'default','accept'=>'','action'=>$this->host['api'].'upload'];
		    					break;
		    				default:

		    			}

		    			if(substr($field['map_name'],strlen($field['map_name'])-3) == '_id'){
		    				$_table = substr($field['map_name'],0,strlen($field['map_name'])-3);
		    				$_table = $this->convert($_table);
		    				// var_dump('_table: '.$_table);
		    				$_class_name = "\\dackou\\model\\{$_table}\\{$_table}Model";
		    				if(!\class_exists($_class_name)){
		    					$_class_name = "\\app\\model\\{$_table}\\{$_table}Model";
		    				}
		    				if(\class_exists($_class_name)){
		    					$type = 'select';
		    					$service = new $_class_name();
		    					$children = $service->getList($request,'option');
		    				}
		    			}

		    			if($field['prefix']){
		    				$slot['prefix'] = ['value'=>$field['prefix']];
		    			}

		    			if($field['suffix']){
		    				$slot['suffix'] = ['value'=>$field['suffix']];
		    			}

		    			if($field['width']){
		    				$attrs['style'] = ['width'=>$field['width'].'px'];
		    			}


		    			if($field['is_must']){
		    				$rules['required'] = true;
		    				$rules['message'] = $type == 'select' ? '请选择'.$field['comment'] : $field['comment'].'不能为空';
		    			}

		    			$option = ['type'=>$type,'label'=>$label,'prop'=>$prop,'value'=>$value];
		    			if($hidden){
		    				$option['hidden'] = true;
		    			}
		    			if($attrs){
		    				$option['attrs'] = $attrs;
		    			}
		    			if($rules){
		    				$option['rules'] = $rules;
		    			}
		    			if($children){
		    				$option['children'] = $children;
		    			}
		    			if($slot){
		    				$option['slot'] = $slot;
		    			}
		    			if($uploadAttrs){
		    				$option['uploadAttrs'] = $uploadAttrs;
		    			}

		    			array_push($action,$option);
		    		}
	    		}

	    		return $action;
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	protected function getHandleList(Request $request,$flag = false): array
    	{
    		// $field = [
    		// 	'IsShow' 	 => 'is_show',
    		// 	'IsRefresh'  => 'is_refresh',
    		// 	'IsAdd' 	 => 'is_add',
    		// 	'IsModify' 	 => 'is_modify',
    		// 	'IsSearch' 	 => 'is_search',
    		// 	'IsSave' 	 => 'is_save',
    		// 	'IsDel' 	 => 'is_del',
    		// 	'IsImport' 	 => 'is_import',
    		// 	'IsExport' 	 => 'is_export',
    		// 	'IsPrint' 	 => 'is_print',
    		// 	'IsChecked'  => 'is_checked',
    		// 	'IsApproved' => 'is_approved',
    		// 	'IsReject' 	 => 'is_reject',
    		// 	'IsInit' 	 => 'is_init',
    		// 	'IsClear' 	 => 'is_clear',
    		// 	'IsBack' 	 => 'is_back',
    		// ];
    		$field = [
    			['id'=>1,'title'=>'显示','key'=>'IsShow','type'=>'','plan'=>true],
    			['id'=>2,'title'=>'刷新','key'=>'IsRefresh','type'=>'','plan'=>true],
    			['id'=>3,'title'=>'新增','key'=>'IsAdd','type'=>'','plan'=>true],
    			['id'=>4,'title'=>'修改','key'=>'IsModify','type'=>'','plan'=>true],
    			['id'=>5,'title'=>'查询','key'=>'IsSearch','type'=>'','plan'=>true],
    			['id'=>6,'title'=>'保存','key'=>'IsSave','type'=>'','plan'=>true],
    			['id'=>7,'title'=>'删除','key'=>'IsDel','type'=>'','plan'=>true],
    			['id'=>8,'title'=>'导入','key'=>'IsImport','type'=>'','plan'=>true],
    			['id'=>9,'title'=>'导出','key'=>'IsExport','type'=>'','plan'=>true],
    			['id'=>10,'title'=>'打印','key'=>'IsPrint','type'=>'','plan'=>true],
    			['id'=>11,'title'=>'审核','key'=>'IsChecked','type'=>'','plan'=>true],
    			['id'=>12,'title'=>'核准','key'=>'IsApproved','type'=>'','plan'=>true],
    			['id'=>13,'title'=>'拒绝','key'=>'IsReject','type'=>'','plan'=>true],
    			['id'=>14,'title'=>'初始化','key'=>'IsInit','type'=>'','plan'=>true],
    			['id'=>15,'title'=>'清空','key'=>'IsClear','type'=>'','plan'=>true],
    			['id'=>16,'title'=>'返回','key'=>'IsBack','type'=>'','plan'=>true],
    		];
    		if($flag){
    			$temp = [];
    			foreach($field as $item){
    				array_push($temp,"grant.".$item['key']." as ".$this->convert($item['key'],false));
    			}

    			return $temp;
    		}

    		return $field;
    	}

    	protected function getpermissionList(Request $request,$field = [],$role_name = 'rid'){
    		try{
    			$role_id = $this->getTokenData($request,$role_name);
    			if(!$role_id){
    				return false;
    			}
    			$router = $request->input('router',$this->table);
    			if(!$field){
    				$field = ['*'];
    			}else{
    				if(!is_array($field)){
    					$field = \explode(',',$field);
    				}
    			}
    			$where = [
    				['RoleID','=',$role_id],
    				['Router','=',$router]
    			];

    			$object = Db::table('grant')
    						->join('menu','MenuID','=','menu.ID')
    						->select(...$field)
    						->where($where)
    						->first();
    			return $object;
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	/**
    	 * 获取操作权限列表
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getGrantsList(Request $request): array
    	{
    		// return ['is_refresh','is_add','is_modify','is_back','is_export','is_import','is_init','is_clear'];
    		// return [
    		// 	['key'=>'is_refresh','title'=>'刷新'],
    		// 	['key'=>'is_add','title'=>'新增'],
    		// 	['key'=>'is_modify','title'=>'修改'],
    		// 	['key'=>'is_import','title'=>'导入'],
    		// 	['key'=>'is_export','title'=>'导出'],
    		// 	['key'=>'is_init','title'=>'初始化'],
    		// 	['key'=>'is_clear','title'=>'清空'],
    		// 	['key'=>'is_back','title'=>'返回'],
    		// ];

            // var_dump('uri: '.$request->uri());
            try{
	    		$router = $request->input('router',$this->table);
	            $router = str_replace("_","/",$router);
	            $sign = $this->getTokenData($request,'sign');
	            // var_dump('router: '.$router);
	            // var_dump('sign: '.$sign);
	            if(is_array($sign)){
	            	return $sign;
	            }
	            if(!$router || !$sign){
	            	return [];
	            }
	            $field = $this->getList($request,'handle',true);

                $object = Db::table('grant')
                            ->join('role','RoleID','=','role.ID')
                            ->join('menu','MenuID','=','menu.ID')
                            ->select(...$field)
                            ->where([['Sign','=',$sign],['Router','=',$router]])
                            ->first();
                // var_dump($object);
                $result = [];
                if($object){
                    foreach($object as $key => $val){
                        if($val === 1){
                            array_push($result,$key);
                        }
                    }
                }
                
                return $result;
            }catch(\Exception $e){
            	// var_dump('error: '.$e->getMessage());
                return [];
            }
    	}

    	/**
    	 * 获取显示表头
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getMapList(Request $request): array
    	{
    		if(!$this->fields || !count($this->fields)){
    			$this->fields = $this->getList($request,'fields');
    		}
    		if(!$this->fields){
    			return [];
    		}
    		$map = [];
    		foreach($this->fields as $field){
				if($field['is_show'] && !in_array($field['field_name'],$this->filter_field)){
					$children = ['type'=>$field['show_type'],'label'=>$field['comment'],'prop'=>$field['map_name']];
					if(in_array($field['map_name'],['pic','img','logo','picture'])){
						$children['type'] = 'img';
					}
					if($field['align']){
						$children['align'] = $field['align'];
					}
					if($field['width']){
						if($this->layer > 1 && $field['map_name'] == 'title'){
							$children['width'] = 180;
						}elseif($field['map_name'] == 'create_time'){
							$children['width'] = 180;
						}else{
							$children['width'] = $field['width'];
						}
					}
					if($field['prefix']){
						$children['prefix'] = $field['prefix'];
					}
					if($field['suffix']){
						$children['suffix'] = $field['suffix'];
					}
					// if($field['callback']){
					// 	$children['callback'] = $field['callback'];
					// }
					array_push($map,$children);
				}
			}
    		return $map;
    	}

    	protected function tree(mixed $data): mixed
    	{
    		if(!$data || $this->layer == 1){
        		return [];
        	}

        	$arr = [];
        	$pid = $data[0]->pid ?? 0;
        	foreach($data as $item){
        		if($item->pid == $pid){
        			array_push($arr,$item);

        			if($item->number){
        				foreach($data as $values){
        					if($values->pid == $item->id){
        						array_push($arr,$values);

        						if($values->number){
        							foreach($data as $value){
        								if($value->pid == $values->id){
        									array_push($arr,$value);

        									if($value->number){
        										foreach($data as $val){
        											if($val->pid == $value->id){
        												array_push($arr,$val);

        												if($val->number){
        													foreach($data as $v){
        														if($v->pid == $val->id){
        															array_push($arr,$v);
        														}
        													}
        												}
        											}
        										}
        									}
        								}
        							}
        						}
        					}
        				}
        			}
        		}
        	}

        	return $arr;
    	}

    	protected function child(mixed $data): mixed
    	{
    		if(!$data || $this->layer == 1){
        		return [];
        	}

        	$arr = [];
        	$pid = $data[0]->pid ?? 0;

        	for($i=0;$i<count($data);$i++){
        		if(!property_exists($data[$i],'label') && property_exists($data[$i],'title')){
        			$data[$i]->label = $data[$i]->title;
        		}
        		if(!property_exists($data[$i],'value') && property_exists($data[$i],'id')){
        			$data[$i]->value = $data[$i]->id;
        		}
        	}

        	foreach($data as $item){
        		if($item->pid == $pid){

        			if($item->number){
        				$child1 = [];
        				foreach($data as $values){
        					if($values->pid == $item->id){
        						if($values->number){
        							$child2 = [];
        							foreach($data as $value){
        								if($value->pid == $values->id){
        									if($value->number){
        										$child3 = [];
        										foreach($data as $val){
        											if($val->pid == $value->id){
        												if($val->number){
        													$child4 = [];
        													foreach($data as $v){
        														if($v->pid == $val->id){
        															array_push($child4,$v);
        														}
        													}
        													if($child4){
        														$val->children = $child4;
        													}
        												}

        												array_push($child3,$val);
        											}
        										}
        										if($child3){
        											$value->children = $child3;
        										}
        									}

        									array_push($child2,$value);
        								}
        							}
        							if($child2){
        								$values->children = $child2;
        							}

        						}
        						array_push($child1,$values);
        					}
        				}
        				if($child1){
        					$item->children = $child1;
        				}
        			}

        			array_push($arr,$item);
        		}
        	}

        	return $arr;
    	}

    	// 获取配置参数
    	protected function getConfig($key,$flag = false){
    		if(isset($this->config[$key])){
    			if(!$flag){
	    			return $this->config[$key];
	    		}
	    		$val = $this->config[$key];
	    		if($val || $val == '1' || $val == 1){
	    			return true;
	    		}
	    		return false;
    		}
    		return false;
    	}

        /**
         * 创建随机字符串
         * @param  integer $num [description]
         * @return [type]       [description]
         */
        protected function createCode(int $len = 8,int $type = 1,bool $flag = false): string
        {
            switch($type){
                case '':
                case 1:
                    $str = '123456789';
                    break;
                case 2:
                    $str = $flag ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz';
                    break;
                case 3:
                    $str = $flag ? 'abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz123456789';
                    break;
                default:
                    $str = '123456789';
            }

            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);

            $num = '';
            for($i=0;$i<$len;$i++){
                if($i==0){
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }else{
                    if($type != 2){
                        $str .= '0';
                    }
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }
            }
            
            return $num;
        }

        // 创建唯一随机字符串
        protected function createUniqueCode(string $field,int $len = 8,int $type = 1,bool $flag = false): string
        {
        	try{
	        	$code = $this->createCode($len,$type,$flag);
	        	if($this->checkExists([$this->convert($field) => $code],0)){
	        		return $this->createUniqueCode($field,$len,$type,$flag);
	        	}

	        	return $code;
	        }catch(\Exception $e){
	        	return $this->getExceptionError($e);
	        }
        }

    	protected function getMaxSort(int $pid = 0,$flag = true): int
    	{
    		try{
    			$object = $flag ? Db::table($this->table)->where('PID',$pid)->max('Sort') : Db::table($this->table)->max('Sort');
    			return $object ? ($object + 1) : 1;
    		}catch(\Exception $e){
    			return 1;
    		}
    	}

        protected function getMaxidList(Request $request)
        {
            try{
            	if($this->layer < 2 || $this->digit < 2){
            		return [];
            	}

            	$field = ['ID as id'];
            	$object = Db::table($this->table)
            				->select(...$field)
            				->where('IsDel',0)
            				->get();
            	$option = [];
            	if($object){
            		foreach($object as $item){
            			$option[$item->id] = $item->id * (10 ** $this->digit);
            		}
            		if($option){
            			$sql = "SELECT PID as pid,MAX(ID) as max_id FROM `".$this->tab."` WHERE PID > 0 GROUP BY ID";
            			$result = Db::select($sql);
            			if($result){
            				foreach($result as $res){
            					if(isset($option[$res->pid])){
            						$option[$res->pid] = $res->max_id;
            					}
            				}
            			}
            		}
            	}

            	return $option;
            }catch(\Exception $e){
            	// var_dump('get maxid error: ',$this->getExceptionError($e));
                return [];
            }
        }

    	/**
    	 * 获取层级最大ID
    	 * @param  integer $pid   [description]
    	 * @param  integer $digit [description]
    	 * @return [type]         [description]
    	 */
    	protected function getMaxid($pid = 0,$digit = 2){
    		try{
    			$digit = $digit ? $digit : $this->digit;
    			if(!$pid){
		            $first_id = (int)('1'.str_repeat('0', $digit-1));
		        }else{
		            $first_id = ($pid * ((int)('1'.str_repeat('0', $digit)))) + 1;
		        }
    			$object = Db::table($this->table)
    							->select($this->convert($this->primaryKey).' as id')
    							->where('PID',$pid)
    							->orderBy($this->convert($this->primaryKey),'desc')
    							->limit(1)
    							->first();
    			return ($object&&$object->id) ? $object->id+1 : $first_id;
    		}catch(\Exception $e){
    			return 0;
    		}
    	}

    	protected function getFieldsList(Request $request){
    		try{
    			if($this->is_schema){
    				$result = $this->getList($request,'schema');
    				if($result && count($result)){
    					return $result;
    				}
    			}
    			$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
    			// var_dump('get fields res:',$res);
    			if(!$res){
    				return false;
    			}
    			$fields = [];
    			for($i=0;$i<count($res);$i++){
    				$type = $res[$i]->Type;
    				$length = 0;
    				$is_must = 0;
    				if(\strpos($type,'(') !== false){
    					$temp = \explode('(',$res[$i]->Type);
    					if(isset($temp[0])){
    						$type = $temp[0];
    					}
    					if(isset($temp[1])){
    						$length = (int)\substr($temp[1],0,\strlen($temp[1])-1);
    					}
    				}
    				if(in_array($res[$i]->Field,['Title',$this->convert($this->table).'Title',$this->convert($this->table).'Name'])){
    					 $is_must = 1;
    				}
    				array_push($fields,[
    					'id' 			 => $i+1,
    					'table_id' 		 => 0,
    					'table' 		 => $this->table,
    					'comment' 		 => $res[$i]->Comment,
    					'field_name' 	 => $res[$i]->Field,
    					'map_name' 		 => $this->convert($res[$i]->Field,false),
    					'field_type' 	 => $type,
    					'length' 		 => $length,
    					'is_primary_key' => $res[$i]->Key == 'PRI' ? 1 : 0,
    					'is_key' 		 => $res[$i]->Key == 'MUL' ? 1 : 0,
    					'is_unique' 	 => 0,
    					'is_must' 		 => $is_must,
    					'is_show' 		 => 1,
    					'is_add' 		 => 1,
    					'is_mod' 		 => 1,
    					'is_search' 	 => 0,
    					'show_type' 	 => 'varchar',
    					'form_type' 	 => 'input',
    					'width' 		 => 0,
    					'align' 		 => $this->align,
    					'default_value'  => $res[$i]->Default,
    					'rules' 		 => [],
    					'prefix' 		 => '',
    					'suffix' 		 => '',
    					'prompt' 		 => '',
    					'callback_field' => '',
    					'callback_key'   => '',
    					'callback_title' => '',
    					'after' 		 => '',
    					'status' 		 => 1,
    				]);
    			}
    			return $fields;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 获取字段
    	 * @param  Request $request [description]
    	 * @param  string  $type    [description]
    	 * @return [type]           [description]
    	 */
    	protected function getFieldList(Request $request,mixed $flag = false){
    		try{
    			$exclude = ['CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP'];
    			$fields = $this->fields ? $this->fields : $this->getList($request,'fields');
    			// var_dump($fields);
    			if($fields){
	    			$field = [];
	    			if($flag === false){
	    				foreach($fields as $item){
	    					if(!in_array($item['field_name'],$exclude)){
								array_push($field,$item['field_name']." as " . $item['map_name']);
							}
						}
	    				$this->field = $field;
	    			}elseif($flag === true){
	    				foreach($fields as $item){
	    					if(!in_array($item['field_name'],$exclude)){
								array_push($field,"" . $this->table . ".".$item['field_name']. " as " . $item['map_name']);
							}
						}
	    				$this->field = $field;
	    			}elseif($flag == 'field'){
	    				foreach($fields as $item){
	    					if(!in_array($item['field_name'],$exclude)){
								$field[$item['field_name']] = $item['map_name'];
							}
						}
	    				$this->field = $field;
	    			}elseif($flag == 'key'){
	    				$keys = [];
	    				$vals = [];
	    				foreach($fields as $item){
	    					if(!in_array($item['field_name'],$exclude)){
	    						array_push($keys,$item['field_name']);
	    						array_push($vals,$item['map_name']);
	    					}
						}
    					array_push($field,$keys,$vals);
	    			}else{
	    				$field = $fields;
	    			}
	    			// var_dump($field);
	    			return $field;
	    		}else{
	    			return false;
	    		}
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	// 获取表注释title
    	protected function getTableTitle(): string
    	{
    		try{
    			if($this->title && !empty($this->title)){
    				return $this->title;
    			}
	    		$sql = "SELECT TABLE_COMMENT as comment FROM INFORMATION_SCHEMA.Tables where table_schema = '".$this->dbname."' AND table_name = '".$this->tab."'";
	    		$result = Db::select($sql);
	    		// var_dump($result);
	    		return ($result && isset($result[0])) ? $result[0]->comment : '';
	    	}catch(\Exception $e){
	    		return '';
	    	}
    	}

    	// 获取查询条件
    	protected function getWhere(Request $request): array
    	{
    		$where = [];
    		if($this->fieldExists('is_del')){
    			array_push($where,[$this->table.'.IsDel','=',0]);
    		}

    		return $where;
    	}

    	/**
         * 获取分页数
         * @param  Request $request [description]
         * @param  string  $key     [description]
         * @return [type]           [description]
         */
        protected function getLimit(Request $request,int $psize = 10,string $pageKey = 'page',string $key = 'pagesize'): array
        {
            $page = $request->input($pageKey);
            if(empty($page) || !is_numeric($page) || !$page){
                $page = 1;
            }
            
            $pagesize = $request->input($key);
            if(empty($pagesize) || !is_numeric($pagesize) || !$pagesize){
                $pagesize = $psize;
            }
            $limit = [($page - 1) * $pagesize,$pagesize];
            return $limit;
        }

    	// 是否存在某个字段
    	protected function fieldExists(string $field = '',array $data = []): bool
    	{
    		if(!$data || !is_array($data) || !count($data)){
    			$data = ($this->fields && count($this->fields)) ? $this->fields : $this->getList(request(),'fields');
    		}
    		// var_dump($data);
			if(!$data || !count($data)){
				return false;
			}
    		
    		$flag = false;
    		foreach($data as $item){
    			$field = $this->convert($field);
    			if($item['field_name'] == $field){
    				$flag = true;
    				break;
    			}
    		}

    		return $flag;
    	}

    	protected function setIncrement($field,$id = 0,$val = 1){
    		try{
    			if(!$id){
    				return false;
    			}
    			if(is_array($id)){
    				$where = "";
    				foreach($id as $k => $v){
    					$where .= "`{$k}` = {$v} AND ";
    				}
    				$where = rtrim($where," AND ");
    			}else{
    				$where = "`".$this->convert($this->primaryKey)."` = ".$id;
    			}
    			$sql = "UPDATE `".$this->tab."` SET `{$field}` = `{$field}` + ? WHERE ".$where;
    			// var_dump('increment sql: '.$sql);
    			$result = Db::update($sql,[$val]);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	protected function setDecrement($field,$id = 0,$val = 1){
    		try{
    			if(!$id){
    				return false;
    			}
    			if(is_array($id)){
    				$where = "";
    				foreach($id as $k => $v){
    					$where .= "`{$k}` = {$v} AND ";
    				}
    				$where = rtrim($where," AND ");
    			}else{
    				$where = "`".$this->convert($this->primaryKey)."` = ".$id;
    			}
    			$sql = "UPDATE `".$this->tab."` SET `{$field}` = `{$field}` - ? WHERE ".$where;
    			// var_dump('decrement sql: '.$sql);
    			$result = Db::update($sql,[$val]);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	// 审核
    	public function setCheck(Request $request){
    		try{
    			$id = $request->post('id',0);
    			$value = $request->post('value');
    			$type = $request->post('type','');
    			$content = trim($request->post('content',''));

    			if(!$id || !is_numeric($id) || $id <= 0){
    				return 100007;
    			}
    			$obj = $this->getList($request,$id);
    			if(!$obj || !is_object($obj)){
    				return 100007;
    			}

    			if($obj->status > 0){
    				return '该记录已通过'.($obj->status>1?'核准':'审核').'，无需重复操作!';
    			}

    			if(!in_array($value,[0,1])){
    				return '无效的审核值';
    			}

    			if(!in_array($type,['check','approve'])){
    				return '无效的审核参数';
    			}

    			if(!$this->fieldExists('status')){
    				return '无效的审核字段';
    			}
    			$is_content = $this->fieldExists('content');

    			switch($obj->status){
    				case -2:
    					$value = $value ? 1 : -2;
    					break;
    				case -1:
    					$value = $value ? 1 : -1;
    					break;
    				case 0:
    					$value = $value ? 1 : -1;
    					break;
    				default:
    			}

    			$data = [
    				'Status'  => $value,
    			];
    			if($is_content){
    				$data['Content'] = $content;
    			}

    			$result = $this->updateData($request,$data,$id);
    			$rid = Db::getPdo()->lastInsertId();
    			if($result !== false){
    				if(\method_exists($this,'setChecked')){
    					return $this->setChecked($request,$id);
    				}
    				return false;
    			}
    			return false;

    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

		// 检查字段值是否已存在
		public function checkExists($data,$id = 0){
			try{
				$where = [];
				foreach($data as $key => $val){
					array_push($where,[$this->convert($key),'=',$val]);
				}
				if($id){
					array_push($where,[$this->convert($this->primaryKey),'<>',$id]);
				}
				$count = Db::table($this->table)
							->where($where)
							->count();
				return $count ? true : false;
			}catch(\Exception $e){
				return true;
			}
		}

    	// 获取token数据
    	protected function getTokenData(Request $request,string $key = ''): mixed
    	{
    		if(\class_exists('\dackou\Token')){
    			if($key == 'rid' || $key == 'role_id'){
    				$sign = \dackou\Token::getTokenData($request,'sign');
    				if(is_string($sign) && $sign){
    					$object = Db::table('role')
    								->select('ID as id')
    								->where('Sign',$sign)
    								->first();
    					if($object){
    						return $object->id;
    					}
    					return '';
    				}
    				return '';
    			}
	    		return \dackou\Token::getTokenData($request,$key);
	    	}
	    	return '';
    	}

    	// 大小写转
		protected function convert(string $str,bool $flag = true): string
		{
			$map = [
				'AccountID' => 'uid',
				'UserName'  => 'username',
				'NickName'  => 'nickname',
				'Authentication'  => 'password',
				'OrderCode'  => 'order_code',
				'Description'  => 'desc',
			];

			foreach($map as $key => $val){
				if($flag && $str == $val){
					return $key;
				}
				if(!$flag && $str == $key){
					return $val;
				}
			}


			$str = trim($str);
			if(!$str){
				return '';
			}

			if($flag){
				if(substr($str,-2) == 'id'){
					if($str == 'uuid' || $str == 'Uuid' || $str == 'UuID'){
						$str = 'Uuid';
					}else{
						$str = substr($str,0,strlen($str)-2).'ID';
					}
				}
				if(substr($str,-2) == 'ip'){
					$str = substr($str,0,strlen($str)-2).'IP';
				}
				if(strpos($str,'_') === false){
					return ucfirst($str);
				}
				$temp = '';
				$arr = explode('_',$str);
				foreach($arr as $val){
					$temp .= ucfirst($val);
				}
				return $temp;
			}

			$regx = '_';
			return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', $regx, $str));
		}

		// 将数组或对象键名转小写
		protected function getConvertData(mixed $payload): mixed
		{
			if(is_array($payload)){
				$data = [];
				foreach($payload as $key => $val){
					$data[$this->convert($key,false)] = $val;
				}

				return $data;
			}
			if(is_object($payload)){
				foreach($payload as $key => $val){
					$k = $this->convert($key,false);
					if($k != $key){
						$payload->$k = $val;
						unset($payload->$key);
					}
				}
				return $payload;
			}

			return $payload;
		}

		/**
		 * 核验验证码
		 * @param  Request $request [description]
		 * @param  [type]  $code    [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function checkValidateCode(Request $request,$code,$type = 'numcode'){
			try{
				$key = $this->prefix.$type;
				$captcha = $request->session()->get($key);
				// var_dump('check session '.$key.': '.$captcha);
				if($captcha){
					// var_dump('code: '.strtolower($code).' captcha: '.strtolower($captcha).' token: '.$token);
					if(!$code || !$captcha || empty($code) || empty($captcha) || strtolower($code) !== strtolower($captcha)){
						return false;
					}

					$request->session()->forget($key);
					return true;
				}
				return false;
			}catch(\Exception $e){
				// var_dump('error: '.$e->getMessage());
				return false;
			}
		}

        public function makePassword($password){
        	return \password_hash($password, PASSWORD_BCRYPT,["cost" => 12]);
        }

        public function checkPassword($password,$checkpwd){
        	return \password_verify($password,$checkpwd);
        }

        public function getIPAddress(Request $request,$flag = true){
        	$result = \dackou\Ip::getIp($request);
        	if(!$result || !is_array($result) || isset($result['code']) || !isset($result['country'])){
        		return '';
        	}
        	return $flag ? $result['country'] : $result;
        }

        protected function getPropertyValue(mixed $value = 0,string $key = ''): string
        {
        	if($key && property_exists($this,$key)){
        		if(count($this->$key) === count($this->$key, COUNT_RECURSIVE)){
        			return isset($this->$key[$value]) ? $this->$key[$value] : $value;
        		}else{
        			$val = '';
        			foreach($this->$key as $item){
        				if($item['id'] === $value){
        					if(isset($item['color'])){
        						$val = '<span style="color:'.$item['color'].';">'.$item['title'].'</span>';
        					}else{
        						$val = $item['title'];
        					}
        				}
        			}

        			return $val;
        		}
        	}

        	return $value;
        }

        /**
         * 设置session
         * @param Request $request [description]
         * @param [type]  $data    [description]
         * @param [type]  $value   [description]
         */
        protected function setSession(Request $request,$data,$value = null){
        	if(!is_null($value) && is_string($data) && $data){
        		$request->session()->set($this->prefix.$data,$value);
        	}
        	if((is_array($data) || is_object($data)) && $data){
        		foreach($data as $key => $val){
        			$request->session()->set($this->prefix.$key,$val);
        		}
        	}
        }

        /**
         * 获取session
         * @param  Request $request [description]
         * @param  string  $key     [description]
         * @return [type]           [description]
         */
        protected function getSession(Request $request,$key = ''){
        	if($key){
        		$key = $this->prefix.$key;
        		$session = $request->session();
        		if($session->exists($key)){
        			return $session->get($key);
        		}
        		return '';
        	}
        	return $request->session();
        }

        /**
         * 删除session
         * @param  Request $request [description]
         * @param  string  $key     [description]
         * @return [type]           [description]
         */
        protected function removeSession(Request $request,$key = ''){
        	if($key){
        		$key = $this->prefix.$key;
        		$request->session()->forget($key);
        	}else{
        		$request->session()->flush();
        	}
        }

        protected function getClassName($class){
        	if(\preg_match('/[A-Z]/',\lcfirst($class))){
				$temp = \lcfirst($class);
				$temp = \preg_split('/(?=[A-Z])/', $temp);
				$class = \ucfirst($class);
				$parent = \ucfirst($temp[0]);
				$_class_name = "\\app\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}else{
				$class = \ucfirst($class);
				$_class_name = "\\app\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}
        }

        protected function getBirth($idcard){
        	return substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
        }

        protected function getGender($idcard){
        	$num = substr($idcard,16,1);
            return $num % 2 === 0 ? 2 : 1;
        }

        /**
         * [getAge description]
         * @param  [type] $idcard [description]
         * @return [type]         [description]
         */
        protected function getAge($birth){
            // $birth = $this->getBirth($idcard);
            // var_dump($birth);
            if(trim($birth) == '' || !$birth){
                return 0;
            }
            list($birthYear, $birthMonth, $birthDay) = explode('-', $birth);
            list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-m-d'));
            $age = $currentYear - $birthYear - 1;
            
            if($currentMonth > $birthMonth || $currentMonth == $birthMonth && $currentDay >= $birthDay)

            $age++;

            return $age;
        }

    	/**
    	 * 获取编辑器组件options
    	 * @param  string $editor [description]
    	 * @return [type]         [description]
    	 */
    	protected function getEditorOptions($editor = 'wang'){
    		$editor = $editor ? $editor : $this->editor;
    		switch($editor){
    			case 'wang':
    				return ['type'=>'wang','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/'.$editor],'video'=>['server'=>$this->host['api'].'upload/video']];
    			case 'umo':
    				return ['type'=>'emo','is_ai'=>$this->is_ai,'action'=>$this->host['api'].'upload/'.$editor,'prefix'=>$this->prefix];
    			default:
    				return ['type'=>'wang','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/'.$editor],'video'=>['server'=>$this->host['api'].'upload/video']];
    		}
    	}

    	/**
    	 * 获取上传组件option
    	 * @param  string  $type  [description]
    	 * @param  integer $limit [description]
    	 * @return [type]         [description]
    	 */
    	protected function getUploadOptions($type = 'img',$limit = 1){
    		switch($type){
    			case 'img':
    			case 'pic':
    				return ['type'=>'img','limit'=>$limit,'size'=>'default','action'=>$this->host['api'].'upload'];
    			case 'card':
    				return ['type'=>'card','limit'=>$limit,'size'=>'default','action'=>$this->host['api'].'upload'];
    			default: 
    				return ['type'=>'img','limit'=>$limit,'size'=>'default','action'=>$this->host['api'].'upload'];
    		}
    	}

        protected function getImage($img,$path = ''){
        	if(empty($img) || !$img){
        		return $img;
        	}
            if(strtolower(substr($img,0,4)) != 'http'){
                $img = $this->host['img'] . !empty($path) ? $path . '/' . $img : $img;
            }
            
            return $img;
        }

        protected function formatPrice($price,$decimal = 2,$divisor = 100){
        	return $this->formatAmount($price,$decimal,$divisor);
        }

        protected function formatAmount($amount,$decimal = 2,$divisor = 100){
        	if(!is_numeric($amount)){
        		return $amount;
        	}
        	return \number_format(($amount / $divisor),$decimal);
        }

        protected function getDateTime($time = ''){
        	if(empty($time) || !$time || !is_numeric($time)){
        		return $time;
        	}
        	return \date('Y-m-d H:i:s',$time);
        }

		protected function getJsonData(mixed $arr = ''): string
		{
			if(is_array($arr)){
				return json_encode($arr);
			}
			return $arr;
		}

        protected function getFieldValue($val,$arr){
            return isset($arr[$val]) ? $arr[$val] : '未知';
        }

        protected function getNextValue($val,$data = []){
        	if(!$data){
        		return '';
        	}

        	$result = '';
        	foreach($data as $item){
        		if($item['value'] === $val){
        			$result = isset($item['next']) ? $item['next'] : '';
        			break;
        		}
        	}

        	return $result;
        }

        protected function getFieldOption($data,$type = 'checkbox'){
            if(!$data || !is_array($data) || !count($data)){
                return [];
            }
            if(isset($data[0]['label']) || isset($data[0]['title'])){
            	return $data;
            }

            $option = [];
            foreach($data as $key => $val){
            	if(is_array($val)){
            		foreach($val as $item){
            			$child = [
            				'type' 	=> 'option',
            				'label' => $item['title'] ?? $item['label'],
            				'value' => (int)$item['id'] ?? (int)$item['value'],
            			];
            			if(isset($item['key'])){
            				$child['key'] = $item['key'];
            			}
            			if(isset($item['desc'])){
            				$child['desc'] = $item['desc'];
            			}
            			if(isset($item['color'])){
            				$child['color'] = $item['color'];
            			}
            			if(isset($item['callback'])){
            				$child['callback'] = $item['callback'];
            			}
            			array_push($option,$child);
            		}
            	}else{
            		array_push($option,['type'=>'option','label'=>$val,'value'=>(int)$key]);
            	}
            }

            return $option;
        }

		protected function getDecodeData(mixed $str = '',$flag = true): array
		{
			if(empty($str) || !$str ){
				return $str;
			}
			if(\is_array($str) || !is_string($str)){
				return $str;
			}
			if(\is_array($flag)){
				return $flag;
			}
			try{
				$result = \json_decode($str,$flag);
				if(\json_last_error() === JSON_ERROR_NONE){
					return $result;
				}

				return [$str];
			}catch(\Exception $e){
				var_dump($e->getMessage());
				return [];
			}
		}

		protected function getOptionsData(mixed $str = ''): array
		{	
			if(empty($str) || !$str){
				return [];
			}
			try{
				$result = \json_decode($str,true);
				if(\json_last_error() === JSON_ERROR_NONE){
					$temp = [];
					foreach($result as $key => $val){
						// array_push($temp,['type'=>'option','label'=>$key,'value' => $val]);
						$temp[$key] = $val;
					}

					return $temp;
				}

				return [];
			}catch(\Exception $e){
				return [];
			}
		}

        /**
         * 返回异常结果数据
         * @param  [type] $e [description]
         * @return [type]    [description]
         */
        protected function getExceptionError(\Exception $e): array
        {
            $data = [
                'code'  => $e->getCode() ? $e->getCode() : 1,
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'method'=> \request()->action,
                'msg'   => $e->getMessage()
            ];
            // var_dump('exception error:',$data);
            return $data;
        }

	}
?>
