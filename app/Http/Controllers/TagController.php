<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * タグを新規登録する
     */
    public function store(TagRequest $request)
    {
        $validated = $request->validated();

        Tag::create($validated);

        return redirect()->route('admin');
    }

    /**
     * タグ編集ページを取得する
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * タグを更新
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect()->route('admin');
    }

    /**
     * タグを削除
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin');
    }
}
