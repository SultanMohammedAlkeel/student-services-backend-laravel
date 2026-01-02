<?php

namespace App\Http\Controllers;

use App\Models\HallBooking;
use App\Models\Notification;
use App\Models\NotificationReply;
use App\Models\NotificationResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $notification = new Notification();
        $notification->teacher_id = $request->teacher_id;
        $notification->sender_id = $request->sender_id;
        $notification->department_id = $request->department_id;
        $notification->level = $request->level;
        $notification->title = $request->title;
        $notification->content = $request->content;
        $notification->course_id = $request->course_id;
        $notification->hall_id = $request->hall_id;
        $notification->schedule_id = $request->schedule_id;
        $notification->period = $request->period;
        $notification->save();
        if ($request->teacher_reply) {
            $notification_responses = new NotificationReply();
            $notification_responses->notification_id = $notification->id;
            $notification_responses->confirmation = $request->confirmation;
            $notification_responses->content = $request->content;
            $notification_responses->save();

            $notification = Notification::find($notification->id);
            $notification->is_delivered = 1;
            $notification->save();

            $hall_booking = new HallBooking();
            $hall_booking->hall_id = $request->hall_id;
            $hall_booking->course_id = $request->course_id;
            $hall_booking->period = $request->period;
            $hall_booking->teacher_id = $request->teacher_id;
            $hall_booking->schedule_id = $request->schedule_id;
            $hall_booking->department_id = $request->department_id;
            $hall_booking->level = $request->level;
            $hall_booking->save();
        }

        return redirect()->back();
    }

    function NotificationResponses(Request $request) 
    {
        $notification_responses = new NotificationReply();
        $notification_responses->notification_id = $request->notification_id;
        $notification_responses->confirmation = $request->confirmation;
        $notification_responses->content = $request->content;
        $notification_responses->save();

        $notification = Notification::find($request->notification_id);
        $notification->is_delivered = 1;
        $notification->save();

        $hall_booking = new HallBooking();
        $hall_booking->hall_id = $request->hall_id;
        $hall_booking->course_id = $request->course_id;
        $hall_booking->period = $request->period;
        $hall_booking->teacher_id = $request->teacher_id;
        $hall_booking->schedule_id = $request->schedule_id;
        $hall_booking->department_id = $notification->department_id;
        $hall_booking->level = $notification->level;
        $hall_booking->save();
        
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
