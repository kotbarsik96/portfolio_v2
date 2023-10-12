<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use DB;

class WorkFilter extends QueryFilter
{
    public function types($titles)
    {
        $this->builder->whereIn('id', function (Builder $query) use ($titles) {
            $query->select('works_types.work_id')
                ->from('works_types')
                ->join('types', 'types.id', '=', 'works_types.type_id')
                ->whereIn('types.title', $titles)
                ->groupBy('works_types.work_id')
                ->havingRaw('COUNT(DISTINCT types.id) = ?', [count($titles)]);
        });
    }

    public function stack($titles)
    {
        $this->builder->whereIn('id', function (Builder $query) use ($titles) {
            $query->select('works_stack.work_id')
                ->from('works_stack')
                ->join('stack', 'stack.id', '=', 'works_stack.stack_id')
                ->whereIn('stack.title', $titles)
                ->groupBy('works_stack.work_id')
                ->havingRaw('COUNT(DISTINCT stack.id) = ?', [count($titles)]);
        });
    }

    public function skills($titles)
    {
        $this->builder->whereIn('id', function (Builder $query) use ($titles) {
            $query->select('works_skills.work_id')
                ->from('works_skills')
                ->join('skills', 'skills.id', '=', 'works_skills.skill_id')
                ->whereIn('skills.title', $titles)
                ->groupBy('works_skills.work_id')
                ->havingRaw('COUNT(DISTINCT skills.id) = ?', [count($titles)]);
        });
    }
}