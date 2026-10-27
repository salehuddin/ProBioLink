<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Support\BlockPresets;
use App\Support\BlockSchema;
use App\Support\Templates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::query()
            ->where('is_published', true)
            ->orderBy('display_name')
            ->get(['username', 'display_name', 'headline']);

        return response()->json(['data' => $profiles]);
    }

    public function show(Profile $profile): ProfileResource
    {
        abort_unless($profile->is_published, 404);

        return new ProfileResource($profile->load('blocks'));
    }

    public function update(Request $request, Profile $profile): ProfileResource
    {
        Gate::authorize('update', $profile);

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:120'],
            'template' => ['sometimes', Rule::in(Templates::keys())],
        ]);

        $profile->update($validated);

        return new ProfileResource($profile->load('blocks'));
    }

    public function applyPreset(Request $request, Profile $profile): ProfileResource
    {
        Gate::authorize('update', $profile);

        $validated = $request->validate([
            'preset' => ['required', Rule::in(BlockPresets::keys())],
        ]);

        DB::transaction(function () use ($profile, $validated) {
            $profile->blocks()->delete();

            foreach (BlockPresets::blocks($validated['preset']) as $position => $block) {
                $profile->blocks()->create([
                    'type' => $block['type'],
                    'position' => $position,
                    'is_visible' => true,
                    'data' => $block['data'],
                ]);
            }

            $profile->update(['preset' => $validated['preset']]);
        });

        return new ProfileResource($profile->load('blocks'));
    }

    public function syncBlocks(Request $request, Profile $profile): ProfileResource
    {
        Gate::authorize('update', $profile);

        $types = $profile->blocks()->pluck('type', 'id');
        $input = $request->all();

        $rules = [
            'blocks' => ['required', 'array'],
            'blocks.*.id' => ['required', 'integer'],
            'blocks.*.position' => ['required', 'integer', 'min:0'],
            'blocks.*.is_visible' => ['required', 'boolean'],
            'blocks.*.data' => ['sometimes', 'array'],
        ];

        foreach ($input['blocks'] ?? [] as $index => $block) {
            if (! is_array($block) || ! isset($block['data'], $block['id'])) {
                continue;
            }

            $type = $types[(int) $block['id']] ?? '';

            if ($type !== '') {
                $rules += BlockSchema::rulesFor($type, "blocks.{$index}.data");
            }
        }

        $validated = Validator::make($input, $rules)->validate();

        $owned = $profile->blocks()->pluck('id')->all();

        foreach ($validated['blocks'] as $block) {
            if (! in_array($block['id'], $owned, true)) {
                abort(422, 'Block does not belong to this profile.');
            }
        }

        DB::transaction(function () use ($profile, $validated) {
            foreach ($validated['blocks'] as $block) {
                $changes = [
                    'position' => $block['position'],
                    'is_visible' => $block['is_visible'],
                ];

                if (array_key_exists('data', $block)) {
                    $changes['data'] = $block['data'];
                }

                $profile->blocks()
                    ->whereKey($block['id'])
                    ->update($changes);
            }
        });

        return new ProfileResource($profile->load('blocks'));
    }
}
