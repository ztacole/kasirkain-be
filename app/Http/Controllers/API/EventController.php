<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventDetailResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('id')->all();

        $response = EventResource::collection($events);

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function show($id)
    {
        $event = Event::load('eventProducts')->find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        $response = new EventDetailResource($event);

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'discount_percentage' => 'required|integer|min:0|max:100',
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create event
            $event = Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_percentage' => $request->discount_percentage,
            ]);

            // Create event products
            $event->eventProducts()->attach($request->product_ids);

            DB::commit();

            $response = new EventDetailResource($event->load('eventProducts'));

            return response()->json([
                'status' => 'success',
                'message' => 'Event created successfully',
                'data' => $response,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'discount_percentage' => 'required|integer|min:0|max:100',
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update event
            $event->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_percentage' => $request->discount_percentage,
            ]);

            // Update event products
            $event->eventProducts()->sync($request->product_ids);

            DB::commit();

            $response = new EventDetailResource($event->load('eventProducts'));

            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
            ], 404);
        }

        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully',
        ]);
    }
}
