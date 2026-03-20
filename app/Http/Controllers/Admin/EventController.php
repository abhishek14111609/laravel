<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventSlot;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::with('category')->latest()->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.id' => ['nullable', 'integer'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.slot' => ['required', 'string', 'max:100'],
            'slots.*.capacity' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        } else {
            unset($validated['image']);
        }

        DB::transaction(function () use ($validated) {
            $slots = $validated['slots'];
            unset($validated['slots']);

            $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
            $validated['total_slots'] = collect($slots)->sum('capacity');

            $event = Event::create($validated);

            foreach ($slots as $slotData) {
                $event->slots()->create($slotData);
            }
        });

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $event->load('slots');
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.id' => ['nullable', 'integer'],
            'slots.*.date' => ['nullable', 'date', 'after_or_equal:today'],
            'slots.*.slot' => ['nullable', 'string', 'max:100'],
            'slots.*.capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            $oldImagePath = $event->image_storage_path;
            $validated['image'] = $request->file('image')->store('events', 'public');

            if ($oldImagePath !== null) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            unset($validated['image']);
        }

        $slots = collect($validated['slots'])
            ->filter(fn(array $slot) => ! empty($slot['date']) && ! empty($slot['slot']) && ! empty($slot['capacity']))
            ->values()
            ->all();

        if (count($slots) === 0) {
            throw ValidationException::withMessages([
                'slots' => 'At least one valid slot is required.',
            ]);
        }

        DB::transaction(function () use ($validated, $event) {
            $slots = collect($validated['slots'])
                ->filter(fn(array $slot) => ! empty($slot['date']) && ! empty($slot['slot']) && ! empty($slot['capacity']))
                ->values()
                ->all();
            unset($validated['slots']);

            $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
            $existingSlots = $event->slots()->lockForUpdate()->get()->keyBy('id');
            $submittedIds = collect($slots)
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->values();

            foreach ($slots as $slotData) {
                $slotId = isset($slotData['id']) ? (int) $slotData['id'] : null;

                if ($slotId !== null) {
                    /** @var EventSlot|null $slot */
                    $slot = $existingSlots->get($slotId);
                    if (! $slot) {
                        throw ValidationException::withMessages([
                            'slots' => 'Invalid slot reference provided.',
                        ]);
                    }

                    $hasBookings = $slot->bookings()->exists();

                    // Preserve historical integrity: immutable date/time once booked.
                    if ($hasBookings && ($slot->date->toDateString() !== $slotData['date'] || $slot->slot !== $slotData['slot'])) {
                        throw ValidationException::withMessages([
                            'slots' => 'Booked slots cannot change date/time.',
                        ]);
                    }

                    if ($slotData['capacity'] < $slot->booked_count) {
                        throw ValidationException::withMessages([
                            'slots' => 'Slot capacity cannot be below current booked count.',
                        ]);
                    }

                    $slot->update([
                        'date' => $slotData['date'],
                        'slot' => $slotData['slot'],
                        'capacity' => $slotData['capacity'],
                    ]);

                    continue;
                }

                $event->slots()->create([
                    'date' => $slotData['date'],
                    'slot' => $slotData['slot'],
                    'capacity' => $slotData['capacity'],
                    'booked_count' => 0,
                ]);
            }

            $existingSlots
                ->filter(fn(EventSlot $slot) => ! $submittedIds->contains($slot->id))
                ->each(function (EventSlot $slot): void {
                    if ($slot->bookings()->exists()) {
                        throw ValidationException::withMessages([
                            'slots' => 'Booked slots cannot be deleted.',
                        ]);
                    }
                    EventSlot::whereKey($slot->getKey())->delete();
                });

            $validated['total_slots'] = (int) $event->slots()->sum('capacity');
            $event->update($validated);
        });

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image_storage_path !== null) {
            Storage::disk('public')->delete($event->image_storage_path);
        }

        Event::whereKey($event->getKey())->delete();

        return back()->with('success', 'Event deleted.');
    }
}
