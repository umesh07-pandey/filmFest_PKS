<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function createEvent(Request $request)
    {
        try {

            DB::beginTransaction();

            $adminData = auth()->user();
            $eventData = $adminData->event;
            // $categoryData=$eventData->category;
            // $categoryId=$eventData->id;
            // Log::info("data",["categiryId"=>$categoryId]);


            // $validateData=$request->validate([
            //     "name" => "required|string|max:255",
            //     "description" => "required|string|max:255",
            //     "starting_date" => "required|date_format:Y-m-d H:i:s",
            //     "ending_date" => "required|date_format:Y-m-d H:i:s|after_or_equal:starting_date",
            //     "venue_name" => "required|string|max:255",
            //     "address_line" => "required|string|max:255",
            //     "city" => "required|string|max:255",
            //     "state" => "required|string|max:255",
            //     "country" => "required|string|max:255",
            //     "pin_code" => "required|string|max:20",
            //     "event_capicity" => "required|string|max:20",
            //     "image" => "required|string|max:255",
            //     "category_id" => "required|exists:category,id",
            // ]);



            // $eventData = Event::create([
            //     "name" => $request->name,
            //     "description" => $request->description,
            //     "starting_date" => $request->starting_date,
            //     "ending_date" => $request->ending_date,
            //     "venue_name" => $request->venue_name,
            //     "address_line" => $request->address_line,
            //     "city" => $request->city,
            //     "state" => $request->state,
            //     "country" => $request->country,
            //     "pin_code" => $request->pin_code,
            //     "image" => $request->image,
            //     "event_capicity"=>$request->event_capicity,
            //     "admin_id" => $adminData->id,
            //     "category_id"=>$request->category_id,
            // ]);










            $validator = Validator::make($request->all(), [
                "name" => "required|string|max:255",
                "description" => "required|string|max:255",
                "starting_date" => "required|date_format:Y-m-d H:i:s",
                "ending_date" => "required|date_format:Y-m-d H:i:s|after_or_equal:starting_date",
                "venue_name" => "required|string|max:255",
                "address_line" => "required|string|max:255",
                "city" => "required|string|max:255",
                "state" => "required|string|max:255",
                "country" => "required|string|max:255",
                "pin_code" => "required|string|max:20",
                "event_capicity" => "required|string|max:20",
                "image" => "required|string|max:255",
                "category_id" => "required|exists:category,id",
            ]);

            // Validation fail hone par
            if ($validator->fails()) {
                return response()->json([

                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                    "status" => "false",
                ], 422);
            }




            // Validation successful — ab event create karo
            $validatedData = $validator->validated();

            $event = Event::create([
                "name" => $validatedData['name'],
                "description" => $validatedData['description'],
                "starting_date" => $validatedData['starting_date'],
                "ending_date" => $validatedData['ending_date'],
                "venue_name" => $validatedData['venue_name'],
                "address_line" => $validatedData['address_line'],
                "city" => $validatedData['city'],
                "state" => $validatedData['state'],
                "country" => $validatedData['country'],
                "pin_code" => $validatedData['pin_code'],
                "image" => $validatedData['image'],
                "event_capicity" => $validatedData['event_capicity'],
                "admin_id" => $adminData->id,
                "category_id" => $validatedData['category_id'],
            ]);


            $data = [
                "admin_id" => $adminData->id,
                "event" => $event
            ];

            DB::commit();

            return response()->json([
                "message" => "Event created successfully",
                "data" => $data,
                "status" => "true"
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "status" => "false"
            ], 500);
        }
    }

    public function uploadEventImage(Request $request)
    {
        try {
            // Validate image
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // max 2MB
            ]);

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('image'), $imageName);

            $imagePath = url('image/' . $imageName);

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image_path' => $imagePath,
                'status' => true
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function updateEvent(Request $request)
    {
        try {

            DB::beginTransaction();

            $adminData = auth()->user();
            $updateId = $request->query('id');

            // Validate the request data
            $validator = Validator::make($request->all(), [
                "name" => "sometimes|string|max:255",
                "description" => "sometimes|string|max:255",
                "starting_date" => "sometimes|date_format:Y-m-d H:i:s",
                "ending_date" => "sometimes|date_format:Y-m-d H:i:s|after_or_equal:starting_date",
                "venue_name" => "sometimes|string|max:255",
                "address_line" => "sometimes|string|max:255",
                "city" => "sometimes|string|max:255",
                "state" => "sometimes|string|max:255",
                "country" => "sometimes|string|max:255",
                "pin_code" => "sometimes|string|max:20",
                "image" => "sometimes|string|max:255",
                "event_capicity" => "sometimes|string|max:20",
                "category_id" => "sometimes|exists:category,id",
            ]);

            // Agar validation fail ho
            if ($validator->fails()) {
                return response()->json([
                    
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                    "status"=>"false"
                ], 422);
            }

            // Event find karo
            $event = Event::where('id', $updateId)->where('admin_id', $adminData->id)->first();

            if (!$event) {
                return response()->json([
                    "message" => "Event not found or you don't have permission to update it.",
                    "user_description" => "Event not found or you don't have permission to update it.",
                    "status" => "false"
                ], 404);
            }

            // Sirf validated data leke update karo
            $validatedData = $validator->validated();
            $event->update($validatedData);


            DB::commit();

            return response()->json([
                "message" => "Event updated successfully",
                "data" => $event,
                "status" => "true"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "user_description" => "There is some problem to update a event",
                "status" => "false"
            ], 500);
        }
    }

    public function deleteEvent(Request $request)
    {
        try {
            DB::beginTransaction();

            $adminData = auth()->user();
            $deleteId = $request->query('id');


            $event = Event::where('id', $deleteId)
                ->where('admin_id', $adminData->id)
                ->first();

            if (!$event) {
                return response()->json([
                    "message" => "Event not found or you don't have permission to delete it.",
                    "status" => "false"
                ], 404);
            }

            $event->delete();

            DB::commit();

            return response()->json([
                "message" => "Event deleted successfully.",
                "status" => "true"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "status" => "false"
            ], 500);
        }
    }

    public function getAllAdminEvents(Request $request)
    {
        try {
            $adminData = auth()->user();

            $perPage = $request->query('per_page', 10);

            $events = Event::where('admin_id', $adminData->id)->paginate($perPage);

            if ($events->isEmpty()) {
                return response()->json([
                    "message" => "No events found.",
                    "status" => "false"
                ], 404);
            }

            // Custom response format
            $customData = $events->getCollection()->transform(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'venue_name' => $event->venue_name,
                    'address_line' => $event->address_line,
                    'city' => $event->city,
                    'state' => $event->state,
                    'country' => $event->country,
                    'pin_code' => $event->pin_code,
                    'starting_date' => $event->starting_date,
                    'ending_date' => $event->ending_date,
                    'image' => $event->image,
                    'event_capicity'=>$event->event_capicity,
                    'category_id'=>$event->category_id

                ];
            });

            // Final response
            $finalAllEvent = [
                'message' => 'Events fetched successfully.',
                'status' => 'true',
                'current_page' => $events->currentPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'last_page' => $events->lastPage(),
                'data' => $customData,
            ];

            return response()->json($finalAllEvent, 200);

        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "status" => "false"
            ], 500);
        }
    }

    public function getLastDateEvents(Request $request)
    {
        try {
            $admin = auth()->user();
            $now = now();
            $next24Hours = now()->addDay();
            $perPage = $request->query('per_page', 10);

            $events = Event::where('admin_id', $admin->id)
                ->where('starting_date', '>=', $now)
                ->where('starting_date', '<=', $next24Hours)
                ->orderBy('starting_date', 'asc')
                ->paginate($perPage);

            if ($events->isEmpty()) {
                return response()->json([
                    "message" => "No events starting in the next 24 hours.",
                    "user_description" => "No events starting in the next 24 hours.",
                    "status" => "false"
                ], 404);
            }

            // transform data
            $customData = $events->getCollection()->transform(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'venue_name' => $event->venue_name,
                    'address_line' => $event->address_line,
                    'city' => $event->city,
                    'state' => $event->state,
                    'country' => $event->country,
                    'pin_code' => $event->pin_code,
                    'starting_date' => $event->starting_date,
                    'ending_date' => $event->ending_date,
                    'image' => $event->image,
                    'event_capicity'=>$event->event_capicity,
                    'category_id'=>$event->category_id
                ];
            });

            // final response
            $response = [
                "message" => "Events starting in the next 24 hours fetched successfully.",
                "user_description" => "Events starting in the next 24 hours fetched successfully.",
                "status" => "true",
                "current_page" => $events->currentPage(),
                "per_page" => $events->perPage(),
                "total" => $events->total(),
                "last_page" => $events->lastPage(),
                "data" => $customData
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "user_description" => "There is some problem to get the recent upcomming event.",
                "status" => "false"
            ], 500);
        }
    }

    public function createCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            auth()->user();
            $data = $request->validate([
                "category_name" => "required|string|max:255",
            ]);

            $categoryData = Category::create([
                "category_name" => $request->category_name,
            ]);
            DB::commit();
            return response()->json([
                "message" => "the category created suceddfully",
                "data" => $categoryData,
                "status" => "true",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }

    }
    public function deleteCategory(Request $request)
    {
        $categoryId = $request->query('id');
        $categoryData = Category::find($categoryId);
        if (!$categoryData) {
            return response()->json([
                "message" => "category not found",
                "status" => "false",
            ], 404);
        }
        $categoryData->delete();

        return response()->json([
            "message" => "category delete successfully",
            "status" => "true",
        ], 200);



    }
    public function fetchAllCategory(Request $request)
    {

        try {
            $categories = Category::all();

            return response()->json([
                "message" => "fetch all category data",
                "data" => $categories,
                "status" => "true"

            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "status" => "false"
            ], 500);

        }

    }

    public function fetchByCategory(Request $request)
    {
        try {
            // $adminData=auth()->user();
            // $eventData=$adminData->events;
            $categoryId = $request->query("id");
            $allEvent = Event::where("category_id", $categoryId)->All();




            $searchId = $request->query("id");

        } catch (\Exception $e) {


        }
    }








}
