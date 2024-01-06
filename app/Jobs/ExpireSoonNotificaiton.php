<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireSoonNotificaiton implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $enrolls;

    public function __construct($enrolls)
    {
        $this->enrolls = $enrolls;
    }

    public function handle(): void
    {
        $enrolls = $this->enrolls;

        foreach ($enrolls as $enroll) {
            $user = $enroll->user;
            $course = $enroll->course;

            $data = [
                'course' => $course->title,
                'name' => $user->name,
                'date' => showDate($enroll->created_at),
                'link' => courseDetailsUrl(@$course->id, @$course->type, @$course->slug),

            ];


            if (UserEmailNotificationSetup('CourseExpireSoon', $user)) {
                SendGeneralEmail::dispatch($user, 'CourseExpireSoon', $data);
            }

            if (UserBrowserNotificationSetup('CourseExpireSoon', $user)) {
                send_browser_notification($user, 'CourseExpireSoon', $data,
                    trans('common.View'),//actionText
                    courseDetailsUrl(@$course->id, @$course->type, @$course->slug),
                );
            }

            if (UserMobileNotificationSetup('CourseExpireSoon', $user) && !empty($user->device_token)) {
                send_mobile_notification($user, 'CourseExpireSoon', $data, $course->title, $course->id, 'course');
            }

            $enroll->send_expire_notification = 1;
            $enroll->save();
        }
    }
}
