<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexQueryRequest;
use App\Models\Category;
use App\Models\Contact;


class AdminController extends Controller
{
    public function index(IndexQueryRequest $request)
    {
        $query = Contact::with(['category', 'tags']);
        $categories = Category::all();

        // キーワードで検索（半角・全角を除外）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // 性別で検索
        if ($request->gender && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        // お問い合わせの種類で検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付で検索
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->paginate(7);

        return view('admin.index', compact(['contacts', 'categories']));
    }

    /**
     * お問い合わせの詳細を表示
     */
    public function show(Contact $contact)
    {
        return view('admin.show', compact('contact'));
    }

    /**
     * お問い合わせを削除
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin');
    }
}
