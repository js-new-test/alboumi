<?php
/**
 * Created by PhpStorm.
 * User: Chanda Motiyani
 * Date: 3/13/2015
 * Time: 5:07 PM
 */
namespace App\DB\Traits;

use App\DB\User\User;
use Illuminate\Support\Facades\Auth;

trait NotifierIdsTrait
{
    /**
     * This is will return the array of ids
     */
    public static function intended_notifiers()
    {
        $user = new User();
        // If administrator is adding the product then seller should be notified for the product
        $adminIds = $user->getUserByRole('admin', ['id']);
        return $adminIds;
    }

    public static function admin_email_notifiers()
    {
        $user = new User();
        $adminIds = '';
        if (Auth::admin()->check() || Auth::seller()->check()) {
            $adminIds = $user->getUserByRole('admin', ['email']);
        }
        return $adminIds;
    }
} 