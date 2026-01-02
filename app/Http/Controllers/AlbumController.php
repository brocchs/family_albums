<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AlbumController extends Controller
{
    public function index(): Response
    {
        $albums = Album::with(['photos' => fn ($query) => $query->latest()->take(6), 'user'])
            ->withCount('photos')
            ->latest()
            ->get();

        return Inertia::render('Albums/Index', [
            'albums' => $albums->map(function (Album $album) {
                $cover = $album->cover_path ?: optional($album->photos->first())->path;

                return [
                    'id' => $album->id,
                    'title' => $album->title,
                    'description' => $album->description,
                    'owner' => $album->user?->name,
                    'cover' => $cover ? Storage::url($cover) : null,
                    'photos' => $album->photos->map(fn (Photo $photo) => [
                        'id' => $photo->id,
                        'title' => $photo->title,
                        'url' => Storage::url($photo->path),
                    ]),
                    'photos_count' => $album->photos_count,
                ];
            }),
            'canManage' => Auth::check(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $album = Album::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('albums.show', $album)
            ->with('success', 'Album berhasil dibuat. Mari tambahkan foto pertama!');
    }

    public function show(Album $album): Response
    {
        $album->load(['photos.user', 'user']);

        $coverPath = $album->cover_path ?: optional($album->photos->first())->path;

        return Inertia::render('Albums/Show', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'description' => $album->description,
                'owner' => $album->user?->name,
                'cover' => $coverPath ? Storage::url($coverPath) : null,
                'photos' => $album->photos->map(fn (Photo $photo) => [
                    'id' => $photo->id,
                    'title' => $photo->title,
                    'caption' => $photo->caption,
                    'url' => Storage::url($photo->path),
                    'taken_at' => optional($photo->taken_at)?->format('Y-m-d'),
                    'uploader' => $photo->user?->name,
                ]),
            ],
            'canUpload' => Auth::check(),
        ]);
    }

    public function storePhoto(Request $request, Album $album)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'taken_at' => ['nullable', 'date'],
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('photo')->store("albums/{$album->id}", 'public');

        $photo = $album->photos()->create([
            'user_id' => $request->user()->id,
            'title' => $data['title'] ?? null,
            'caption' => $data['caption'] ?? null,
            'taken_at' => $data['taken_at'] ?? null,
            'path' => $path,
        ]);

        if (! $album->cover_path) {
            $album->update(['cover_path' => $photo->path]);
        }

        return redirect()
            ->back()
            ->with('success', 'Foto berhasil diunggah ke album.');
    }
}
