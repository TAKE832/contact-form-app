<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
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

        $contact = Contact::create($validated);

        $contact->tags()->attach($validated['tag_ids']);

        return redirect()->route('thanks');
    }

    /**
     * サンクスページを表示
     */
    public function thanks()
    {
        return view('contact.thanks');
    }

    /**
     * お問い合わせをダウンロードする機能
     */
    public function export(ExportContactRequest $request)
    {
        $query = Contact::with(['category', 'tags']);

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

        $contacts = $query->latest()->get();

        return response()->streamDownload(function () use ($contacts) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', '名前', '性別', 'メール', '電話', '住所', '建物', 'カテゴリ', '内容', '作成日時']);
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->first_name.' '.$contact->last_name,
                    $contact->genderLabel,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building ?? '',
                    $contact->category->content,
                    $contact->detail,
                    $contact->created_at,
                ]);
            }
            fclose($file);
        }, 'contacts.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
