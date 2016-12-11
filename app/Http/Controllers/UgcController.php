<?php

namespace App\Http\Controllers;

use App\UGC;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class UgcController extends Controller
{
    private $order_field = ['created_at'];

    public function get($id)
    {
        $ugc = UGC::find($id);
        if ($ugc) {
            return $this->success($ugc);
        } else {
            return $this->error(404, 'not found');
        }
    }

    public function add(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');
        $creator = $request->input('creator');
        $reply_to = $request->input('reply_to');

        if ($content == null) {
            return $this->error(501, 'content cannot be null');
        }
        if ($creator == null) {
            return $this->error(502, 'creator cannot be null');
        }
        if ($reply_to == null) {
            $reply_to = 0;
        }

        $ugc = new UGC();
        $ugc->title = $title;
        $ugc->content = $content;
        $ugc->creator = $creator;
        $ugc->reply_to = $reply_to;

        if ($ugc->save()) {
            return $this->success();
        } else {
            return $this->error(503, 'save failed');
        }
    }

    public function edit($id, Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        $ugc = UGC::find($id);
        if ($ugc) {
            if ($content == null) {
                return $this->error(501, 'content cannot be null');
            }

            $ugc->content = $content;
            $ugc->title = $title;

            if ($ugc->save()) {
                return $this->success();
            } else {
                return $this->error(503, 'save failed');
            }

        } else {
            return $this->error(404, 'not found');
        }

    }

    public function delete($id)
    {
        $ugc = UGC::find($id);
        if ($ugc) {
            if ($ugc->delete()) {
                return $this->success();
            } else {
                return $this->error(504, 'delete failed');
            }
        } else {
            return $this->error(404, 'not found');
        }
    }

    public function getByCreator($creator, Request $request)
    {
        $perPage = intval($request->input('perPage', 10));
        $curPage = intval($request->input('curPage', 1));
        $order = $request->input('order', 'desc');
        $orderBy = $request->input('orderBy', 'created_at');
        $withReply = boolval($request->input('withReply', '0'));

        if ($perPage <= 0) {
            return $this->error(600, 'page num invalid');
        }
        if ($curPage <= 0) {
            return $this->error(601, 'current page invalid');
        }
        if ($order != 'desc' && $order != 'asc') {
            return $this->error(602, 'order invalid');
        }
        if (!$this->order_field_valid($orderBy)) {
            return $this->error(505, 'order field invalid');
        }


        $builder = UGC::where('creator', $creator);
        if (!$withReply) {
            $builder->where('reply_to', 0);
        }

        $total = $builder->count();
        $hasMore = $total > $curPage * $perPage ? true : false;

        $ugc = $builder
            ->offset(($curPage - 1) * $perPage)
            ->limit($perPage)
            ->orderBy($orderBy, $order)
            ->get();

        $data = [
            'perPage' => $perPage,
            'curPage' => $curPage,
            'total' => $total,
            'hasMore' => $hasMore,
            'data' => $ugc
        ];

        return $this->success($data);
    }

    public function getByReplyTo($reply_to, Request $request)
    {
        $perPage = intval($request->input('perPage', 10));
        $curPage = intval($request->input('curPage', 1));
        $order = $request->input('order', 'desc');
        $orderBy = $request->input('orderBy', 'created_at');

        if ($perPage <= 0) {
            return $this->error(600, 'page num invalid');
        }
        if ($curPage <= 0) {
            return $this->error(601, 'current page invalid');
        }
        if ($order != 'desc' && $order != 'asc') {
            return $this->error(602, 'order invalid');
        }
        if (!$this->order_field_valid($orderBy)) {
            return $this->error(505, 'order field invalid');
        }

        $builder = UGC::where('reply_to', $reply_to);
        $total = $builder->count();
        $hasMore = $total > $curPage * $perPage ? true : false;

        $ugc = $builder
            ->offset(($curPage - 1) * $perPage)
            ->limit($perPage)
            ->orderBy($orderBy, $order)
            ->get();
        $data = [
            'perPage' => $perPage,
            'curPage' => $curPage,
            'total' => $total,
            'hasMore' => $hasMore,
            'data' => $ugc
        ];

        return $this->success($data);
    }

    private function order_field_valid($field)
    {
        return in_array($field, $this->order_field);
    }

}
