<?php

namespace App\Http\Controllers;

use App\Models\Changelog;
use App\Models\ChangelogItem;
use App\Models\ChangelogRead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChangelogController extends Controller
{
    public function adminIndex()
    {
        $changelogs = Changelog::with('items')->orderByDesc('created_at')->get();
        return view('admin.changelogs.index', compact('changelogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'notification_style' => 'required|in:badge,modal,both',
            'is_published' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|in:feature,improvement,bugfix,hotfix',
            'items.*.description' => 'required|string',
            'items.*.sort_order' => 'integer',
        ]);

        if (!empty($validated['is_published']) && empty($validated['published_at'])) {
            $validated['published_at'] = Carbon::now();
        }

        $changelog = Changelog::create([
            'title' => $validated['title'],
            'version' => $validated['version'] ?? null,
            'notification_style' => $validated['notification_style'],
            'is_published' => $validated['is_published'] ?? false,
            'published_at' => $validated['published_at'] ?? null,
        ]);

        foreach ($validated['items'] as $index => $item) {
            ChangelogItem::create([
                'changelog_id' => $changelog->id,
                'category' => $item['category'],
                'description' => $item['description'],
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }

        return response()->json(['success' => true, 'changelog' => $changelog->load('items')]);
    }

    public function update(Request $request, Changelog $changelog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'notification_style' => 'required|in:badge,modal,both',
            'is_published' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|in:feature,improvement,bugfix,hotfix',
            'items.*.description' => 'required|string',
            'items.*.sort_order' => 'integer',
        ]);

        $wasPublished = $changelog->is_published;

        $updateData = [
            'title' => $validated['title'],
            'version' => $validated['version'] ?? null,
            'notification_style' => $validated['notification_style'],
            'is_published' => $validated['is_published'] ?? false,
        ];

        if (!empty($validated['is_published']) && !$wasPublished) {
            $updateData['published_at'] = Carbon::now();
        } elseif (empty($validated['is_published'])) {
            $updateData['published_at'] = null;
        }

        $changelog->update($updateData);

        $changelog->items()->delete();

        foreach ($validated['items'] as $index => $item) {
            ChangelogItem::create([
                'changelog_id' => $changelog->id,
                'category' => $item['category'],
                'description' => $item['description'],
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }

        return response()->json(['success' => true, 'changelog' => $changelog->fresh()->load('items')]);
    }

    public function destroy(Changelog $changelog)
    {
        $changelog->delete();
        return response()->json(['success' => true]);
    }

    public function markRead(Request $request, Changelog $changelog)
    {
        $userId = auth()->id();

        ChangelogRead::firstOrCreate(
            ['user_id' => $userId, 'changelog_id' => $changelog->id],
            ['read_at' => Carbon::now()]
        );

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        $userId = auth()->id();

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => true]);
        }

        $changelogs = Changelog::published()->whereIn('id', $ids)->get();

        foreach ($changelogs as $changelog) {
            ChangelogRead::firstOrCreate(
                ['user_id' => $userId, 'changelog_id' => $changelog->id],
                ['read_at' => Carbon::now()]
            );
        }

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $userId = auth()->id();
        $count = Changelog::published()->unreadBy($userId)->count();
        return response()->json(['count' => $count]);
    }
}
