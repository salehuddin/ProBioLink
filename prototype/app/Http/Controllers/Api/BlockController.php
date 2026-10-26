<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Block;
use App\Models\Profile;
use App\Support\BlockSchema;
use App\Support\BlockTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BlockController extends Controller
{
    public function store(Request $request, Profile $profile): ProfileResource
    {
        Gate::authorize('update', $profile);

        $validated = $request->validate([
            'type' => ['required', Rule::in(BlockTemplates::types())],
        ]);

        $nextPosition = $profile->blocks()->max('position');

        $profile->blocks()->create([
            'type' => $validated['type'],
            'position' => $nextPosition === null ? 0 : $nextPosition + 1,
            'is_visible' => true,
            'data' => BlockTemplates::defaults($validated['type']),
        ]);

        return new ProfileResource($profile->load('blocks'));
    }

    public function update(Request $request, Profile $profile, Block $block): ProfileResource
    {
        Gate::authorize('update', $profile);

        abort_unless($block->profile_id === $profile->id, 404);

        $validated = $request->validate([
            'data' => ['required', 'array'],
            ...BlockSchema::rulesFor($block->type, 'data'),
        ]);

        $block->update(['data' => $validated['data']]);

        return new ProfileResource($profile->load('blocks'));
    }

    public function destroy(Profile $profile, Block $block): ProfileResource
    {
        Gate::authorize('update', $profile);

        abort_unless($block->profile_id === $profile->id, 404);

        DB::transaction(function () use ($profile, $block) {
            $block->delete();

            $profile->blocks()
                ->orderBy('position')
                ->get()
                ->each(fn (Block $item, int $index) => $item->update(['position' => $index]));
        });

        return new ProfileResource($profile->load('blocks'));
    }
}
