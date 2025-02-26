<?php

namespace App\Services;

use App\Models\Tag;
use Exception;
use Illuminate\Support\Facades\DB;

class TagService
{
    /**
     * Create a new tag.
     *
     * @return void
     *
     * @throws Exception
     */
    public function createTag(array $data)
    {
        DB::transaction(function () use ($data) {
            Tag::create($data);
        });
    }

    /**
     * Update an existing tag.
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateTag(Tag $tag, array $data)
    {
        DB::transaction(function () use ($tag, $data) {
            $tag->update($data);
        });
    }

    /**
     * Delete a tag.
     *
     * @return void
     *
     * @throws Exception
     */
    public function deleteTag(Tag $tag)
    {
        DB::transaction(function () use ($tag) {
            $tag->delete();
        });
    }
}
