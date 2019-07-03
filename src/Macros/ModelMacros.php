<?php

namespace Singsys\LQ\Macros;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ModelMacros {

	protected $builder;
	protected $request;

	public function __construct (Builder $builder, Request $request) {
		$this->builder = $builder;
		$this->request = $request;
	}

    protected function dbd($array) {
        $new_q = '';
        $query = $array['query'];
        $bindings = $array['bindings'];
        $limit = strlen($query);
        $occurences = 0;
        $temp_string = '';
        for ($i = 0; $i < $limit; $i++) {
            if($query[$i] == '?') {
                $temp_string .= "'" . $bindings[$occurences]. "'";
                $occurences++;

            } else {
                $temp_string .= $query[$i];
            }
        }
        $new_q = $temp_string;
        return $new_q;
	}

	public function getSql() {
		$builder = clone $this->builder;
		if ($this->builder instanceof \Illuminate\Database\Eloquent\Builder) {
			$builder = $this->builder->getQuery();
		}
		$data =  ['query' => $builder->toSql(), 'bindings' => $builder->getBindings()];
		return $this->dbd($data);
	}

	public function total() {
	 	return $this->builder->toBase()->getCountForPagination();
	}

	public function lqPaginate($columns = ['*'], $fetch_total_for_all_page = false) {
        $model = $this->builder->getModel();
        $builder = clone $this->builder;
		$page = $this->request->page ? $this->request->page : 1;
        $perPage = $this->request->page_size ? $this->request->page_size : $model->getPerPage();
        if ($perPage != '-1') {
            $builder->forPage($page, $perPage);
        }
        $results = $builder->get($columns);
		$data = [];
		$data['data'] = $results;
		if ($page == 1 || $fetch_total_for_all_page) {
			$data['total'] = $perPage == '-1' ? $results->count() : $this->total();
		}
		return $data;
	}
	/**
     * Update a record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function lqUpdate(array $values) {
		return $this->builder->getModel()->update($values);
	}
}
