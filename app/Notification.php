<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'category_id', 'lat', 'lng', 'distance',
    ];

    /**
     * Compares the submitted item location with the notification location distances
     * and returns all those notification emails where the distance is lower than the
     * max. distance specified in the notification
     *
     * @param int $latitude Latitude of the submitted item location
     * @param int $longitude Longitude of the sumbitted item location
     * @param int $category Category of the item
     * @return object|void
     */
    public static function nearbyItemNotificationRequestEmails($latitude = 0, $longitude = 0, $category = 0)
    {
        $notifications = Notification::select('email', 'category_id')
            ->whereRaw('distance > (6371 * acos(cos(radians('.$latitude.')) * cos(radians(`lat`)) * cos(radians(`lng`) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin(radians(`lat`))))')->get();

        $notificationArray = [];
        if(count($notifications) > 0) {
            foreach($notifications as $notification) {
                if($notification->category_id === 0 || $notification->category_id === '0') {
                    $notificationArray[] = $notification->email;
                } elseif ($notification->category_id === $category) {
                    $notificationArray[] = $notification->email;
                }
            }
            return $notificationArray;
        }
    }

    /**
     * Sends a notification email about a lost/found item to the specified recipient
     *
     * @param array $emailArray Recipient email array
     * @param string $type Type of item: lost or found
     * @param int $itemId The item's ID
     * @param string $itemTitle The item's title
     * @param string $itemDescription The item's description
     * @return \Illuminate\Support\Facades\Mail
     */
    public static function sendNotificationEmails($emailArray = '', $type = '', $itemId = 0, $itemTitle = '', $itemDescription = '')
    {
        if(count($emailArray) > 0) {
            foreach($emailArray as $email) {
                if($type === 'found') {
                    $type = __('Found');
                } else {
                    $type = __('Lost');
                }
                $itemLink = \URL::to('items').'/'.$itemId;
                Mail::send('emails.item-notification', [
                    'item_link'         => $itemLink,
                    'item_description'  => $itemDescription,
                    'item_title'        => $itemTitle,
                    'type'              => $type,
                ], function ($message) use ($email, $type) {
                    $message->from(\Config::get('site.notification_email_from'), __('A new item has been :type in your area', ['type' => $type]));
                    $message->to($email);
                });
            }
        }
    }

}
