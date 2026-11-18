<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    /**
     * お問い合わせフォームを表示
     */
    public function index()
    {
        $categories = Category::all();

        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * お問い合わせ内容を確認
     */
    public function confirm(StoreContactRequest $request, Category $category)
    {
        $validated = $request->validated();

        // セッションを保持して入力フォームに戻る
        session()->flash('_old_input', $validated);

        $category = Category::find($request->category_id);

        $tags = Tag::find($request->tag_ids);

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    /**
     * お問い合わせ内容を送信
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        Contact::create($validated);

        return redirect()->route('thanks');
    }

    /**
     * サンクスページを表示
     */
    public function thanks()
    {
        return view('contact.thanks');
    }
}
