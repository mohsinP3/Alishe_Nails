<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind the 'admin' guard middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'shape' => ['nullable', 'string', 'in:Almond,Coffin,Square,Stiletto'],
            'length' => ['nullable', 'string', 'in:Short,Medium,Long,Extra Long'],
            'finish' => ['nullable', 'string', 'in:Glossy,Matte,Glitter/Chrome'],
            'badge' => ['nullable', 'string', 'max:30'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],

            // ---------- Unified ordered media gallery ----------
            // Already-uploaded items the admin kept, in their chosen order:
            // one "type|filename" string per position. The filename segment
            // only allows slug/time/random/extension characters, so path
            // traversal can never sneak in.
            'existing_media' => ['nullable', 'array', 'max:10'],
            'existing_media.*' => ['string', 'regex:/^(image|video)\|[A-Za-z0-9._-]+$/'],

            // Newly picked files, submitted at their position in the gallery.
            // MIME + size checked here; per-type size + type matching below.
            'media_files' => ['nullable', 'array', 'max:10'],
            'media_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:20480'],
            'media_types' => ['nullable', 'array', 'max:10'],
            'media_types.*' => ['in:image,video'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $existing = count($this->input('existing_media', []));
            $newCount = 0;

            foreach ($this->file('media_files', []) ?? [] as $index => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $newCount++;
                $type = $this->input('media_types.'.$index);

                if (! in_array($type, ['image', 'video'], true)) {
                    $validator->errors()->add('media_files.'.$index, 'Each uploaded file needs a media type.');
                    continue;
                }

                // Images and videos have different ceilings — enforce the
                // right one per item (4MB images, 20MB videos).
                $maxKb = $type === 'video' ? 20480 : 4096;
                if ($file->getSize() > $maxKb * 1024) {
                    $validator->errors()->add('media_files.'.$index, $type === 'video'
                        ? 'Videos may not be larger than 20MB.'
                        : 'Images may not be larger than 4MB.');
                }

                // The declared type must match the real content type, so an
                // "image" slot can never carry a video (or an executable).
                // MP4 files may be detected as video/mp4 OR application/mp4.
                $allowed = $type === 'video'
                    ? ['video/mp4', 'application/mp4', 'video/webm', 'audio/webm']
                    : ['image/jpeg', 'image/png', 'image/webp'];

                if (! in_array($file->getMimeType(), $allowed, true)) {
                    $validator->errors()->add('media_files.'.$index, $type === 'video'
                        ? 'The file must be a real MP4 or WebM video.'
                        : 'The file must be a real JPG, PNG or WebP image.');
                }
            }

            $total = $existing + $newCount;

            if ($total === 0) {
                $validator->errors()->add('media', 'At least one image or video is required.');
            } elseif ($total > 10) {
                $validator->errors()->add('media', 'A product can have at most 10 media items.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'media_files.*.mimes' => 'Images must be JPG, PNG or WebP; videos must be MP4 or WebM.',
            'media_files.*.max' => 'Videos may not be larger than 20MB.',
            'existing_media.*.regex' => 'Invalid media item.',
        ];
    }
}
