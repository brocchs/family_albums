<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Contracts\Encryption\DecryptException;

class AlbumController extends Controller
{
    private function encodeId(int $id): string
    {
        return urlencode(Crypt::encryptString($id));
    }

    private function findAlbumByToken(string $token, bool $withPhotos = false): Album
    {
        try {
            $id = Crypt::decryptString(urldecode($token));
        } catch (DecryptException) {
            abort(HttpResponse::HTTP_NOT_FOUND);
        }

        $query = Album::query()->whereKey($id)->with('user');
        if ($withPhotos) {
            $query->with('photos.user');
        }

        return $query->firstOrFail();
    }

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
                    'token' => $this->encodeId($album->id),
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
            ->route('albums.show', $this->encodeId($album->id))
            ->with('success', 'Album berhasil dibuat. Mari tambahkan foto pertama!');
    }

    public function update(Request $request, string $albumToken)
    {
        $album = $this->findAlbumByToken($albumToken);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $album->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => $album->user_id ?: $request->user()->id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Detail album diperbarui.');
    }

    public function show(string $albumToken): Response
    {
        $album = $this->findAlbumByToken($albumToken, true);

        $coverPath = $album->cover_path ?: optional($album->photos->first())->path;

        return Inertia::render('Albums/Show', [
            'album' => [
                'id' => $album->id,
                'token' => $this->encodeId($album->id),
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

    public function storePhoto(Request $request, string $albumToken)
    {
        $album = $this->findAlbumByToken($albumToken);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'taken_at' => ['nullable', 'date'],
            'photos' => ['required', 'array'],
            'photos.*' => ['required', 'image', 'max:5120'],
        ]);

        $uploadedPhotos = [];
        
        foreach ($request->file('photos') as $file) {
            $path = $file->store("albums/{$album->id}", 'public');

            $photo = $album->photos()->create([
                'user_id' => $request->user()->id,
                'title' => $data['title'] ?? null,
                'caption' => $data['caption'] ?? null,
                'taken_at' => $data['taken_at'] ?? null,
                'path' => $path,
            ]);
            
            $uploadedPhotos[] = $photo;
        }

        if (! $album->cover_path && !empty($uploadedPhotos)) {
            $album->update(['cover_path' => $uploadedPhotos[0]->path]);
        }

        $count = count($uploadedPhotos);
        $message = $count === 1 ? 'Foto berhasil diunggah ke album.' : "{$count} foto berhasil diunggah ke album.";

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function destroy(string $albumToken)
    {
        $album = $this->findAlbumByToken($albumToken, true);

        // Delete all photos from storage
        foreach ($album->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        // Delete album and all related photos
        $album->delete();

        return redirect()
            ->route('albums.index')
            ->with('success', 'Album dan semua foto di dalamnya berhasil dihapus.');
    }

    public function destroyPhoto(Request $request, string $albumToken, Photo $photo)
    {
        $album = $this->findAlbumByToken($albumToken);
        $photo = $album->photos()->whereKey($photo->id)->firstOrFail();

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        if ($album->cover_path === $photo->path) {
            $nextCover = $album->photos()->latest()->first();
            $album->update(['cover_path' => $nextCover?->path]);
        }

        return redirect()
            ->back()
            ->with('success', 'Foto berhasil dihapus.');
    }
}
